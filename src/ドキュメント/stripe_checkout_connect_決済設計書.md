# Stripe Checkout + Stripe Connect 決済設計書

この資料は、`@src/resources/views/skills/show.blade.php` の「購入する」ボタンを起点に、Stripe Checkout で実際に決済し、取引完了時に Stripe Connect で販売者へ売上を送金するまでの流れを、メソッド単位で整理したものです。

## 1. まず実現したい流れ

1. 購入者が `購入する` をクリックす

、Stripe Checkout の決済画面へ遷移する。
3. 購入者が Stripe の画面でクレジットカード情報を入力し、支払いを確定する。
4. 支払いが成功したら、注文は「支払い済み・取引中」になる。
5. 取引チャット画面で購入者と販売者がやり取りする。
6. 販売者が納品し、購入者が星評価を付けて `取引を完了` を押す。
7. 取引完了時に、運営手数料 10% を差し引いた 90% を Stripe Connect 経由で販売者へ送金する。
8. 最後に注文は正式な完了状態になる。

## 2. いまの実装との関係

現状のコードでは、`SkillOrderController@store` が `SkillOrderService@purchase` を呼び、`skill_orders` に `pending` の注文を作ってから取引チャットへ遷移しています。

現在の主な流れは次の通りです。

- `src/resources/views/skills/show.blade.php`
  - `購入する` ボタンが `skills.purchase` に POST している。
- `App\Http\Controllers\SkillOrderController@store`
  - ログイン確認、簡易バリデーション、購入処理の呼び出しを行う。
- `App\Services\SkillOrderService@purchase`
  - `skill_orders` を作成し、取引開始メッセージを入れる。
- `App\Http\Controllers\SkillTransactionController`
  - 取引チャット、納品、完了、レビューを扱う。

Stripe を入れる場合は、この流れの中に「Checkout セッション生成」と「Webhook での支払い確定確認」と「Connect 送金」を追加します。

## 3. 役割分担の考え方

### 3.1 コントローラー

コントローラーは「画面遷移」「当事者チェック」「入力の最低限の確認」に寄せます。

- `SkillOrderController`
  - 購入開始
  - Stripe Checkout へのリダイレクト
- `StripeWebhookController`
  - Stripe からのイベント受信
  - 支払い成功、失敗、セッション期限切れの反映
- `SkillTransactionController`
  - 取引チャット表示
  - 納品
  - 取引完了
  - レビュー保存

### 3.2 サービスクラス

サービスクラスは「Stripe API を叩く処理」「金額計算」「送金処理」をまとめます。

- `SkillOrderService`
  - 注文レコード作成
  - 注文の初期状態設定
- `StripeCheckoutService`
  - Stripe Checkout Session 作成
  - success / cancel URL の組み立て
  - `payment_intent_id` や `checkout_session_id` の保存
- `StripeWebhookService`
  - Webhook イベントの正当性確認
  - 決済結果を DB に反映
- `PayoutService`
  - 販売者への送金額を計算
  - 90% 送金処理
  - 送金結果を保存

## 4. メソッドごとの処理フロー

### 4.1 `skills/show.blade.php`

**役割**
- 購入ボタンを表示する画面。

**処理内容**
- 購入者が `購入する` を押すと `skills.purchase` に POST する。
- ここでは決済処理そのものは行わず、Laravel の購入開始処理へ渡す。

**平易な説明**
- この画面は「購入の入口」です。
- 実際にお金を受け取るのはこの画面ではなく、次のコントローラーです。

### 4.2 `SkillOrderController@store`

**役割**
- 購入開始の窓口。

**やること**
1. ログインしているか確認する。
2. 出品本人が自分のスキルを買おうとしていないか確認する。
3. `SkillOrderService` で注文の下書きを作る。
4. `StripeCheckoutService` を呼んで Checkout Session を作る。
5. Stripe の決済画面へリダイレクトする。

**平易な説明**
- ここは「購入ボタンが押された直後の受付係」です。
- まず Laravel 側で注文の土台を作り、そのあと Stripe の支払い画面へ送ります。

**Stripe 対応後のイメージ**
- `purchase()` ではなく `startCheckout()` のような処理を呼ぶ。
- 返ってきた `checkout_session_url` に対して `redirect()->away()` で飛ばす。

### 4.3 `SkillOrderService@purchase` または `createPendingOrder`

**役割**
- 注文の記録を作る。

**やること**
1. `skill_orders` に注文レコードを作る。
2. 出品時点の金額を `amount` に保存する。
3. 注文の初期状態を `pending` にする。
4. `purchased_at` を入れる。
5. 取引開始メッセージを作る。

**平易な説明**
- ここは「注文の控えを残す」処理です。
- 決済前でも、誰が何をいくらで買おうとしたかを記録します。

**Stripe 対応後の考え方**
- この段階ではまだ「支払い完了」とはしない。
- まずは `checkout_session_id` を保存して、Stripe 側の支払い結果を Webhook で確定させる。

### 4.4 `StripeCheckoutService@createSession`

**役割**
- Stripe の決済画面を作る。

**やること**
1. 注文金額を Stripe 用の金額形式に変換する。
2. Checkout Session を作成する。
3. `success_url` と `cancel_url` を設定する。
4. セッション ID を注文に保存する。
5. 生成された URL をコントローラーへ返す。

**平易な説明**
- ここは「Stripe のレジ画面を発行する処理」です。
- Laravel は金額を決めるだけで、カード情報の入力は Stripe に任せます。

**補足**
- カード情報は自前で保持しない。
- 3D セキュアや本人認証は Stripe に任せる。

### 4.5 `StripeWebhookController@handle`

**役割**
- Stripe からの通知を受けて、支払い成功を正式に確定する。

**やること**
1. Stripe 署名を検証する。
2. `checkout.session.completed` を受け取る。
3. 対象の `skill_order` を探す。
4. 注文の支払い状態を `paid` にする。
5. `transaction_status` を `in_progress` にする。
6. `payment_intent_id`、`stripe_checkout_session_id` などを保存する。
7. 取引開始メッセージを確定済みにする。

**平易な説明**
- ブラウザの戻り先よりも、Stripe からの正式通知を信じるのが大事です。
- これで「本当に決済が成功した」と判定します。

**なぜ Webhook が必要か**
- ブラウザは途中で閉じられることがある。
- success 画面に来ても支払い失敗のケースを避ける必要がある。
- だから最終確定は Stripe からの通知で行う。

### 4.6 `SkillTransactionController@show`

**役割**
- 取引チャット画面を表示する。

**やること**
1. ログイン確認。
2. 購入者か販売者かを確認する。
3. 注文、メッセージ、支払い状態、取引状態を読み込む。
4. 画面に表示する。

**平易な説明**
- ここは「取引の進行表」を見せる画面です。
- 決済完了後は、ここから納品・評価・完了まで進めます。

### 4.7 `SkillTransactionController@deliver`

**役割**
- 販売者が納品したことを記録する。

**やること**
1. 販売者本人か確認する。
2. 取引ステータスが `in_progress` か確認する。
3. `transaction_status` を `delivered` にする。
4. `delivered_at` を保存する。
5. システムメッセージで「納品しました」を残す。

**平易な説明**
- ここは「販売者側の作業完了ボタン」です。
- 納品しただけでは売上はまだ確定しません。

### 4.8 `SkillTransactionController@complete`

**役割**
- 購入者の承認と評価を受けて、取引を正式完了にする。

**やること**
1. 購入者本人か確認する。
2. `transaction_status` が `delivered` か確認する。
3. 評価とコメントを保存する。
4. レビュー平均とレビュー数を更新する。
5. `transaction_status` を `completed` にする。
6. `completed_at` を保存する。
7. システムメッセージで完了通知を残す。
8. `PayoutService` を呼んで販売者へ 90% を送金する。

**平易な説明**
- ここが「正式に取引が終わるボタン」です。
- 評価を付けたあとに、売上の振り分けまで進めます。

### 4.9 `PayoutService@transferToSeller`

**役割**
- 運営手数料を差し引いた金額を販売者へ送る。

**やること**
1. 注文金額の 10% を運営手数料として計算する。
2. 90% を販売者の Stripe Connect 口座へ送金する。
3. 送金結果を `skill_orders` に保存する。
4. 送金成功後に送金済みフラグを立てる。

**平易な説明**
- ここは「お金を分ける処理」です。
- 購入時に入った全額を運営が受け取り、完了時に販売者へ 90% を送ります。

**重要な考え方**
- Stripe 手数料を誰が負担するかは、先に決めておく必要がある。
- この設計では「販売者の取り分は 90%」としているため、Stripe 手数料は別途運営負担にするか、別の会計ルールを決める必要がある。

## 5. DB で持っておきたい項目

Stripe 連携では、注文に Stripe の識別子を保存しておくと管理しやすくなります。

### 5.1 既存の項目
- `amount`
- `status`
- `purchased_at`
- `transaction_status`
- `delivered_at`
- `completed_at`

### 5.2 追加を検討したい項目
- `stripe_checkout_session_id`
- `stripe_payment_intent_id`
- `stripe_charge_id`
- `stripe_transfer_id`
- `paid_at`
- `transferred_at`
- `payout_status`

### 5.3 追加理由
- どの Stripe 支払いに対応する注文かを追跡するため。
- 同じ Webhook が複数回届いても重複処理しないため。
- 送金済みかどうかを後で確認するため。

## 6. ステータスの考え方

### 6.1 決済状態
- `pending` : 注文作成直後、まだ支払い前
- `paid` : Stripe で支払い完了
- `cancelled` : 支払いキャンセルや失敗で中止

### 6.2 取引状態
- `in_progress` : 取引中
- `delivered` : 出品者が納品済み
- `completed` : 購入者が承認して完了

### 6.3 重要な分離
- 「支払いが終わった」ことと「取引が終わった」ことは別物です。
- 支払いは Checkout 完了で確定。
- 売上の最終送金は `completed` のときに実行。

## 7. 本番前にどうテストするか

### 7.1 まずは Stripe のテストモードで行う

本番の live key ではなく、Stripe の test key を使って確認します。

確認する項目は次の通りです。

- Checkout 画面に正しく遷移するか
- カード入力後に支払い成功になるか
- Webhook で `paid` に変わるか
- 取引チャットに遷移できるか
- 納品ボタンで `delivered` になるか
- 完了ボタンで `completed` になるか
- 送金処理が 90% で動くか

### 7.2 テスト用のカードを使う

Stripe が用意しているテストカードを使います。

代表例:
- `4242 4242 4242 4242`
  - もっとも基本的な成功用カード
- 有効期限
  - 未来日付を入れる
- CVC
  - 任意の 3 桁

### 7.3 Webhook を必ず確認する

Checkout の画面だけでは不十分です。

必ず次を確認します。

- `checkout.session.completed` が受信できるか
- Webhook から DB 更新ができるか
- 同じイベントが複数回来ても二重処理しないか

### 7.4 Connect のテストアカウントで確認する

販売者が Stripe Connect のアカウントを持つ前提で、テストモードの connected account を用意します。

確認する項目は次の通りです。

- 送金先アカウントが正しいか
- 送金額が注文金額の 90% になっているか
- 送金失敗時のエラー処理があるか

### 7.5 ステージング環境で一連の流れを通す

本番公開前に、できればステージング環境で次を通しで試します。

1. スキル詳細で `購入する` を押す。
2. Stripe Checkout に遷移する。
3. テストカードで支払う。
4. 取引チャットに入る。
5. 出品者として納品する。
6. 購入者として評価付きで完了する。
7. 送金ログを確認する。

### 7.6 本番前の最終確認

本番 live key に切り替える前に、次を必ず見ると安全です。

- `.env` の Stripe キーが test のままになっていないか
- Webhook URL が本番向けになっているか
- `success_url` と `cancel_url` が本番ドメインになっているか
- 送金先がテストアカウントではないか
- 送金ログと注文履歴が一致しているか

## 8. 実装時の注意点

### 8.1 Webhook を正にする
- ブラウザのリダイレクトより、Webhook を正しい支払い確定ソースにする。

### 8.2 二重送金を防ぐ
- `stripe_transfer_id` や `payout_status` を保存して、完了処理が2回走っても送金が重複しないようにする。

### 8.3 金額は注文時点で固定する
- 出品価格が後で変わっても、すでに買った注文の金額は変えない。

### 8.4 失敗時の導線を作る
- 支払い失敗
- Checkout キャンセル
- Webhook 受信失敗
- 送金失敗

この4つは、後で再実行や再確認ができるようにしておく。

## 9. この設計の一言まとめ

「購入時に Stripe Checkout で支払いを確定し、取引完了時に Stripe Connect で 90% を販売者へ送る。Laravel は注文管理と状態管理を担当し、Stripe は決済と送金を担当する」

