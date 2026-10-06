<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            ['key' => 'app_name', 'value' => 'EMAC Management', 'type' => 'string', 'group' => 'general'],
            ['key' => 'dashboard_logo', 'value' => null, 'type' => 'file', 'group' => 'branding'],
            ['key' => 'website_logo', 'value' => null, 'type' => 'file', 'group' => 'branding'],
            ['key' => 'timezone', 'value' => 'Asia/Karachi', 'type' => 'string', 'group' => 'general'],
            ['key' => 'session_lifetime', 'value' => '120', 'type' => 'integer', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'info@emacdevelopment.com', 'type' => 'string', 'group' => 'contact'],
            ['key' => 'support_phone', 'value' => '+1 (800) 555-0199', 'type' => 'string', 'group' => 'contact'],
        ];

        foreach ($defaults as $item) {
            Setting::updateOrCreate(
                ['key' => $item['key']],
                $item
            );
        }
    }
}
