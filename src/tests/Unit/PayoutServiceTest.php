<?php

namespace Tests\Unit;

use App\Contracts\StripeTransferClientInterface;
use App\Models\Buyer;
use App\Models\Freelancer;
use App\Models\SkillListing;
use App\Models\SkillOrder;
use App\Models\User;
use App\Services\PayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class PayoutServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_amounts(): void
    {
        $service = new PayoutService(Mockery::mock(StripeTransferClientInterface::class));
        $amounts = $service->calculateAmounts(10000);

        $this->assertSame(1000, $amounts['platform_fee']);
        $this->assertSame(9000, $amounts['seller_amount']);
    }

    public function test_transfer_is_skipped_when_already_transferred(): void
    {
        $order = $this->makePaidDeliveredOrder();
        $order->update([
            'stripe_transfer_id' => 'tr_exists',
            'payout_status' => SkillOrder::PAYOUT_TRANSFERRED,
        ]);

        $client = Mockery::mock(StripeTransferClientInterface::class);
        $client->shouldReceive('createTransfer')->never();

        $service = new PayoutService($client);
        $result = $service->transferForOrder($order);

        $this->assertSame('tr_exists', $result->stripe_transfer_id);
    }

    private function makePaidDeliveredOrder(): SkillOrder
    {
        $buyerUser = User::factory()->create(['role' => 'buyer']);
        Buyer::create(['user_id' => $buyerUser->id, 'display_name' => 'Buyer']);

        $sellerUser = User::factory()->create(['role' => 'freelancer']);
        $freelancer = Freelancer::create([
            'user_id' => $sellerUser->id,
            'display_name' => 'Seller',
            'job_title' => 'Engineer',
            'bio' => 'bio',
            'min_hours_per_week' => 10,
            'max_hours_per_week' => 40,
            'hours_per_day' => 8,
            'days_per_week' => 5,
            'work_availability_status' => 'available_full',
            'min_rate' => 1000,
            'max_rate' => 2000,
            'stripe_connect_account_id' => 'acct_unit_1',
        ]);

        $listing = SkillListing::create([
            'freelancer_id' => $freelancer->id,
            'title' => 'unit',
            'description' => 'desc',
            'purchase_instructions' => 'instruction',
            'price' => 10000,
            'pricing_type' => 'fixed',
            'status' => 1,
        ]);

        return SkillOrder::create([
            'skill_listing_id' => $listing->id,
            'buyer_user_id' => $buyerUser->id,
            'amount' => 10000,
            'status' => SkillOrder::STATUS_PAID,
            'payment_type' => SkillOrder::PAYMENT_TYPE_ESCROW,
            'transaction_status' => SkillOrder::TX_DELIVERED,
            'payout_status' => SkillOrder::PAYOUT_NOT_TRANSFERRED,
            'purchased_at' => Carbon::now(),
            'paid_at' => Carbon::now(),
            'delivered_at' => Carbon::now(),
        ]);
    }
}
