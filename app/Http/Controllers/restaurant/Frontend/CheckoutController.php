<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\CouponType;
use App\Enums\DiscountType;
use Paystack;
use App\Models\Order;
use Razorpay\Api\Api;
use App\Models\Restaurant;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use App\Enums\OrderTypeStatus;
use App\Http\Services\OrderService;
use App\Notifications\OrderCreated;
use App\Http\Services\StripeService;
use App\Http\Services\PaymentService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Notifications\NewShopOrderCreated;
use App\Http\Controllers\FrontendController;
use App\Models\Coupon;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class CheckoutController extends FrontendController
{
    public function __construct()
    {
        parent::__construct();

        $this->data['site_title'] = 'Frontend';
    }

    public function index()
    {
        if (blank(session()->get('cart'))) {
            return redirect('/');
        }
        $this->data['menuitems'] = session()->get('cart');
        $this->data['restaurant'] = Restaurant::find(session('session_cart_restaurant_id'));
        return view('frontend.restaurant.checkout', $this->data);
    }

    public function store(Request $request)
    {
        $payment        = null;
        $sessionRestaurantId = session('session_cart_restaurant_id');

        if ($sessionRestaurantId > 0) {
            $restaurant = Restaurant::find(session('session_cart_restaurant_id'));

            $validation = [
                'mobile'       => 'required|regex:/^([0-9\s\-\+\(\)]*)$/',
                'address'      => 'required|string',
                'payment_type' => 'required|numeric',
            ];


            $validator = Validator::make($request->all(), $validation);
            $validator->after(function ($validator) use ($request, $restaurant) {
                if ($request->payment_type == PaymentMethod::WALLET) {
                    if ((float) auth()->user()->balance->balance < (float) (session()->get('cart')['totalAmount'] + $restaurant->delivery_charge)) {
                        $validator->errors()->add('payment_type', 'The Credit balance does not enough for this payment.');
                    }
                }
            })->validate();

            if (auth()->check()) {
                session()->put('checkoutRequest', $request->all());

                if ($request->payment_type == PaymentMethod::STRIPE) {
                    $orderService = $this->StirpeCallback($restaurant);

                    if ($orderService->status) {
                        $order = Order::find($orderService->order_id);
                        session()->put('cart', null);
                        session()->put('checkoutRequest', null);
                        session()->put('session_cart_restaurant_id', 0);
                        session()->put('session_cart_restaurant', null);
                        try {
                            auth()->user()->notify(new OrderCreated($order));
                            $order->restaurant->user->notify(new NewShopOrderCreated($order));
                        } catch (\Exception $exception) {
                        }

                        return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
                    } else {
                        return redirect(route('checkout.index'))->withError($orderService->message);
                    }
                } elseif ($request->payment_type == PaymentMethod::PAYSTACK) {

                    $request->request->add([
                        'currency'    => env('PAYSTACK_CURRENCY'),
                        'amount'      => (session()->get('cart')['totalAmount'] + $restaurant->delivery_charge) * 100,
                        'email'       => auth()->user()->email,
                        'metadata'    => json_encode($array = ['key_name' => 'value',]),
                        'reference'   => Paystack::genTranxRef(),
                        '_token'      => csrf_token(),
                    ]);

                    try {
                        return Paystack::getAuthorizationUrl()->redirectNow();
                    } catch (\Exception $e) {
                        return Redirect::back()->withMessage(['msg' => 'The paystack token has expired. Please refresh the page and try again.', 'type' => 'error']);
                    }
                } elseif ($request->payment_type == PaymentMethod::PAYPAL) {

                    $provider = new PayPalClient;
                    $provider->setApiCredentials(config('paypal'));
                    $paypalToken = $provider->getAccessToken();

                    $response = $provider->createOrder([
                        "intent" => "CAPTURE",
                        "application_context" => [
                            "return_url" => route('successTransaction'),
                            "cancel_url" => route('cancelTransaction'),
                        ],
                        "purchase_units" => [
                            0 => [
                                "amount" => [
                                    "currency_code" => setting('currency_name'),
                                    "value" => session()->get('cart')['totalAmount'] + $restaurant->delivery_charge
                                ]
                            ]
                        ]
                    ]);

                    if (isset($response['id']) && $response['id'] != null) {

                        // redirect to approve href
                        foreach ($response['links'] as $links) {
                            if ($links['rel'] == 'approve') {
                                return redirect()->away($links['href']);
                            }
                        }

                        return redirect(route('checkout.index'))->withError('You have canceled the transaction.');
                    } else {
                        return redirect(route('checkout.index'))->withError('You have canceled the transaction.');
                    }
                } elseif ($request->payment_type == PaymentMethod::RAZORPAY) {

                    $input = $request->all();
                    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

                    $payment = $api->payment->fetch($input['razorpay_payment_id']);

                    if (count($input)  && !empty($input['razorpay_payment_id'])) {
                        try {
                            $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                            if ($response['status'] == 'captured') {
                                $orderService = app(PaymentService::class)->payment(true);
                            } else {
                                $orderService = app(PaymentService::class)->payment(false);
                            }
                            if ($orderService->status) {
                                $order = Order::find($orderService->order_id);
                                session()->put('cart', null);
                                session()->put('checkoutRequest', null);
                                session()->put('session_cart_restaurant_id', 0);
                                session()->put('session_cart_restaurant', null);
                                try {
                                    auth()->user()->notify(new OrderCreated($order));
                                    $order->restaurant->user->notify(new NewShopOrderCreated($order));
                                } catch (\Exception $exception) {
                                    //
                                }
                                
                                return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
                            } else {
                                return redirect(route('checkout.index'))->withError($orderService->message);
                            }
                        } catch (\Exception $e) {
                            return redirect(route('checkout.index'))->withError($e->getMessage());
                        }
                    }
                } else {
                    $orderService = app(PaymentService::class)->payment(false);
                    if ($orderService->status) {
                        $order = Order::find($orderService->order_id);
                        session()->put('cart', null);
                        session()->put('checkoutRequest', null);
                        session()->put('session_cart_restaurant_id', 0);
                        session()->put('session_cart_restaurant', null);
                        try {
                            auth()->user()->notify(new OrderCreated($order));
                            $order->restaurant->user->notify(new NewShopOrderCreated($order));
                        } catch (\Exception $exception) {
                            //
                        }

                        return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
                    } else {
                        return redirect(route('checkout.index'))->withError($orderService->message);
                    }
                }
            } else {
                return redirect()->route('login');
            }
        } else {
            return redirect(route('checkout.index'))->withError('The shop not found');
        }
    }

    public function StirpeCallback($restaurant)
    {

        $stripeService    = new StripeService();
        $stripeParameters = [
            'amount'      => session()->get('cart')['totalAmount'] + $restaurant->delivery_charge,
            'currency'    => 'USD',
            'token'       => request('stripeToken'),
            'description' => 'N/A',
        ];

        $payment = $stripeService->payment($stripeParameters);

        if (is_object($payment) && $payment->isSuccessful()) {
            $orderService = app(PaymentService::class)->payment(true);
        } else {
            $orderService = app(PaymentService::class)->payment(false);
        }
        return $orderService;
    }

    public function PaystackCallback()
    {
        $payment = Paystack::getPaymentData();

        if ($payment['status'] && $payment['data']['status'] == 'success') {

            $orderService = app(PaymentService::class)->payment(true);
        } else {
            $orderService = app(PaymentService::class)->payment(false);
        }

        if ($orderService->status) {
            $order = Order::find($orderService->order_id);
            session()->put('cart', null);
            session()->put('checkoutRequest', null);
            session()->put('session_cart_restaurant_id', 0);
            session()->put('session_cart_restaurant', null);
            try {
                auth()->user()->notify(new OrderCreated($order));
                $order->restaurant->user->notify(new NewShopOrderCreated($order));
            } catch (\Exception $exception) {
                //
            }

            return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
        } else {
            return redirect(route('checkout.index'))->withError($orderService->message);
        }
    }


    public function paypalSuccessTransaction(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request['token']);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $orderService = app(PaymentService::class)->payment(true);
        } else {
            $orderService = app(PaymentService::class)->payment(false);
        }

        if ($orderService->status) {
            $order = Order::find($orderService->order_id);
            session()->put('cart', null);
            session()->put('checkoutRequest', null);
            session()->put('session_cart_restaurant_id', 0);
            session()->put('session_cart_restaurant', null);
            try {
                auth()->user()->notify(new OrderCreated($order));
                $order->restaurant->user->notify(new NewShopOrderCreated($order));
            } catch (\Exception $exception) {
                //
            }

            return redirect(route('account.order.show', $order->id))->withSuccess('You order completed successfully.');
        } else {
            return redirect(route('checkout.index'))->withError($orderService->message);
        }
    }

    public function paypalCancelTransaction(Request $request)
    {
        return redirect(route('checkout.index'))->withError('You have canceled the transaction.');
    }
}
