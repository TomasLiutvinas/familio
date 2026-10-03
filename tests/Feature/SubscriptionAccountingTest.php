<?php

namespace Tests\Feature;

use App\Filament\Resources\Subscriptions\Pages\ListSubscriptions;
use App\Filament\Resources\MemberPayments\Pages\CreateMemberPayment;
use App\Filament\Resources\SubscriptionCharges\Pages\CreateSubscriptionCharge;
use App\Filament\Widgets\PendingPaymentsWidget;
use App\Filament\Widgets\RecentPaymentsWidget;
use App\Models\MemberPayment;
use App\Models\Person;
use App\Models\Subscription;
use App\Models\SubscriptionCharge;
use App\Models\SubscriptionMember;
use App\Models\User;
use App\Services\ChargeBalances;
use App\Services\SubscriptionPriceChange;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionAccountingTest extends TestCase
{
    use RefreshDatabase;

    private function subscription(int $members = 6): Subscription
    {
        $owner = Person::create(['name' => 'Owner']);
        $subscription = Subscription::create(['service_name' => 'YouTube', 'billing_period' => 'yearly',
            'default_amount_eur' => 240, 'owner_id' => $owner->id, 'started_on' => '2025-11-01']);
        SubscriptionMember::create(['subscription_id' => $subscription->id, 'person_id' => $owner->id]);
        for ($i = 1; $i < $members; $i++) {
            $person = Person::create(['name' => 'Member '.$i]);
            SubscriptionMember::create(['subscription_id' => $subscription->id, 'person_id' => $person->id]);
        }

        return $subscription;
    }

    private function charge(Subscription $subscription, int $year = 2025, float $amount = 240): SubscriptionCharge
    {
        return SubscriptionCharge::create(['subscription_id' => $subscription->id, 'period_year' => $year,
            'charge_date' => $year.'-11-01', 'amount_eur' => $amount, 'notes' => 'Original collection']);
    }

    private function login(): void
    {
        $this->actingAs(User::factory()->create());
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_price_change_adjusts_only_remaining_months_and_keeps_payments(): void
    {
        $subscription = $this->subscription();
        $past = $this->charge($subscription, 2024);
        $current = $this->charge($subscription);
        $future = $this->charge($subscription, 2026);
        $member = $subscription->members()->where('person_id', '!=', $subscription->owner_id)->first();
        $payment = MemberPayment::create(['charge_id' => $current->id, 'person_id' => $member->person_id,
            'amount_eur' => 40, 'paid_on' => '2025-11-02']);
        $service = app(SubscriptionPriceChange::class);
        $preview = $service->preview($subscription, '2026-09-01', 22);
        $this->assertSame([24400, 26400], array_column($preview['changes'], 'after_cents'));
        $this->assertEquals(240, $current->fresh()->amount_eur, 'Preview must not write');
        $service->apply($subscription, '2026-09-01', 22);
        $this->assertEquals(240, $past->fresh()->amount_eur);
        $this->assertEquals(244, $current->fresh()->amount_eur);
        $this->assertEquals(264, $future->fresh()->amount_eur);
        $this->assertEquals(264, $subscription->fresh()->default_amount_eur);
        $this->assertEquals(40, $payment->fresh()->amount_eur);
        $this->assertEquals($current->id, $payment->fresh()->charge_id);
        $this->assertStringContainsString('Original collection', $current->fresh()->notes);
        $this->assertStringContainsString('2026-09-01', $current->fresh()->notes);
        $service->apply($subscription, '2026-09-01', 22);
        $this->assertEquals(244, $current->fresh()->amount_eur, 'Same price must not adjust twice');
        $balances = app(ChargeBalances::class)->forCharge($current->fresh());
        $this->assertSame(67, $balances[$member->person_id]['outstanding_cents']);
    }

    public function test_partial_and_multiple_payments_and_owner_shares_use_exact_cents(): void
    {
        $subscription = $this->subscription(3);
        $charge = $this->charge($subscription, 2025, 10);
        $members = $subscription->members()->orderBy('person_id')->get();
        foreach ([1, 1.5] as $amount) {
            MemberPayment::create(['charge_id' => $charge->id, 'person_id' => $members[1]->person_id,
                'amount_eur' => $amount, 'paid_on' => '2025-11-02']);
        }
        MemberPayment::create(['charge_id' => $charge->id, 'person_id' => $members[2]->person_id,
            'amount_eur' => 5, 'paid_on' => '2025-11-02']);
        $balances = app(ChargeBalances::class)->forCharge($charge);
        $this->assertSame(1000, $balances->sum('due_cents'));
        $this->assertSame(0, $balances[$subscription->owner_id]['outstanding_cents']);
        $this->assertSame(83, $balances[$members[1]->person_id]['outstanding_cents']);
        $this->assertSame(0, $balances[$members[2]->person_id]['outstanding_cents']);
        $pending = app(ChargeBalances::class)->pendingByPerson();
        $this->assertCount(1, $pending);
        $this->assertSame(83, $pending[$members[1]->person_id]['total_cents']);
    }

    public function test_credit_on_one_charge_does_not_hide_debt_on_another(): void
    {
        $subscription = $this->subscription(2);
        $first = $this->charge($subscription, 2025, 10);
        $second = $this->charge($subscription, 2026, 10);
        $person = $subscription->members()->where('person_id', '!=', $subscription->owner_id)->first()->person_id;
        MemberPayment::create(['charge_id' => $first->id, 'person_id' => $person, 'amount_eur' => 10, 'paid_on' => '2025-11-02']);
        $this->assertSame(500, app(ChargeBalances::class)->pendingByPerson()[$person]['total_cents']);
        $this->assertSame($second->id, app(ChargeBalances::class)->pendingByPerson()[$person]['charges'][0]['charge_id']);
    }

    public function test_price_change_rejects_mid_month_dates_without_writing(): void
    {
        $subscription = $this->subscription();
        $charge = $this->charge($subscription);
        try {
            app(SubscriptionPriceChange::class)->apply($subscription, '2026-09-15', 22);
            $this->fail('Expected validation failure');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('effective_from', $exception->errors());
        }
        $this->assertEquals(240, $charge->fresh()->amount_eur);
        $this->assertEquals(240, $subscription->fresh()->default_amount_eur);
    }

    public function test_forms_suggest_remaining_share_and_new_annual_default(): void
    {
        $subscription = $this->subscription();
        $charge = $this->charge($subscription, 2025, 244);
        $member = $subscription->members()->where('person_id', '!=', $subscription->owner_id)->first();
        MemberPayment::create(['charge_id' => $charge->id, 'person_id' => $member->person_id,
            'amount_eur' => 40, 'paid_on' => '2025-11-02']);
        $subscription->update(['default_amount_eur' => 264]);
        $this->login();
        Livewire::test(CreateMemberPayment::class)
            ->set('data.charge_id', $charge->id)
            ->set('data.person_id', $member->person_id)
            ->assertSet('data.amount_eur', '0.67');
        Livewire::test(CreateSubscriptionCharge::class)
            ->set('data.subscription_id', $subscription->id)
            ->assertSet('data.amount_eur', '264.00');
    }

    public function test_dashboard_widgets_and_price_action_render(): void
    {
        $subscription = $this->subscription();
        $charge = $this->charge($subscription);
        $this->login();
        Livewire::test(PendingPaymentsWidget::class)->assertSee('Pending Payments')->assertSee('Member 1')->assertSee('40.00');
        Livewire::test(RecentPaymentsWidget::class)->assertSee('Recent Payments')->assertSee('familio-recent-payments');
        Livewire::test(ListSubscriptions::class)
            ->assertTableActionVisible('changePrice', $subscription)
            ->mountTableAction('changePrice', $subscription)
            ->assertSet('mountedActions.0.name', 'changePrice')
            ->setActionData(['monthly_price' => 22, 'effective_from' => '2026-09-01'])
            ->callMountedAction()
            ->assertHasNoActionErrors();
        $this->assertEquals(244, $charge->fresh()->amount_eur);
        Livewire::withoutLazyLoading();
        $this->get('/admin')->assertOk()->assertSee('Pending Payments')->assertSee('Recent Payments');
    }
}
