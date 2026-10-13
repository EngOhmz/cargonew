<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use App\Libraries\MyString;

use Pesapal;

//use   Storewid\Pesapal;

use Illuminate\Http\Request;

class PesapalController extends Controller
{

    public function index(Request $request){

        echo  $request->pesapal_transaction_tracking_id;
       
    }
    //
    //
    public function store(){

        $callbacUrl = URL::to('')."/pesapalResonse";

  
         MyString::setEnv('PESAPAL_CONSUMER_KEY','BQRCwbtaVO7uKdxDPQi/HXQ/Lk9oh3Me');
         MyString::setEnv('PESAPAL_CONSUMER_SECRET',"1+qbNhG7qirv0ElpgQ1d0I0ernw=");
        MyString::setEnv('PESAPAL_API_URL','https://demo.pesapal.com/api/PostPesapalDirectOrderV4');

        MyString::setEnv('PESAPAL_CALLBACK_URL',$callbacUrl);
        



        //Pesapal::make_payment("customerfirstname","customerlastname","customerlastname","amount","transaction_id");
      $res=Pesapal::makepayment("samwel","1000","herman","epmnzava@gmail.com","MERCHANT","453f4f4343" ,"transacto","0715438485");
       
      echo  $res;
    
    }
    public function customer_makepayment(){
        $key = "BQRCwbtaVO7uKdxDPQi/HXQ/Lk9oh3Me";
        $secret = "1+qbNhG7qirv0ElpgQ1d0I0ernw=";
        $endpoint = "https://demo.pesapal.com/api/PostPesapalDirectOrderV4";
        $currency = "TZS";
        $callback = URL::to('')."/pesapalResonse";

        $payment=new Pesapal($key, $secret, $endpoint, $currency, $callback,null);
  
       $response=$payment->processpayment($firstname, $lastname, $phone_number, $email, $amount, $description, $reference, $type = "MERCHANT");
       $response=$payment->processpayment($firstname="samwel", $lastname="herman", $phone_number="0715438485", $email="samwelherman85@gmail.com", $amount=1000, $description="ww", $reference="12we", $type = "MERCHANT");
  
     //response will be an iframe
      echo  $response;
  
      }
  
}
