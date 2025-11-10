<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MpesaB2CService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaB2CController extends Controller
{
    protected MpesaB2CService $mpesaB2C;

    public function __construct(MpesaB2CService $mpesaB2C)
    {
        $this->mpesaB2C = $mpesaB2C;
    }

    public function handleResult(Request $request): JsonResponse
    {
        Log::info('M-Pesa B2C Result Callback', $request->all());
        
        if (isset($request->Result)) {
            $result = $request->Result;
            $resultCode = $result['ResultCode'];
            
            if ($resultCode == 0) {
                $this->handleSuccessfulPayment($result);
            } else {
                $this->handleFailedPayment($result);
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    public function handleTimeout(Request $request): JsonResponse
    {
        Log::warning('M-Pesa B2C Timeout Callback', $request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    protected function handleSuccessfulPayment(array $result): void
    {
        try {
            if (isset($result['ResultParameters']['ResultParameter'])) {
                $transactionData = $result['ResultParameters']['ResultParameter'];
                $details = [];
                
                foreach ($transactionData as $param) {
                    $details[$param['Key']] = $param['Value'];
                }
                
                $transactionId = $result['TransactionID'] ?? null;
                $receipt = $details['TransactionReceipt'] ?? null;
                
                if ($transactionId) {
                    $payment = Payment::where('transaction_id', $transactionId)->first();
                    
                    if ($payment) {
                        $payment->update([
                            'payment_status' => 'completed',
                            'mpesa_receipt_number' => $receipt,
                            'mpesa_response' => array_merge(
                                $payment->mpesa_response ?? [],
                                ['callback_result' => $result]
                            )
                        ]);
                        Log::info("Payment completed for transaction: {$transactionId}");
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing successful payment: ' . $e->getMessage());
        }
    }

    protected function handleFailedPayment(array $result): void
    {
        $transactionId = $result['TransactionID'] ?? null;
        $resultDesc = $result['ResultDesc'] ?? 'Unknown error';
        
        Log::error("Payment failed - TransactionID: {$transactionId}, Reason: {$resultDesc}");

        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            
            if ($payment) {
                $payment->update([
                    'payment_status' => 'failed',
                    'mpesa_response' => array_merge(
                        $payment->mpesa_response ?? [],
                        ['callback_result' => $result, 'failed_at' => now()]
                    )
                ]);
                Log::error("Payment failed for transaction: {$transactionId}");
            }
        }
    }
}