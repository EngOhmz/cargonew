<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRefPlaceholderToArrivalOfficeSmsTemplate extends Migration
{
    /**
     * Ensure registration SMS template includes REF No for parcel inquiry.
     */
    public function up()
    {
        $template = DB::table('sms')->where('id', 1)->where('status', 'template')->first();
        if (!$template) {
            return;
        }

        $message = (string) $template->message;
        if (stripos($message, '[Ref]') !== false || stripos($message, '[REF]') !== false) {
            return;
        }

        // Prefer inserting before the pickup instruction if present.
        $needle = 'Tafadhali fanya maandalizi';
        if (stripos($message, $needle) !== false) {
            $message = preg_replace(
                '/' . preg_quote($needle, '/') . '/i',
                'REF No: [Ref]. ' . $needle,
                $message,
                1
            );
        } else {
            $message = rtrim($message, " \t\n\r.") . '. REF No: [Ref].';
        }

        DB::table('sms')->where('id', 1)->update([
            'message' => $message,
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $template = DB::table('sms')->where('id', 1)->where('status', 'template')->first();
        if (!$template) {
            return;
        }

        $message = preg_replace('/\s*REF No:\s*\[Ref\]\.?/i', '', (string) $template->message);
        $message = preg_replace('/\s*REF No:\s*\[REF\]\.?/i', '', $message);

        DB::table('sms')->where('id', 1)->update([
            'message' => trim($message),
            'updated_at' => now(),
        ]);
    }
}
