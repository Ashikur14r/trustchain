<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'two_factor_secret') || ! Schema::hasColumn('users', 'two_factor_enabled')) {
            return;
        }

        DB::table('users')
            ->whereIn('email', ['seller@trustchain.com', 'buyer@trustchain.com'])
            ->update([
                'two_factor_secret' => null,
                'two_factor_enabled' => false,
            ]);
    }

    public function down(): void
    {
        // Demo accounts intentionally remain password-only after rollback.
    }
};
