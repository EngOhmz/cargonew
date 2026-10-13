<?php

namespace App\Services;

use App\Models\Sms;
use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiUrl;
    protected $apiToken;
    protected $defaultSenderId;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.url');
        $this->apiToken = config('services.sms.token');
        $this->defaultSenderId = config('services.sms.sender_id', 'MADEKENYA');
    }

    /**
     * Send SMS using template key (1-4), resolved per company.
     * All templates are kept but disabled (no send) until re-enabled.
     */
    public function sendTemplateSms($templateKey, $replacements, $phoneNumber, $customerId = null, $companyId = null)
    {
        $key = (int) $templateKey;
        $template = Sms::resolveTemplate($key, $companyId);

        if (!$template) {
            Log::error("SMS Template key {$key} not found for company {$companyId}.");
            return ['success' => false, 'message' => 'Template not found', 'skipped' => false];
        }

        if (!$template->isSendingEnabled()) {
            Log::info("SMS Template key {$key} is disabled — skip send", [
                'company_id' => $companyId,
                'customer_id' => $customerId,
            ]);

            return [
                'success' => true,
                'skipped' => true,
                'message' => 'SMS template is disabled',
            ];
        }

        $message = $template->message;

        foreach ($replacements as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }

        return $this->sendSms($phoneNumber, $message, $customerId, $companyId);
    }

    /**
     * Send SMS directly via Imart oneClick API v3.
     */
    public function sendSms($phoneNumber, $message, $customerId = null, $companyId = null)
    {
        $phoneNumber = $this->normalizePhoneNumber($phoneNumber);

        if (!$phoneNumber) {
            return [
                'success' => false,
                'message' => 'Invalid phone number format'
            ];
        }

        $senderId = $this->resolveSenderId($companyId);

        $smsRecord = Sms::create([
            'customer_id' => $customerId,
            'phone_number' => $phoneNumber,
            'message' => $message,
            'status' => 'pending',
            'company_id' => $companyId,
        ]);

        Log::info("SMS Attempt: ID {$smsRecord->id} to {$phoneNumber} sender {$senderId}");

        try {
            $payload = [
                'recipient' => $phoneNumber,
                'sender_id' => $senderId,
                'type' => 'plain',
                'message' => $message,
            ];

            Log::debug('SMS API Request Payload:', $payload);

            $response = Http::withToken($this->apiToken)
                ->acceptJson()
                ->asJson()
                ->post($this->apiUrl, $payload);

            $result = trim($response->body());
            $smsRecord->response = $result;

            Log::info("SMS API Response for ID {$smsRecord->id}: {$result}");

            $json = $response->json();
            $isSuccess = $response->successful()
                && is_array($json)
                && (($json['status'] ?? null) === 'success');

            if ($isSuccess) {
                $shootId = data_get($json, 'data.uid')
                    ?: data_get($json, 'data.id')
                    ?: data_get($json, 'uid');

                $smsRecord->status = 'sent';
                $smsRecord->shoot_id = $shootId ? (string) $shootId : null;
                $smsRecord->save();

                Log::info("SMS SENT: ID {$smsRecord->id}, UID: {$shootId}");

                return [
                    'success' => true,
                    'shoot_id' => $shootId,
                    'sms_id' => $smsRecord->id
                ];
            }

            $errorMessage = is_array($json)
                ? ($json['message'] ?? $result)
                : $result;

            $smsRecord->status = 'failed';
            $smsRecord->save();

            Log::error("SMS FAILED: ID {$smsRecord->id}, Error: {$errorMessage}");

            return [
                'success' => false,
                'message' => $errorMessage,
                'sms_id' => $smsRecord->id
            ];
        } catch (\Exception $e) {
            $smsRecord->status = 'failed';
            $smsRecord->response = $e->getMessage();
            $smsRecord->save();

            Log::critical("SMS EXCEPTION: ID {$smsRecord->id}, Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'sms_id' => $smsRecord->id
            ];
        }
    }

    /**
     * SMS #1 — Mzigo umepokelewa ofisini (registration).
     */
    public function sendArrivalAtOfficeSms($phoneNumber, $supplier, $packages, $customerId = null, $companyId = null, $deliveryRef = null)
    {
        $ref = trim((string) ($deliveryRef ?? ''));
        $companyName = $this->resolveCompanyName($companyId);
        $replacements = [
            '[Company]' => $companyName,
            '[Supplier]' => $supplier,
            '[Packages]' => $packages,
            '[Ref]' => $ref,
            '[REF]' => $ref,
        ];

        $template = Sms::resolveTemplate(1, $companyId);
        if (!$template) {
            Log::error('SMS Template key 1 not found.');
            return ['success' => false, 'message' => 'Template not found'];
        }

        if (!$template->isSendingEnabled()) {
            Log::info('SMS Template 1 disabled — skip receive/office SMS', [
                'company_id' => $companyId,
            ]);
            return ['success' => true, 'skipped' => true, 'message' => 'SMS template is disabled'];
        }

        $message = $template->message;
        foreach ($replacements as $placeholder => $value) {
            $message = str_replace($placeholder, $value, $message);
        }

        if ($ref !== '' && stripos($message, $ref) === false) {
            $message = rtrim($message, " \t\n\r.") . '. REF: ' . $ref . '.';
        }

        return $this->sendSms($phoneNumber, $message, $customerId, $companyId);
    }

    /**
     * SMS #2 — Dispatch (DISABLED by default; kept for future).
     */
    public function sendDispatchSms($phoneNumber, $vehicleId, $packages, $customerId = null, $companyId = null)
    {
        $replacements = [
            '[Vehicle]' => $vehicleId,
            '[Packages]' => $packages,
            '[Company]' => $this->resolveCompanyName($companyId),
        ];
        return $this->sendTemplateSms(2, $replacements, $phoneNumber, $customerId, $companyId);
    }

    /**
     * SMS #3 — Mzigo umefika eneo la kusudi (on drop / unload).
     */
    public function sendArrivalAtDestinationSms($phoneNumber, $destination, $packages, $amount, $customerId = null, $companyId = null, $deliveryRef = null)
    {
        $formattedAmount = number_format((float) $amount, 0, '.', ',');
        $ref = trim((string) ($deliveryRef ?? ''));
        $replacements = [
            '[Company]' => $this->resolveCompanyName($companyId),
            '[Destination]' => $destination,
            '[Packages]' => $packages,
            '[Amount]' => $formattedAmount,
            '[Ref]' => $ref,
            '[REF]' => $ref,
        ];
        return $this->sendTemplateSms(3, $replacements, $phoneNumber, $customerId, $companyId);
    }

    /**
     * SMS #4 — Payment confirmation (DISABLED by default; kept for future).
     */
    public function sendPaymentConfirmationSms($phoneNumber, $amount, $customerId = null, $companyId = null)
    {
        $formattedAmount = number_format((float) $amount, 0, '.', ',');
        $replacements = [
            '[Company]' => $this->resolveCompanyName($companyId),
            '[Amount]' => $formattedAmount,
        ];
        return $this->sendTemplateSms(4, $replacements, $phoneNumber, $customerId, $companyId);
    }

    /**
     * Get SMS by ID
     */
    public function getSmsById($id)
    {
        return Sms::find($id);
    }

    /**
     * Normalize to 255XXXXXXXXX (TZ mobile with country code, no plus).
     */
    protected function normalizePhoneNumber($phoneNumber): ?string
    {
        $phoneNumber = preg_replace('/\s+/', '', (string) $phoneNumber);
        $phoneNumber = preg_replace('/[^\d+]/', '', $phoneNumber);

        if (str_starts_with($phoneNumber, '+255')) {
            $phoneNumber = substr($phoneNumber, 4);
        } elseif (str_starts_with($phoneNumber, '255')) {
            $phoneNumber = substr($phoneNumber, 3);
        }

        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) > 9) {
            $phoneNumber = substr($phoneNumber, -9);
        }

        if (strlen($phoneNumber) !== 9 || !ctype_digit($phoneNumber)) {
            Log::warning("Invalid phone number after normalize: {$phoneNumber}");
            return null;
        }

        return '255' . $phoneNumber;
    }

    protected function resolveSenderId($companyId): string
    {
        if ($companyId) {
            $company = Company::find($companyId);
            $custom = trim((string) ($company->sms_sender_id ?? ''));
            if ($custom !== '') {
                return $custom;
            }
        }

        return $this->defaultSenderId ?: 'MADEKENYA';
    }

    protected function resolveCompanyName($companyId): string
    {
        if (!$companyId) {
            return 'Madekenya';
        }

        $company = Company::find($companyId);

        if (!$company) {
            return 'Madekenya';
        }

        return $company->app_name ?: $company->cname ?: 'Madekenya';
    }
}
