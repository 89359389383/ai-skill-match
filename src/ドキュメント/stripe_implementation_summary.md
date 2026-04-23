## Stripe 実装まとめ（購入者→運営→販売者までの流れ）

このページは「購入者のお金が、運営を通って販売者へ届くまで」を、**IT知識ゼロでも分かる言葉**で説明します。

このアプリでは、支払い（カード決済）の“確定”は **Webhook**を正として扱い、その後の取引完了タイミングで **送金（transfer）**を行います。

---
## 登場人物（誰が関わる？）

- 購入者（buyer）：スキルを買ってお金を払う人
- 販売者（freelancer）：スキルを提供する人
- 運営（このアプリ）：購入者からの支払いを受け取り、販売者へ送る
- Stripe：カード決済・Webhook通知・送金（transfer）を担当する

---
## 「10%手数料」って何？

販売者へ送る金額は、注文金額から **10%を差し引いた額**です。

- 運営手数料（10%）＝ `floor(注文金額 × 0.10)`
- 販売者へ送る金額＝ `注文金額 − 運営手数料`

この計算は `PayoutService` の `calculateAmounts()` で行われます。

---
## 全体の流れ（5ステップ）

1. 購入者が購入して Stripe の支払い画面へ進む
2. 購入者が Stripe で支払い完了する
3. Stripe が Webhook で「支払い完了」をアプリへ通知する（運営が注文を確定）
4. 取引が進む（納品→購入者の完了）
5. 取引完了のタイミングで、運営が手数料10%を差し引いた金額を販売者へ送金する

---
## ステップ1：購入者が購入して“仮注文”を作る

購入者が `購入` を押すと、サーバは先に注文を作ります（まだ確定していないので“仮”です）。

この時点のイメージ：
- 注文状態：`pending`（未確定）
- 取引状態：`waiting_payment`（支払い待ち）
- 送金状態：`not_transferred`（まだ送金しない）

担当：`SkillOrderController@store`

さらにサーバは、Stripeへ支払い依頼をするための「支払い画面（Checkout Session）」を作ります。

このとき、後でWebhookで注文を見つけるための情報として `order_id` を Stripe の metadata に保存します。

---
## ステップ2：購入者が Stripe で支払いを完了する

購入者が Stripe の画面で支払いを完了すると、Stripe 側で「支払い完了」の出来事が発生します。

しかし、このアプリは“画面が成功した”だけでは確定にしません。
確定は必ずWebhookで行います。

---
## ステップ3：Webhookで支払いを確定（運営のサーバが注文を更新）

Stripe は `POST /stripe/webhook` 宛てに通知します。

担当：`StripeWebhookController@handle`

Webhookが届いたときに行うこと（超重要）：

1. Stripe-Signatureを使って、通知が本物かどうか検証する
2. `checkout.session.completed` の場合だけ処理する
3. `metadata.order_id` を使って「どの注文か」を特定する
4. `skill_orders` を更新して、支払いを確定させる

この更新で変更されるイメージ：
- 注文状態：`paid`（支払い確定）
- 支払い日時：`paid_at` を保存
- 取引状態（escrowの場合）：`TX_IN_PROGRESS` に進める

ここまでで「お金は支払われた」ことが確定します。
ただし、ここではまだ販売者へ送金しません。

---
## ステップ4：取引を進める（納品→購入者の完了）

取引は escrow（預かり）として段階があります。

流れのイメージ：
- 販売者が「納品」する
  - `transaction_status` が `TX_DELIVERED` になる
- 購入者が「取引完了」する
  - `transaction_status` の完了処理に進み、送金へ進む

この段階は、Webページ上で操作されます。

---
## ステップ5：取引完了で送金（10%手数料を差し引く）

購入者が「取引完了」を押すと、サーバは `SkillTransactionController@complete` を実行します。

その中で `PayoutService->transferForOrder()` を呼び出し、販売者へ送金します。

### 送金時に起きること

1. `PayoutService` が「運営手数料10%」と「販売者取り分」を計算する
2. 販売者（freelancer）の Stripe Connect アカウントへ transfer を作成する
3. 注文に送金結果の情報を保存する
   - `stripe_transfer_id`
   - `transferred_at`
   - `payout_status = transferred`
4. 最後に取引状態を `TX_COMPLETED` に進める

---
## まとめ（お金の流れを一言で）

- 購入者が支払い → StripeのWebhookで運営が支払い確定
- 取引が完了 → 運営が10%手数料を差し引く
- 残りをStripe Connect経由で販売者へ送金

