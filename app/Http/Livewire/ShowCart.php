<?php

namespace App\Http\Livewire;


use App\Models\restaurant\MenuItem;
use App\Models\restaurant\MenuItemOption;
use App\Models\restaurant\MenuItemVariation;
use Livewire\Component;

class ShowCart extends Component
{
    public $restaurant;
    public $menuItem;
    public $quantity=1;
    public $menu_id;
    public $restaurant_id;
    public $variationID;
    public $options=[];
    public $instructions;
    protected $listeners = ['CartModal'];

    public function addItemQty(){
        $this->quantity++;
    }
    public function removeItemQty()
    {
        $this->quantity--;

    }
    public function submit($menu_id){
        // $this->restaurant_id = $restaurant_id;
        $this->menu_id = $menu_id;
        // session()->put('session_cart_restaurant_id', $restaurant_id);
        // session()->put('session_cart_restaurant', $this->restaurant->slug);
        $variationArray = [];
        $variationId    = null;
        if((int)$this->variationID) {
            $variations              = MenuItemVariation::find($this->variationID);
            $variationArray['id']    = $variations->id;
            $variationArray['name']  = $variations->name;
            $variationArray['price'] = $variations->price - $variations->discount_price;
            $variationId             = $variations->id;
            $totalPrice              = $variationArray['price'];
            $discount                = $variations->discount_price;
        } else {
            $totalPrice = $this->menuItem->unit_price - $this->menuItem->discount_price;
            $discount   = $this->menuItem->discount_price;
        }


        $optionArray = [];
        if(!blank($this->options)) {
            $options = MenuItemOption::whereIn('id', $this->options)->get();
            $i       = 0;
            foreach($options as $option) {
                $optionArray[$i]['id']    = $option->id;
                $optionArray[$i]['name']  = $option->name;
                $optionArray[$i]['price'] = $option->price;
                $i++;
                $totalPrice += $option->price;
            }
        }
        $instructions = !blank($this->instructions) ? $this->instructions : "";
        $cartItem     = [
            'id'      => $menu_id,
            'name'    => $this->menuItem->name,
            'qty'     => $this->quantity,
            'price'   => $totalPrice,
            'delivery_charge'  => 0,
            'options'       => $optionArray,
            'variation'     => $variationArray,
            'discount'      => $discount,
            'restaurant_id' => $this->menuItem->restaurant_id,
            'images'        => $this->menuItem->images,
            'menuItem_id'   => $this->menuItem->id,
            'variationID'   => $variationId,
            'instructions'  => $instructions

        ];
        $this->emit('addCart',$cartItem);
        $this->quantity = 1;
        $this->instructions = '';
        $this->variationID = null;
        $this->options = null;
        $this->dispatchBrowserEvent('closeFormModalCart');

    }
    public function CartModal($itemID)
    {
        $this->menuItem = MenuItem::with('variations')->with('options')->where(['id' => $itemID])->first();
    }

    public function render()
    {
        return view('livewire.show-cart');
    }
}
