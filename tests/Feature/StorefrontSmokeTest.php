<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->seed();
        $this->get('/')->assertOk();
    }

    public function test_admin_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_dashboard_for_admin_user(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_cod_checkout_creates_order(): void
    {
        $this->seed();
        $product = Product::query()->first();
        $this->assertNotNull($product);

        $this->post('/cart', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertRedirect(route('cart.index'));

        $response = $this->post('/checkout', [
            'name' => 'Test Buyer',
            'phone' => '03001112233',
            'email' => 'buyer@example.com',
            'city' => 'Karachi',
            'address' => 'Street 1, Block A',
            'notes' => null,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }
}
