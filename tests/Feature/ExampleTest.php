<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Sales;
use App\Models\SalesItem;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_sums_same_product_quantity_for_highest_volume(): void
    {
        $store = Store::create([
            'name' => 'Toko Demo',
            'username' => 'tokodemo',
            'address' => 'Jakarta',
            'package' => 'basic',
            'max_radius_attendance' => 100,
            'inventory_method' => 'FIFO',
        ]);

        $user = User::factory()->create();
        $access = UserStore::create([
            'user_id' => $user->id,
            'store_id' => $store->id,
            'role' => 'owner',
        ]);
        $user->update(['access_id' => $access->id]);

        $productA = Product::create([
            'store_id' => $store->id,
            'name' => 'Product A',
            'description' => 'Product A description',
            'price' => 10000,
            'point' => 0,
        ]);

        $productB = Product::create([
            'store_id' => $store->id,
            'name' => 'Product B',
            'description' => 'Product B description',
            'price' => 15000,
            'point' => 0,
        ]);

        ProductStock::create([
            'store_id' => $store->id,
            'product_id' => $productA->id,
            'supplier_id' => null,
            'label' => 'Stock A',
            'cost_price' => 8000,
            'start_quantity' => 10,
            'quantity' => 10,
            'expired_at' => null,
        ]);

        ProductStock::create([
            'store_id' => $store->id,
            'product_id' => $productB->id,
            'supplier_id' => null,
            'label' => 'Stock B',
            'cost_price' => 12000,
            'start_quantity' => 10,
            'quantity' => 10,
            'expired_at' => null,
        ]);

        $customer = Customer::create([
            'store_id' => $store->id,
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'whatsapp' => '081234567890',
            'point' => 0,
        ]);

        $sale1 = Sales::create([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-001',
            'total_quantity' => 5,
            'total_price' => 50000,
            'total_margin' => 20000,
        ]);

        $sale2 = Sales::create([
            'store_id' => $store->id,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-002',
            'total_quantity' => 3,
            'total_price' => 30000,
            'total_margin' => 10000,
        ]);

        $stockA = ProductStock::firstWhere('product_id', $productA->id);
        $stockB = ProductStock::firstWhere('product_id', $productB->id);

        SalesItem::create([
            'store_id' => $store->id,
            'sales_id' => $sale1->id,
            'product_id' => $productA->id,
            'stock_id' => $stockA->id,
            'price' => 10000,
            'quantity' => 2,
            'total_price' => 20000,
            'margin' => 5000,
        ]);

        SalesItem::create([
            'store_id' => $store->id,
            'sales_id' => $sale2->id,
            'product_id' => $productA->id,
            'stock_id' => $stockA->id,
            'price' => 10000,
            'quantity' => 3,
            'total_price' => 30000,
            'margin' => 8000,
        ]);

        SalesItem::create([
            'store_id' => $store->id,
            'sales_id' => $sale2->id,
            'product_id' => $productB->id,
            'stock_id' => $stockB->id,
            'price' => 15000,
            'quantity' => 1,
            'total_price' => 15000,
            'margin' => 3000,
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/home');

        $response->assertOk();
        $response->assertJsonPath('highestVolume.0.product.id', $productA->id);
        $response->assertJsonPath('highestVolume.0.total_quantity', 5);
        $this->assertCount(2, $response->json('highestVolume'));
    }
}
