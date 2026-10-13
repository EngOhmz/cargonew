<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sms;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Seed global default SMS templates (company_id = null).
     * Each company gets its own copies lazily via Sms::ensureCompanyTemplates().
     */
    public function run()
    {
        foreach (Sms::TEMPLATE_KEYS as $key) {
            Sms::updateOrCreate(
                [
                    'status' => 'template',
                    'company_id' => null,
                    'template_key' => $key,
                ],
                [
                    'message' => Sms::DEFAULT_MESSAGES[$key],
                    'phone_number' => null,
                ]
            );
        }
    }
}
