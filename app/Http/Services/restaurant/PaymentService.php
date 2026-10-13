<?php


namespace App\Http\Services\restaurant;

use App\Models\restaurant\Order;
use App\Models\restaurant\Restaurant;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\OrderTypeStatus;
use App\Notifications\OrderCreated;
use Illuminate\Support\Facades\Session;
use App\Notifications\NewShopOrderCreated;


class PaymentService
{
    public $data = array();

    public function payment($paymetSuccess)
    {

        $restaurant = Restaurant::find(session('session_cart_restaurant_id'));
        $request = session()->get('checkoutRequest');


        $items = [];
        if (!blank(session()->get('cart'))) {
            $i                      = 0;
            $menuItemVariationId = 0;
            $variation              = [];
            $options                = [];
            foreach (session()->get('cart')['items'] as $cart) {
                if (isset($cart['variation']) && !empty($cart['variation'])) {
                    $menuItemVariationId = $cart['variation']['id'];
                    $variation = $cart['variation'];
                }

                if (isset($cart['options']) && !empty($cart['options'])) {
                    $options = $cart['options'];
                }
                $instructions = "";
                if (isset($cart['instructions'])) {
                    $instructions = $cart['instructions'];
                }
                $items[$i] = [
                    'restaurant_id'          => $restaurant->id,
                    'menu_item_variation_id' => $menuItemVariationId,
                    'menu_item_id'           => $cart['menuItem_id'],
                    'unit_price'             => (float) $cart['price'],
                    'quantity'               => (int) $cart['qty'],
                    'discounted_price'       => (float) $cart['discount'],
                    'variation'              => $variation,
                    'options'                => $options,
                    'instructions'           => $instructions,
                ];
                $i++;
            }
        }

        if ($request['payment_type'] == PaymentMethod::STRIPE && $paymetSuccess) {
            $this->data['paid_amount']    = session()->get('cart')['totalAmount'] + $restaurant->delivery_charge;
            $this->data['payment_method'] = $request['payment_type'];
            $this->data['payment_status'] = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::WALLET) {

            $this->data['paid_amount']    = session()->get('cart')['totalAmount'] + $restaurant->delivery_charge;
            $this->data['payment_method'] = $request['payment_type'];
            $this->data['payment_status'] = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PAYSTACK && $paymetSuccess) {

            $this->data['paid_amount']           = session()->get('cart')['totalAmount'] + $restaurant->delivery_charge;
            $this->data['payment_method']       = $request['payment_type'];
            $this->data['payment_status']        = PaymentStatus::PAID;
        } elseif ($request['payment_type'] == PaymentMethod::PAYPAL && $paymetSuccess) {

            $this->data['paid_amount']           = session()->get('cart')['totalAmount'] + $restaurant->delivery_charge;
            $this->data['payment_method']       = $request['payment_type'];
            $this->data['payment_status']        = PaymentStatus::PAID;
        }elseif ($request['payment_type'] == PaymentMethod::RAZORPAY && $paymetSuccess) {
            $this->data['paid_amount']           = session()->get('cart')['totalAmount'] + $restaurant->delivery_charge;
            $this->data['payment_method']       = $request['payment_type'];
            $this->data['payment_status']        = PaymentStatus::PAID;
        } else {
            $this->data['paid_amount']    = 0;
            $this->data['payment_method'] = PaymentMethod::CASH_ON_DELIVERY;
            $this->data['payment_status'] = PaymentStatus::UNPAID;
        }


        $this->data['coupon_id']     = session()->get('cart')['couponID'];
        $this->data['coupon_amount'] = session()->get('cart')['coupon_amount'];

        $this->data['items']           = $items;
        $this->data['order_type']      = OrderTypeStatus::DELIVERY;
        $this->data['restaurant_id']   = session('session_cart_restaurant_id');
        $this->data['user_id']         = auth()->user()->id;
        $this->data['total']           = session()->get('cart')['totalAmount'];
        $this->data['delivery_charge'] = $restaurant->delivery_charge;
        $this->data['address']         = $request['address'];
        $this->data['mobile']          = $request['mobile'];

        $orderService = app(OrderService::class)->order($this->data);
        return $orderService;
    }
}
