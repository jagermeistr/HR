<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaB2CService
{
    protected $baseUrl; // Remove type declaration

    public function __construct()
    {
        $env = config('mpesa.b2c.env');
        $this->baseUrl = config("mpesa.urls.{$env}");
    }

    public function getAccessToken()
    {
        try {
            $response = Http::withBasicAuth(
                config('mpesa.b2c.consumer_key'),
                config('mpesa.b2c.consumer_secret')
            )->get($this->baseUrl['auth'], ['grant_type' => 'client_credentials']);

            if ($response->successful()) {
                Log::info('M-Pesa access token generated successfully');
                return $response->json()['access_token'];
            }

            Log::error('M-Pesa authentication failed', $response->json());
            return null;
        } catch (\Exception $e) {
            Log::error('M-Pesa authentication error: ' . $e->getMessage());
            return null;
        }
    }

    public function sendSalary($phone, $amount, $remarks = 'Salary Payment', $paymentId = null)
    {
        $token = $this->getAccessToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Failed to get access token from M-Pesa'
            ];
        }

        $requestData = [
            'InitiatorName' => config('mpesa.b2c.initiator_name'),
            'SecurityCredential' => config('mpesa.b2c.security_credential'),
            'CommandID' => 'BusinessPayment',
            'Amount' => $amount,
            'PartyA' => config('mpesa.b2c.shortcode'),
            'PartyB' => $this->formatPhoneNumber($phone),
            'Remarks' => $remarks,
            'QueueTimeOutURL' => config('mpesa.b2c.queue_timeout_url'),
            'ResultURL' => config('mpesa.b2c.result_url'),
            'Occasion' => 'Salary Payment'
        ];

        try {
            Log::info('Sending B2C payment request', $requestData);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl['b2c'], $requestData);

            $result = $response->json();
            Log::info('M-Pesa B2C API response', $result);

            if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'data' => $result,
                    'conversation_id' => $result['ConversationID'] ?? null,
                    'originator_conversation_id' => $result['OriginatorConversationID'] ?? null,
                    'message' => 'Salary payment initiated successfully'
                ];
            }

            return [
                'success' => false,
                'data' => $result,
                'message' => $result['ResponseDescription'] ?? 'Payment initiation failed'
            ];
        } catch (\Exception $e) {
            Log::error('M-Pesa B2C request failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'M-Pesa service temporarily unavailable'
            ];
        }
    }

    protected function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '254')) {
            return '254' . $phone;
        }

        return $phone;
    }
}
