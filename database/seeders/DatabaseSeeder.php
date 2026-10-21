<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Device;
use App\Models\Accessory;
use App\Models\Evidence;
use App\Models\Certificate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create primary demo users with password-only sign-in
        $seller = User::updateOrCreate(
            ['email' => 'seller@trustchain.com'],
            [
                'name' => 'Alex Morgan',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ]
        );

        $buyer = User::updateOrCreate(
            ['email' => 'buyer@trustchain.com'],
            [
                'name' => 'Jordan Lee',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ]
        );

        // Also ensure test@example.com exists for quick login
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Transaction 1: Confirmed with Certificate
        $t1 = Transaction::firstOrCreate(
            ['transaction_code' => 'TC-2026-0001'],
            [
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'status' => 'confirmed',
                'seller_notes' => 'Factory reset, battery health 98%, includes original box and 20W brick.',
                'buyer_notes' => 'Inspected in person. IMEI and serial match description. Transfer accepted.',
                'confirmed_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(3),
            ]
        );

        $d1 = Device::firstOrCreate(
            ['transaction_id' => $t1->id],
            [
                'device_type' => 'Smartphone',
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro (256GB, Natural Titanium)',
                'serial_number' => 'C39ZT09MP0',
                'imei' => '354892110293847',
                'condition_description' => 'Mint condition, zero scratches on OLED display, minor micro-abrasion on bottom bezel corner.',
                'agreed_price' => 950.00,
            ]
        );

        // Accessories for Device 1
        $d1->accessories()->delete();
        Accessory::create(['device_id' => $d1->id, 'name' => 'Original Retail Box & Documentation', 'condition' => 'Like New']);
        Accessory::create(['device_id' => $d1->id, 'name' => 'Apple 20W USB-C Power Adapter', 'condition' => 'Used - Good']);
        Accessory::create(['device_id' => $d1->id, 'name' => 'Apple Braided USB-C Cable (1m)', 'condition' => 'Used - Good']);
        Accessory::create(['device_id' => $d1->id, 'name' => 'MagSafe Clear Protective Case', 'condition' => 'Fair']);

        // Evidence for Device 1
        $d1->evidence()->delete();
        Evidence::create([
            'device_id' => $d1->id,
            'file_path' => 'evidence/TC-2026-0001/front_screen_on.jpg',
            'file_name' => 'front_screen_on.jpg',
            'file_type' => 'image/jpeg',
            'evidence_type' => 'condition',
            'description' => 'Front display illuminated showing zero dead pixels and iOS hello setup screen.',
            'file_hash' => 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855',
        ]);
        Evidence::create([
            'device_id' => $d1->id,
            'file_path' => 'evidence/TC-2026-0001/imei_dialer_screen.jpg',
            'file_name' => 'imei_dialer_screen.jpg',
            'file_type' => 'image/jpeg',
            'evidence_type' => 'imei_verification',
            'description' => 'Photo of device displaying *#06# IMEI and serial number for verification.',
            'file_hash' => 'a591a6d40bf420404a011733cfb7b190d62c65bf0bcda32b57b277d9ad9f146e',
        ]);

        // Certificate for Transaction 1
        Certificate::updateOrCreate(
            ['transaction_id' => $t1->id],
            [
                'certificate_number' => 'TC-CERT-2026-88219',
                'verification_token' => 'verify-9f8a3c2b1e7d4a5c',
                'issued_at' => Carbon::now()->subDays(2),
            ]
        );

        // 3. Transaction 2: Pending Buyer Confirmation
        $t2 = Transaction::firstOrCreate(
            ['transaction_code' => 'TC-2026-0002'],
            [
                'seller_id' => $seller->id,
                'buyer_id' => $buyer->id,
                'status' => 'pending',
                'seller_notes' => 'MacBook Pro 14-inch M3 Pro. 18GB Unified Memory, 512GB SSD. Ready for handover.',
                'buyer_notes' => null,
                'confirmed_at' => null,
                'created_at' => Carbon::now()->subHours(6),
            ]
        );

        $d2 = Device::firstOrCreate(
            ['transaction_id' => $t2->id],
            [
                'device_type' => 'Laptop',
                'brand' => 'Apple',
                'model' => 'MacBook Pro 14" (M3 Pro, Space Black)',
                'serial_number' => 'W891234567X',
                'imei' => null,
                'condition_description' => 'Battery cycle count 34. No keyboard shine, pristine Liquid Retina XDR screen.',
                'agreed_price' => 1650.00,
            ]
        );

        $d2->accessories()->delete();
        Accessory::create(['device_id' => $d2->id, 'name' => 'Apple 70W USB-C Power Adapter', 'condition' => 'Like New']);
        Accessory::create(['device_id' => $d2->id, 'name' => 'USB-C to MagSafe 3 Cable (2m)', 'condition' => 'Like New']);

        $d2->evidence()->delete();
        Evidence::create([
            'device_id' => $d2->id,
            'file_path' => 'evidence/TC-2026-0002/system_report.jpg',
            'file_name' => 'system_report.jpg',
            'file_type' => 'image/jpeg',
            'evidence_type' => 'diagnostic',
            'description' => 'Hardware diagnostic check report showing normal battery and sensor states.',
            'file_hash' => '3f79bb7b435b05321651daefd374cd681b6101fa189c42a1875738ae348b7393',
        ]);
    }
}
