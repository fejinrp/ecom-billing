<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LuckyDrawSetting;

class LuckyDrawSettingSeeder extends Seeder
{
    /**
     * Seed default lucky draw categories.
     * These mirror the reference app's rules but are now fully configurable via the admin UI.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_key'   => 'bronze',
                'category_label' => 'Bronze',
                'min_amount'     => 0.01,
                'max_amount'     => 999.99,
                'batch_size'     => 40,
                'prize_amount'   => 1000.00,
                'is_active'      => true,
            ],
            [
                'category_key'   => 'premium',
                'category_label' => 'Premium',
                'min_amount'     => 1000.00,
                'max_amount'     => null,       // No upper limit
                'batch_size'     => 20,
                'prize_amount'   => 1000.00,
                'is_active'      => true,
            ],
        ];

        foreach ($categories as $data) {
            LuckyDrawSetting::updateOrCreate(
                ['category_key' => $data['category_key']],
                $data
            );
        }

        $this->command->info('✅ Lucky draw default categories seeded (Bronze + Premium).');
    }
}
