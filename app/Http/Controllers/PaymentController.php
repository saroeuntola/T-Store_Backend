<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\PayWayService;

class PaymentController extends Controller
{
    protected $payWayService;
    public function __construct(PayWayService $payWayService){
        $this->payWayService =$payWayService;
    }
    public function checkoutWithAba(Request $request)
{    $reqTime = now()->setTimezone('UTC')->format('YmdHis');
    $merchantId = config('payway.merchant_id');
    $tranId = 'INV-' . time();
    $paymentOption = 'abapay';
    $cartItems = $request->input('cartItems', []);
    $amount = number_format($request->amount, 2, '.', '');

    // Sanitize & encode items
    $itemsFormatted = array_map(function ($item) {
        return [
            'name' => $item['name'],
            'quantity' => (int) $item['quantity'],
            'price' => (float) $item['price'],
        ];
    }, $cartItems);

    $encodedItems = base64_encode(json_encode($itemsFormatted));
 $hash = $this->payWayService->getHash(
      $reqTime . $merchantId . $tranId . $amount . $paymentOption .  $encodedItems
 );

    $response = Http::asMultipart()->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase', [
        ['name' => 'req_time', 'contents' => $reqTime],
        ['name' => 'merchant_id', 'contents' => $merchantId],
        ['name' => 'tran_id', 'contents' => $tranId],
        ['name' => 'amount', 'contents' => $amount],
        ['name' => 'payment_option', 'contents' => $paymentOption],
        ['name' => 'items', 'contents' => $encodedItems],
    ]);

    return response()->json($response->json());
}



// public function checkoutWithAba(Request $request)
// {
//     $merchantId = env('ABA_MERCHANT_ID');
//     $tranId = 'INV-' . time(); // unique invoice
//     $reqTime = now()->setTimezone('UTC')->format('YmdHis');
//     $amount = number_format($request->amount, 2, '.', '');
//     $returnUrl = route('payment.success');
//     $cancelUrl = route('payment.cancel');
//     $continueUrl = route('payment.complete');

//     $items = base64_encode(json_encode([
//         ["name" => "Product 1", "quantity" => 1, "price" => 1.00],
//         ["name" => "Product 2", "quantity" => 2, "price" => 2.50],
//     ]));

//     // Hash string format: merchant_id + tran_id + req_time + amount + return_url + hash_secret
//     $stringToHash = $merchantId . $tranId . $reqTime . $amount . $returnUrl;
//     $hash = hash('sha256', $stringToHash);

//     $response = Http::asMultipart()->post('https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase', [
//         ['name' => 'req_time', 'contents' => $reqTime],
//         ['name' => 'merchant_id', 'contents' => $merchantId],
//         ['name' => 'tran_id', 'contents' => $tranId],
//         ['name' => 'amount', 'contents' => $amount],
//         ['name' => 'items', 'contents' => $items],
//         ['name' => 'return_url', 'contents' => $returnUrl],
//         ['name' => 'cancel_url', 'contents' => $cancelUrl],
//         ['name' => 'continue_success_url', 'contents' => $continueUrl],
//         ['name' => 'currency', 'contents' => 'USD'], // optional
//         ['name' => 'payment_option', 'contents' => 'abapay'],
//         ['name' => 'hash', 'contents' => $hash],
//     ]);

//     return response()->json($response->json());
// }

}
