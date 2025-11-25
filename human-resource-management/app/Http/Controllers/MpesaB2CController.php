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

    public function handleB2CResult(Request $request): JsonResponse
    {
        Log::info('=== M-PESA B2C CALLBACK START ===', $request->all());
        
        if (isset($request->Result)) {
            $result = $request->Result;
            $resultCode = $result['ResultCode'];
            
            Log::info('Callback Result Code: ' . $resultCode);
            
            if ($resultCode == 0) {
                $this->handleSuccessfulPayment($result);
            } else {
                $this->handleFailedPayment($result);
            }
        } else {
            Log::warning('No Result found in callback payload');
        }

        Log::info('=== M-PESA B2C CALLBACK END ===');
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }

    protected function handleSuccessfulPayment(array $result): void
    {
        try {
            Log::info('Processing successful payment', $result);
            
            $transactionId = $result['TransactionID'] ?? null;
            $conversationId = $result['ConversationID'] ?? null;
            $originatorConversationId = $result['OriginatorConversationID'] ?? null;
            
            Log::info('Payment identifiers', [
                'transactionId' => $transactionId,
                'conversationId' => $conversationId,
                'originatorConversationId' => $originatorConversationId
            ]);

            // Extract transaction details
            $receipt = null;
            $transactionAmount = null;
            
            if (isset($result['ResultParameters']['ResultParameter'])) {
                $transactionData = $result['ResultParameters']['ResultParameter'];
                $details = [];
                
                foreach ($transactionData as $param) {
                    $details[$param['Key']] = $param['Value'];
                    if ($param['Key'] === 'TransactionReceipt') {
                        $receipt = $param['Value'];
                    }
                    if ($param['Key'] === 'TransactionAmount') {
                        $transactionAmount = $param['Value'];
                    }
                }
                
                Log::info('Extracted transaction details', [
                    'receipt' => $receipt,
                    'amount' => $transactionAmount
                ]);
            }

            // Find payment using multiple strategies
            $payment = $this->findPaymentRecord($transactionId, $conversationId, $originatorConversationId);
            
            if ($payment) {
                $updateData = [
                    'payment_status' => 'completed',
                    'mpesa_receipt_number' => $receipt ?: $transactionId,
                    'transaction_id' => $transactionId,
                    'mpesa_response' => array_merge(
                        $payment->mpesa_response ?? [],
                        ['callback_result' => $result, 'processed_at' => now()->toDateTimeString()]
                    ),
                    'updated_at' => now()
                ];
                
                $payment->update($updateData);
                
                Log::info('✅ Payment successfully updated', [
                    'payment_id' => $payment->id,
                    'employee_id' => $payment->employee_id,
                    'amount' => $payment->amount,
                    'transaction_id' => $transactionId,
                    'receipt' => $receipt
                ]);
            } else {
                Log::error('❌ Payment record not found', [
                    'transactionId' => $transactionId,
                    'conversationId' => $conversationId,
                    'originatorConversationId' => $originatorConversationId
                ]);
                
                // Log recent payments to help debugging
                $recentPayments = Payment::where('payment_status', 'processing')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(['id', 'employee_id', 'amount', 'transaction_id', 'mpesa_response']);
                    
                Log::info('Recent processing payments for reference', $recentPayments->toArray());
            }
            
        } catch (\Exception $e) {
            Log::error('💥 Error processing successful payment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'result' => $result
            ]);
        }
    }

    protected function findPaymentRecord($transactionId, $conversationId, $originatorConversationId)
    {
        // Strategy 1: Find by transaction_id
        if ($transactionId) {
            $payment = Payment::where('transaction_id', $transactionId)->first();
            if ($payment) {
                Log::info('Found payment by transaction_id', ['transaction_id' => $transactionId]);
                return $payment;
            }
        }
        
        // Strategy 2: Find by ConversationID in mpesa_response
        if ($conversationId) {
            $payment = Payment::where('mpesa_response->ConversationID', $conversationId)->first();
            if ($payment) {
                Log::info('Found payment by ConversationID', ['conversationId' => $conversationId]);
                return $payment;
            }
        }
        
        // Strategy 3: Find by OriginatorConversationID in mpesa_response
        if ($originatorConversationId) {
            $payment = Payment::where('mpesa_response->OriginatorConversationID', $originatorConversationId)->first();
            if ($payment) {
                Log::info('Found payment by OriginatorConversationID', ['originatorConversationId' => $originatorConversationId]);
                return $payment;
            }
        }
        
        // Strategy 4: Find by amount and recent timestamp (fallback)
        if (isset($details['TransactionAmount'])) {
            $amount = $details['TransactionAmount'];
            $payment = Payment::where('amount', $amount)
                ->where('payment_status', 'processing')
                ->where('created_at', '>=', now()->subHours(2))
                ->orderBy('created_at', 'desc')
                ->first();
                
            if ($payment) {
                Log::info('Found payment by amount and timestamp', ['amount' => $amount]);
                return $payment;
            }
        }
        
        return null;
    }

    protected function handleFailedPayment(array $result): void
    {
        $transactionId = $result['TransactionID'] ?? null;
        $conversationId = $result['ConversationID'] ?? null;
        $originatorConversationId = $result['OriginatorConversationID'] ?? null;
        $resultDesc = $result['ResultDesc'] ?? 'Unknown error';
        
        Log::error("Payment failed", [
            'transactionId' => $transactionId,
            'conversationId' => $conversationId,
            'reason' => $resultDesc
        ]);

        $payment = $this->findPaymentRecord($transactionId, $conversationId, $originatorConversationId);
        
        if ($payment) {
            $payment->update([
                'payment_status' => 'failed',
                'mpesa_response' => array_merge(
                    $payment->mpesa_response ?? [],
                    ['callback_result' => $result, 'failed_at' => now()->toDateTimeString()]
                ),
                'updated_at' => now()
            ]);
            Log::error("Payment marked as failed", ['payment_id' => $payment->id]);
        } else {
            Log::error('Payment record not found for failed transaction', [
                'transactionId' => $transactionId,
                'conversationId' => $conversationId
            ]);
        }
    }

    public function handleB2CTimeout(Request $request): JsonResponse
    {
        Log::warning('M-Pesa B2C Timeout Callback', $request->all());
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Success']);
    }
}