<?php

namespace Tests\Feature;

use App\Actions\AdvanceOrderProductionAction;
use App\Actions\RecordPaymentAction;
use App\Actions\SetOrderFinalPriceAction;
use App\Actions\VerifyPaymentAction;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderManagementWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_stage_three_order_payment_inventory_cashbook_and_tracking_workflow(): void
    {
        Storage::fake('private_uploads');
        $admin = User::factory()->create(['is_admin' => true]);
        $product = Product::factory()->create(['base_price' => 100000, 'stock_on_hand' => 10, 'stock_minimum' => 2]);
        $order = Order::factory()->create(['estimated_subtotal' => 200000, 'estimated_total' => 200000]);
        OrderItem::factory()->for($order)->for($product)->create(['quantity' => 2, 'subtotal' => 200000, 'unit_estimate' => 100000]);

        app(SetOrderFinalPriceAction::class)->execute($order, 240000, 20000, $admin, 'Harga final');
        $order->refresh();

        $this->assertSame('waiting_customer_approval', $order->status);
        $this->assertSame(240000, $order->final_total);
        $this->get(route('orders.status', ['order_number' => $order->order_number, 'phone' => $order->customer->phone]))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->post(route('orders.approve', $order), ['payment_scheme' => 'down_payment'])->assertRedirect();
        $payment = app(RecordPaymentAction::class)->execute($order->refresh(), 'qris', 'down_payment', 120000, UploadedFile::fake()->create('bukti.pdf', 20, 'application/pdf'));
        Storage::disk('private_uploads')->assertExists($payment->proof_path);

        app(VerifyPaymentAction::class)->verify($payment, $admin);
        app(VerifyPaymentAction::class)->verify($payment, $admin);
        $order->refresh();

        $this->assertSame(120000, $order->amount_paid);
        $this->assertDatabaseCount('cashbook_entries', 0);

        app(VerifyPaymentAction::class)->cancelVerification($payment, $admin);
        $this->assertSame(0, $order->refresh()->amount_paid);
        $this->assertDatabaseCount('cashbook_entries', 0);

        $fullPayment = app(RecordPaymentAction::class)->execute($order->refresh(), 'bank_transfer', 'full', 240000);
        app(VerifyPaymentAction::class)->verify($fullPayment, $admin);

        app(AdvanceOrderProductionAction::class)->start($order->refresh(), $admin);
        app(AdvanceOrderProductionAction::class)->start($order->refresh(), $admin);

        $this->assertSame(8, $product->refresh()->stock_on_hand);
        $this->assertSame(1, InventoryMovement::query()->where('reference', 'order-item:'.$order->items()->first()->id.':production')->count());
        $this->assertDatabaseHas('activity_logs', ['subject_id' => $order->id, 'event' => 'production.started']);
    }
}
