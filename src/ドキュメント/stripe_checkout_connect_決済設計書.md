# Cashier 組み込み後の正式版設計書
## エスクロー型 + 買い切り即時送金型 両対応・テスト設計補強版・完全版
## 0. この設計書の目的

この設計書は、スキル販売型マーケットプレイスにおいて、購入者が Stripe Checkout で支払いを行い、その後の状態管理・取引進行・販売者送金・テスト確認までを安全に運用できる状態を作るための正式な設計書である。

この設計書が扱う範囲は、単なる決済実装だけではない。次をすべて含む。

① 購入開始から支払い確定までの流れ
② 支払い確定後の注文状態管理
③ エスクロー型の取引進行と完了時送金
④ 買い切り即時送金型の即時送金
⑤ Webhook を正とした整合性設計
⑥ 二重更新防止
⑦ 二重送金防止
⑧ 失敗時の状態管理
⑨ テスト設計
⑩ ログ設計
⑪ 本番公開前の確認基準

この設計書の完成条件は、実装できることに加えて、本番前に十分なテストができること、そして異常系でも状態が壊れないことである。Stripe は Webhook によるイベント通知、Separate charges and transfers、テスト環境、Webhook endpoint の設定をそれぞれ公式に案内しており、今回の設計はそれらを前提に組み立てる。

## 1. 実現したい販売モデル

本システムでは、1つの注文基盤で次の2つの販売モデルを扱う。

### 1.1 エスクロー型

① 購入者が支払いを完了した時点では、代金は運営側で保持する。
② その後、購入者と販売者が取引を進め、購入者が 取引完了 を押したタイミングで、運営手数料を差し引いた金額を販売者へ送金する。

・このモデルは、納品確認や評価を挟みたい商品・サービスに向いている。

### 1.2 買い切り即時送金型

・購入者が支払いを完了したら、取引完了の合図を待たずに、運営手数料を差し引いた金額を販売者へ送金する。

・このモデルは、コンテンツ販売、ダウンロード商品、即時受け渡し可能な商品などに向いている。

### 1.3 なぜ同じ設計で両方扱うのか

① 2つのモデルの違いは、本質的には送金タイミングの違いである。
② 支払い自体はどちらも Stripe Checkout で行い、まずプラットフォーム側で受け、その後の transfer 実行タイミングだけを分ければよい。

・Stripe Connect の separate charges and transfers は、支払いと送金を分ける方式であり、購入時点では送金先が確定していない場合や、複数送金先があり得る場合にも使える。今回のように「即時送金」と「後日送金」を同じ基盤で扱う考え方と相性が良い。

## 2. 基本方針
### 2.1 Cashier を使う範囲

Laravel Cashier は、Stripe の定番連携を Laravel に寄せて扱いやすくするために使う。主な利用範囲は次のとおり。

① 購入者モデルへの Billable 適用
② Stripe Checkout の開始
③ Webhook 署名検証の土台
④ Stripe 連携の定番構成の簡略化

Cashier は Stripe の請求機能を扱う Laravel の公式ライブラリであり、Webhook secret を設定した署名検証も含めて利用できる。

### 2.2 Cashier だけで完結しない範囲

今回の要件はサブスクではなく、マーケットプレイスの注文管理と送金管理である。
そのため、以下は自前の業務ロジックとして持つ必要がある。

① 仮注文作成
② 注文状態管理
③ 取引状態管理
④ 送金状態管理
⑤ 納品
⑥ 完了処理
⑦ 即時送金分岐
⑧ Connect transfer 実行
⑨ 失敗時の状態保存
⑩ 二重送金防止

つまり、Cashier は Checkout と Webhook の足場であり、注文業務そのものを置き換えるものではない。

### 2.3 Stripe Connect の方式

本設計では、Stripe Connect の separate charges and transfers を採用する。
これは、まずプラットフォーム側で支払いを受け、その後に connected account へ transfer を行う方式である。Stripe はこの方式について、プラットフォームのアカウントに charge が作成され、connected account への transfer は後から実行できること、Stripe 手数料・返金・チャージバックはプラットフォーム残高に影響することを説明している。

## 3. 業務フロー
### 3.1 共通の購入開始フロー
① 購入者が商品詳細画面で 購入する を押す
② Laravel 側で仮注文を作成する
③ Cashier を使って Checkout Session を作成する
④ 購入者を Stripe Checkout へ遷移させる
⑤ 購入者が支払いを完了する
⑥ Stripe から Webhook が届く
⑦ Webhook を正として注文を paid に更新する
### 3.2 エスクロー型のフロー
① transaction_status = in_progress にする
② 購入者と販売者が取引を進める
③ 販売者が納品する
④ 購入者が評価し、取引完了 を押す
⑤ その時点で 90% を販売者へ transfer する
⑥ 注文を completed にする
### 3.3 買い切り即時送金型のフロー
① Webhook で paid に更新した直後に送金条件を確認する
② payment_type = instant の場合、その場で 90% を販売者へ transfer する
③ 注文を業務上の完了扱いにするか、簡略状態で保持するかは要件に応じて制御する
### 3.4 なぜ Webhook を正とするのか

・Stripe は Webhook endpoint に HTTPS POST でイベントを送信し、リアルタイムに Stripe 上の状態変化を通知する。
・success_url はユーザーを戻すための画面遷移であり、アプリケーション内部で「本当に成功した」と確定するための唯一の情報源には向かない。
・そのため本設計では、支払い成功の最終確定は checkout.session.completed を受けた Webhook とする。

## 4. 支払いタイプ設計
### 4.1 新たに持つべき概念

・この完全版で重要なのは、注文に 支払いタイプ を持たせることである。

追加項目の考え方:

① payment_type = escrow
② payment_type = instant
### 4.2 役割
① escrow
② 支払い後も送金せず、完了時に送金する
③ instant
④ 支払い確定後、直ちに送金する
### 4.3 この設計の利点

・この方式にすると、テーブルやシステムを分けずに、同じ注文基盤で 2 種類の販売方式を管理できる。
・分岐点は主に 送金タイミング であり、決済そのものは共通化できる。

## 5. 役割分担
### 5.1 コントローラー
#### 5.1.1 SkillOrderController

・役割は購入開始の受付。

担当内容

① ログイン確認
② 自己購入防止
③ 商品取得
④ 仮注文作成の呼び出し
⑤ Checkout 開始の呼び出し
⑥ Stripe Checkout へのリダイレクト
#### 5.1.2 StripeWebhookController または Cashier webhook listener

・役割は Stripe から届いたイベントを受け、注文と送金の初動反映を行うこと。

担当内容

① Webhook 署名検証
② event type 判定
③ checkout.session.completed の処理
④ 注文の paid 反映
⑤ payment_type に応じた分岐
⑥ 必要であれば即時送金の起動
⑦ ログ記録
⑧ 冪等性確保
#### 5.1.3 SkillTransactionController

・役割はエスクロー型の取引進行管理。

担当内容

① 取引チャット画面表示
② 納品
③ 完了
④ レビュー保存
⑤ 完了時送金の呼び出し
### 5.2 サービスクラス
#### 5.2.1 SkillOrderService

・役割は仮注文の作成と初期状態保存。

#### 5.2.2 StripeCheckoutService

・役割は Cashier を使った Checkout Session の開始。

#### 5.2.3 StripeWebhookService

・役割は Webhook payload を解析し、自社の注文状態へ反映すること。

#### 5.2.4 PayoutService

・役割は販売者送金の集約。

担当内容

① 手数料計算
② 送金額計算
③ Stripe Connect transfer 実行
④ 二重送金防止
⑤ 成功・失敗状態保存
#### 5.2.5 OrderCompletionService（任意だが推奨）

・役割は「エスクロー型の完了」と「即時型の自動完了扱い」の差分を吸収すること。

## 6. 画面・メソッドごとの処理フロー
### 6.1 resources/views/skills/show.blade.php
役割

・購入導線の入口。

処理内容
① 商品情報表示
② 購入する ボタン表示
③ 商品に応じて payment_type を hidden で渡すか、商品側設定から決定する
④ ボタン押下で skills.purchase に POST
説明

・ここでは決済処理自体は行わず、Laravel 側の購入開始へ渡す。

### 6.2 SkillOrderController@store
役割

・購入開始の受付。

処理内容
① 購入者ログイン確認
② 商品取得
③ 購入者が販売者本人でないことを確認
④ 商品に紐づく payment_type を決定
⑤ SkillOrderService@createPendingOrder を呼ぶ
⑥ StripeCheckoutService@createCheckoutSession を呼ぶ
⑦ Checkout URL へリダイレクトする
説明

・この時点ではまだ paid にしない。
・あくまで仮注文と Checkout 開始までである。

### 6.3 SkillOrderService@createPendingOrder
役割

・仮注文の作成。

処理内容
① 注文レコード作成
② 購入時点の価格を amount に固定
③ status = pending
④ transaction_status = waiting_payment
⑤ payout_status = not_transferred
⑥ payment_type を保存
⑦ purchased_at を保存
⑧ 必要に応じて取引開始の下書き情報を作る
説明

・この段階では「購入希望の記録」を残すだけであり、支払い成功ではない。

### 6.4 StripeCheckoutService@createCheckoutSession
役割

・Cashier を使って Checkout Session を開始する。

処理内容
① Billable な購入者モデルを取得
② 金額を Stripe 用に準備
③ Cashier の checkout() を利用して Session を作成
④ success_url を設定
⑤ cancel_url を設定
⑥ metadata.order_id を設定
⑦ 必要に応じて client_reference_id を設定
⑧ Session 情報を注文へ保存
⑨ Checkout URL を返す
説明

・Stripe の Checkout Session には success_url、cancel_url、metadata などを持たせられる。metadata で自社注文と結びつける設計は重要である。

### 6.5 success_url 到達時
役割

・ユーザー向け画面遷移。

処理内容
① 「支払いを確認中」などの表示
② Webhook 未反映なら確定表示にしない
③ 必要に応じて注文詳細や取引画面へ誘導
説明

・ここでは DB を最終確定しない。

### 6.6 cancel_url 到達時
役割

・キャンセル時の戻り先。

処理内容
① 「支払いは完了していません」と表示
② 注文は pending のまま
③ checkout_cancelled_at を保存してもよい
④ 再購入導線を用意する
### 6.7 Webhook 受信
対象イベント
・checkout.session.completed

・Stripe は Webhook を通じてイベントを送信し、Webhook endpoint はダッシュボードまたは API から設定できる。 checkout.session.completed は Checkout 正常完了の主要イベントである。

StripeWebhookService@handleCheckoutCompleted
役割

・支払い成功の正式反映。

処理内容
① Webhook 署名検証
② event id を取得
③ event type を取得
④ checkout.session.completed であることを確認
⑤ 同じ event id を処理済みでないか確認
⑥ metadata.order_id で対象注文取得
⑦ 注文がすでに paid でないことを確認
⑧ status = paid
⑨ paid_at 保存
⑩ stripe_checkout_session_id 保存
⑪ stripe_payment_intent_id 保存
⑫ stripe_webhook_event_id 保存
⑬ last_webhook_type 保存
⑭ last_webhook_received_at 保存
⑮ payment_type に応じて分岐する
⑯ 分岐A: payment_type = escrow
⑰ transaction_status = in_progress
⑱ 取引進行待ちにする
⑲ ログ記録
⑳ 分岐B: payment_type = instant
㉑ 即時送金条件を確認
㉒ PayoutService@transferToSeller を呼ぶ
㉓ 必要に応じて transaction_status = completed または fulfilled 相当の簡略状態へ進める
㉔ completed_at を保存するか、別の即時完了時刻を保存
㉕ ログ記録
説明

・Webhook 再送はあり得るため、event id 単位で冪等性が必要である。
・また、即時送金は Webhook を正とした後にのみ動かす。

### 6.8 SkillTransactionController@show
役割

・エスクロー型の取引チャット画面表示。

処理内容
① ログイン確認
② 購入者または販売者であること確認
③ 注文・メッセージ・状態読込
④ payment_type = escrow の場合のみ納品 / 完了の導線を表示
⑤ payment_type = instant の場合は簡略表示または購入履歴表示へ切り替える
### 6.9 SkillTransactionController@deliver
役割

・販売者の納品記録。エスクロー型のみで使う。

処理内容
① 販売者本人確認
② status = paid 確認
③ payment_type = escrow 確認
④ transaction_status = in_progress 確認
⑤ transaction_status = delivered
⑥ delivered_at 保存
⑦ システムメッセージ保存
ログ記録
### 6.10 SkillTransactionController@complete
役割

・購入者による正式完了。エスクロー型のみで使う。

処理内容
① 購入者本人確認
② payment_type = escrow 確認
③ transaction_status = delivered 確認
④ レビューと評価保存
⑤ PayoutService@transferToSeller 呼び出し
⑥ 送金成功時に transaction_status = completed
⑦ completed_at 保存
⑧ システムメッセージ保存
⑨ ログ記録
### 6.11 PayoutService@transferToSeller
役割

・販売者への送金を一元管理する。

処理内容
① payout_status = transferred でないこと確認
② stripe_transfer_id が未保存であること確認
③ 注文金額から 10% を手数料として計算
④ 90% を販売者取り分として計算
⑤ connected account id を取得
⑥ Stripe Connect の transfer を実行
⑦ stripe_transfer_id 保存
⑧ transferred_at 保存
⑨ payout_status = transferred
⑩ transfer_attempts 加算
⑪ 成功ログ記録
⑫ 失敗時
⑬ payout_status = failed
⑭ last_transfer_error 保存
⑮ transfer_attempts 加算
⑯ 失敗ログ記録
説明

・Separate charges and transfers では、支払いはプラットフォーム側に作成され、transfer は後から connected account に対して行う。即時型でもエスクロー型でも、送金実体はこの共通サービスで扱う。

## 7. DB 設計
### 7.1 既存前提項目
① amount
② status
③ purchased_at
④ transaction_status
⑤ delivered_at
⑥ completed_at

### 7.2 追加項目
① 支払い追跡用
② stripe_checkout_session_id
③ stripe_payment_intent_id
④ stripe_charge_id
⑤ Webhook 追跡用
⑥ stripe_webhook_event_id
⑦ last_webhook_type
⑧ last_webhook_received_at
⑨ 送金追跡用
⑩ stripe_transfer_id
⑪ transferred_at
⑫ payout_status
⑬ transfer_attempts
⑭ last_transfer_error
⑮ 支払いタイプ用
⑯ payment_type
⑰ 時刻追跡用
⑱ paid_at
⑲ checkout_cancelled_at
⑳ checkout_expires_at（必要なら）
### 7.3 payment_type の値
① escrow
② instant
### 7.4 各項目の意味
① payment_type は送金タイミングの分岐に使う。
② stripe_webhook_event_id は同じイベント再送時の重複処理防止に使う。
③ stripe_transfer_id と payout_status は二重送金防止に使う。
④ last_transfer_error は運用上の再送判断に使う。

## 8. ステータス設計
### 8.1 決済状態
① pending
② paid
③ cancelled
④ refunded（将来拡張）
### 8.2 取引状態
① waiting_payment
② in_progress
③ delivered
④ completed
### 8.3 送金状態
① not_transferred
② transferred
③ failed
### 8.4 2つの販売モデルでの使い方
① エスクロー型
② 購入直後: pending / waiting_payment / not_transferred
③ Webhook 後: paid / in_progress / not_transferred
④ 納品後: paid / delivered / not_transferred
⑤ 完了後: paid / completed / transferred
⑥ 即時送金型
⑦ 購入直後: pending / waiting_payment / not_transferred
⑧ Webhook 後送金成功: paid / completed / transferred
⑨ もしくは
⑩ Webhook 後送金成功: paid / in_progress / transferred
⑪ とし、商品特性に応じて後続処理を持たせてもよい
### 8.5 分離する理由
・支払い成功、業務完了、送金完了は別々の事象であり、同じカラムにまとめると事故原因になる。
・特に即時型では「支払い」と「送金」は近いタイミングで起こるが、それでも別管理が安全である。

## 9. Webhook 設計
### 9.1 基本方針
① Webhook を正とする
② 署名検証する
③ event id を保存する
④ 冪等に処理する
⑤ Stripe ダッシュボードで確認できる状態にする

Laravel Cashier では webhook secret を設定し、受信時の妥当性検証に使う構成が案内されている。Stripe でも Webhook endpoint を登録してイベントを受信する構成を前提としている。

### 9.2 保存すべきもの
① event id
② event type
③ order id
④ session id
⑤ payment intent id
⑥ 受信時刻
⑦ 処理結果
### 9.3 受信失敗への備え
① Stripe ダッシュボードで event を確認
② アプリログで失敗原因確認
③ 再送時に二重更新しない
④ 必要に応じて手動再処理できるようにする
## 10. ログ設計
### 10.1 Checkout 開始ログ

記録内容

① user_id
② order_id
③ amount
④ product_id
⑤ payment_type
⑥ session 作成結果
### 10.2 Webhook 受信ログ

記録内容

① event id
② event type
③ order_id
④ payment_type
⑤ session id
⑥ payment intent id
⑦ 処理結果
### 10.3 納品ログ

記録内容

① order_id
② seller_id
③ delivered_at
### 10.4 完了ログ

記録内容

① order_id
② buyer_id
③ completed_at
④ review id
### 10.5 Transfer ログ

記録内容

① order_id
② seller_id
③ payment_type
④ transfer amount
⑤ stripe_transfer_id
⑥ payout_status
⑦ error 内容
### 10.6 ログの目的
① テスト時の照合
② 本番障害の切り分け
③ 二重送金調査
④ Stripe ダッシュボードとの照合
## 11. 異常系設計
### 11.1 Checkout キャンセル

期待動作

① status = pending
② paid にしない
③ transaction_status = waiting_payment
④ payout_status = not_transferred
⑤ checkout_cancelled_at 保存してもよい
### 11.2 支払い失敗

期待動作

① paid にしない
② 再購入可能
③ ログに残す
### 11.3 Webhook 未着

期待動作

① success_url だけで paid にしない
② Stripe ダッシュボードで event 確認
③ 未反映調査できるログを残す
### 11.4 Webhook 再送

期待動作

① 同じ event id なら再処理しない
② 二重更新しない
③ 即時型でも二重送金しない
### 11.5 完了ボタン二重押し

期待動作

① エスクロー型で transfer が1回だけ
② stripe_transfer_id が1件のみ
③ payout_status が壊れない
### 11.6 即時送金型での送金失敗

期待動作

① status = paid は維持
② payout_status = failed
③ last_transfer_error 保存
④ 再送金対象として運用で把握可能
### 11.7 connected account 未設定

期待動作

① 支払いまでは成功しても送金を行わない
② payout_status = failed
③ 管理者確認対象にする
## 12. テスト設計
### 12.1 テスト設計の目的

本番公開前に確認すべきことは次の3つである。

① 正常に支払いと送金が流れるか
② 失敗時に状態が壊れないか
③ 同じ通知や同じ操作が重なっても二重処理しないか

Stripe は test mode と Sandboxes を提供しており、実際の請求や資金移動なしで統合テストを行える。Stripe は Sandboxes を、より分離された包括テスト用途として案内している。

### 12.2 テスト環境

使用するもの

① Stripe test mode または Sandbox
② test API keys
③ test webhook secret
④ テスト用 connected account
⑤ ステージング環境またはローカル環境
⑥ 必要なら Stripe CLI
⑦ テストカード
### 12.3 確認対象
アプリ側
① 画面遷移
② DB状態
③ ログ
④ ボタン表示制御
Stripe 側
① Checkout Session
② PaymentIntent
③ Webhook event
④ Transfer
ユーザー体験
① 購入導線
② キャンセル導線
③ エスクロー完了導線
④ 即時完了導線
### 12.4 正常系テスト: エスクロー型

シナリオ

① 購入者でログイン
② エスクロー型商品を購入
③ Checkout へ遷移
④ テストカードで支払い成功
⑤ Webhook 受信
⑥ 注文が paid
⑦ transaction_status = in_progress
⑧ 販売者が納品
⑨ 購入者が評価して完了
⑩ transfer 成功
⑪ payout_status = transferred
⑫ transaction_status = completed

確認ポイント

① Checkout 遷移成功
② Stripe ダッシュボードに決済あり
③ Webhook 到達
④ DB 更新正しい
⑤ transfer 金額が 90%
⑥ stripe_transfer_id 保存
### 12.5 正常系テスト: 即時送金型

シナリオ

① 購入者でログイン
② 即時型商品を購入
③ Checkout へ遷移
④ テストカードで支払い成功
⑤ Webhook 受信
⑥ 注文が paid
⑦ 直ちに transfer 成功
⑧ payout_status = transferred
⑨ transaction_status が設計どおりの即時完了系状態になる

確認ポイント

① 支払い後に transfer が自動で走る
② transfer 金額が 90%
③ 手動完了操作なしで処理が閉じる
④ 二重送金しない
### 12.6 Checkout キャンセルテスト

シナリオ

① 購入する
② Checkout に進む
③ 支払わず戻る

確認ポイント

① status = pending
② paid にならない
③ payout_status = not_transferred
④ 即時型でも送金されない
### 12.7 Webhook 再送テスト

シナリオ

① 正常決済
② 同じ webhook event を再送

確認ポイント

① 注文状態が二重更新されない
② 即時型でも transfer が二重実行されない
③ ログで再送と判別できる

・Stripe は Webhook によるイベント受信を前提にしており、再送に耐える設計が必要である。

### 12.8 完了二重実行テスト

シナリオ

① エスクロー型を delivered にする
② 完了処理を2回発火

確認ポイント

① transfer が1回だけ
② stripe_transfer_id が1件
③ payout_status が壊れない
### 12.9 即時型送金失敗テスト

シナリオ

① 即時型購入
② Webhook は成功
③ transfer 失敗

確認ポイント

① status = paid
② payout_status = failed
③ last_transfer_error 保存
④ 運用上再送可能
### 12.10 本番前に問題なしと判断する条件

以下をすべて満たしたとき、本番前確認として十分と判断する。

① エスクロー型正常系が通る
② 即時型正常系が通る
③ Checkout キャンセルで状態が壊れない
④ Webhook 再送で二重更新しない
⑤ 即時型で二重送金しない
⑥ エスクロー型で完了二重押しでも二重送金しない
⑦ transfer 金額が 90% で正しい
⑧ Stripe ダッシュボードと DB が一致する
⑨ ログに未解決エラーが残っていない
## 13. Antigravity などブラウザ自動確認の位置づけ
### 13.1 有効な点

ブラウザ自動操作は次に有効である。

① 購入導線確認
② ボタン表示確認
③ Checkout 遷移確認
④ success / cancel の画面導線確認
⑤ エスクロー型と即時型の画面差分確認
### 13.2 足りない点

・ブラウザ操作だけでは次は十分確認できない。

① DB状態
② Webhook 冪等性
③ Stripe ダッシュボードの event 確認
④ transfer の内部状態
⑤ 二重送金防止
### 13.3 この設計での位置づけ
① Antigravity
② 画面導線の通し確認担当
③ 開発者確認
④ DB・ログ・Stripe ダッシュボード・Webhook・Transfer 確認担当

・つまり、Antigravity は有効だが、最終安全確認の代替ではない。

## 14. .env と運用設定

必要な代表設定

① STRIPE_KEY
② STRIPE_SECRET
③ STRIPE_WEBHOOK_SECRET

・Laravel Cashier の webhook 検証では webhook secret 設定が重要である。Stripe 側では test mode と live mode が分かれており、キーの切り替えが必要である。

本番切替前確認

① test key のままではないか
② webhook secret が本番用か
③ success_url / cancel_url が本番ドメインか
④ connected account が本番用か
## 15. 実装時の注意点
### 15.1 購入者モデルに Billable を付与する

・Cashier を前提にするなら必要。

### 15.2 metadata.order_id を必ず持たせる

・Webhook で注文へ戻るための最短導線になる。

### 15.3 金額は注文時点で固定する

・あとで商品価格が変わっても注文金額は変えない。

### 15.4 DB トランザクションを使う
仮注文作成
① Webhook 反映
② エスクロー型完了 + transfer
③ 即時型 transfer 実行
### 15.5 即時型では送金を Webhook 成功後にのみ行う

・success_url 到達時には実行しない。

### 15.6 二重送金防止を最優先にする
① stripe_transfer_id があれば再送しない
② payout_status = transferred なら再送しない
③ event id で Webhook 再送を防ぐ
## 16. この設計の一言まとめ

購入時は Laravel Cashier で Stripe Checkout を開始し、支払い確定は Webhook を正として反映する。注文には payment_type を持たせ、escrow なら取引完了時に、instant なら支払い確定直後に、Stripe Connect の separate charges and transfers で販売者へ 90% を送金する。アプリ側では決済状態・取引状態・送金状態・Webhook 識別子を分けて保持し、二重更新と二重送金を防ぎながら、本番前テストまで完結できる構成を完成形とする。