<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Device;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrustChainTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_real_transaction_counters()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $transaction = Transaction::create([
            'transaction_code' => 'TC-TEST-001',
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'pending',
        ]);

        Device::create([
            'transaction_id' => $transaction->id,
            'device_type' => 'Smartphone',
            'brand' => 'Apple',
            'model' => 'iPhone 15',
            'agreed_price' => 799.00,
            'condition_description' => 'Mint condition',
        ]);

        $response = $this->actingAs($seller)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Total Transactions');
        $response->assertSee('TC-TEST-001');
    }

    public function test_seller_can_create_transaction_with_device_and_accessories()
    {
        $seller = User::factory()->create(['email' => 'seller@test.com']);
        $buyer = User::factory()->create(['email' => 'buyer@test.com']);

        $response = $this->actingAs($seller)->post('/transactions', [
            'buyer_email' => 'buyer@test.com',
            'device_type' => 'Laptop',
            'brand' => 'Apple',
            'model' => 'MacBook Air M2',
            'serial_number' => 'C02XYZ123',
            'agreed_price' => 850.00,
            'condition_description' => 'Great condition, 90% battery health',
            'seller_notes' => 'Includes original charger and box',
            'accessories' => [
                ['name' => 'Original 35W Dual Charger', 'condition' => 'Like New'],
                ['name' => 'MagSafe 3 Cable', 'condition' => 'Good'],
            ],
        ]);

        $this->assertDatabaseHas('devices', [
            'brand' => 'Apple',
            'model' => 'MacBook Air M2',
            'serial_number' => 'C02XYZ123',
        ]);

        $this->assertDatabaseHas('accessories', [
            'name' => 'Original 35W Dual Charger',
        ]);

        $transaction = Transaction::where('seller_id', $seller->id)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals($buyer->id, $transaction->buyer_id);

        $response->assertRedirect(route('transactions.show', $transaction));
    }

    public function test_buyer_can_confirm_transaction_and_certificate_is_issued()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $transaction = Transaction::create([
            'transaction_code' => 'TC-TEST-CONFIRM',
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'pending',
        ]);

        Device::create([
            'transaction_id' => $transaction->id,
            'device_type' => 'Tablet',
            'brand' => 'Apple',
            'model' => 'iPad Pro 11"',
            'agreed_price' => 600.00,
            'condition_description' => 'Flawless condition',
        ]);

        // Seller cannot confirm
        $this->actingAs($seller)
            ->post("/transactions/{$transaction->id}/confirm")
            ->assertStatus(403);

        // Buyer confirms
        $response = $this->actingAs($buyer)
            ->post("/transactions/{$transaction->id}/confirm", [
                'buyer_notes' => 'Inspected device, serial confirmed.',
            ]);

        $response->assertRedirect(route('transactions.show', $transaction));

        $transaction->refresh();
        $this->assertEquals('confirmed', $transaction->status);
        $this->assertNotNull($transaction->confirmed_at);

        // Verify Certificate was generated
        $this->assertDatabaseHas('certificates', [
            'transaction_id' => $transaction->id,
        ]);

        $certificate = $transaction->certificate;
        $this->assertNotNull($certificate);
        $this->assertStringStartsWith('TC-CERT-', $certificate->certificate_number);
    }

    public function test_public_verification_portal_validates_legitimate_certificate()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();

        $transaction = Transaction::create([
            'transaction_code' => 'TC-TEST-VERIFY',
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        Device::create([
            'transaction_id' => $transaction->id,
            'device_type' => 'Smartphone',
            'brand' => 'Google',
            'model' => 'Pixel 8 Pro',
            'serial_number' => 'G-PIXEL-883',
            'imei' => '354890011223344',
            'agreed_price' => 650.00,
            'condition_description' => 'Near mint condition',
        ]);

        $cert = Certificate::create([
            'transaction_id' => $transaction->id,
            'certificate_number' => 'TC-CERT-2026-99999',
            'verification_token' => 'verify-test-token-pixel8pro',
            'issued_at' => now(),
        ]);

        // 1. Verify via token
        $response = $this->get('/verify/verify-test-token-pixel8pro');
        $response->assertStatus(200);
        $response->assertSee('VERIFIED AUTHENTIC RECORD');
        $response->assertSee('Pixel 8 Pro');
        $response->assertSee('TC-CERT-2026-99999');

        // 2. Verify via query redirect
        $redirectResponse = $this->get('/verify?query=TC-CERT-2026-99999');
        $redirectResponse->assertRedirect('/verify/TC-CERT-2026-99999');

        // 3. Verify invalid token returns not found
        $invalidResponse = $this->get('/verify/non-existent-fake-token');
        $invalidResponse->assertStatus(200);
        $invalidResponse->assertSee('RECORD NOT FOUND');
    }
}
