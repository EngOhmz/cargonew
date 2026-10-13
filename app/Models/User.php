<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\post;
use Laravel\Sanctum\HasApiTokens;
use App\Permissions\HasPermissionsTrait;

// for restaurant start
use App\Enums\BalanceType;
use App\Models\Address;
use App\Models\Management\Car;
use App\Models\Company;
use App\Models\restaurant\Balance;
use App\Models\restaurant\Bank;
use App\Models\restaurant\DeliveryBoyAccount;
use App\Models\restaurant\Order;
use App\Models\restaurant\Reservation;
use App\Models\restaurant\Restaurant;
use App\Models\restaurant\UserDeposit;
use App\Models\restaurant\Waiter;
use App\Presenters\CustomerPresenter;
use App\Presenters\InvoicePresenter;
use Shipu\Watchable\Traits\HasModelEvents;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
// use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
//for restaurant end
class User extends Authenticatable 
{

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'visitor_id',
        'is_active',
        'is_driver',
        'is_delivery_agent',
        'is_approved',
        'plain_password',
        'member_id',
        'company_id',
        'password',
        'added_by',
        'status',
        'department_id',
        'designation_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_driver' => 'integer',
        'is_delivery_agent' => 'integer',
    ];
    public function cars()
      {
          return $this->belongsToMany(Car::class);
      }


    public function posts()
    {
        return $this->hasMany('App\Models\post');
    }
    public function farmer()
    {
        return $this->hasMany('App\Models\Farmer');
    }
    public function group()
    {
        return $this->hasMany('App\Models\Group');
    }
    public function land()
    {
        return $this->hasMany('App\Models\FarmLand');
    }
    public function supply()
    {
        return $this->hasMany('App\Models\Supply');
    }
    public function unit()
    {
        return $this->hasMany('App\Models\Unit');
    }
    public function product()
    {
        return $this->hasMany('App\Models\Product');
    }
  
    public function Warehouse()
    {
        return $this->hasMany('App\Models\Warehouse','id');
    }
    
       public function basic_details()
    {
        return $this->hasOne('App\Models\UserDetails\BasicDetails','user_id');
    }

    public function bank_details()
    {
        return $this->hasOne('App\Models\UserDetails\BankDetails','user_id');
    }
    
    public function designation(){
    
        return $this->belongsTo('App\Models\Designation','designation_id');
      }
      public function department(){
    
        return $this->belongsTo('App\Models\Department','department_id');
      }

      public function company()
      {
          return $this->belongsTo(Company::class, 'company_id');
      }

      //for restaurant start
      public function orders()
      {
          return $this->hasMany(Order::class);
      }
  
      public function reservations()
      {
          return $this->hasMany(Reservation::class);
      }
  
      public function restaurants()
      {
          return $this->hasMany(Restaurant::class);
      }
  
      public function addresses()
      {
          return $this->hasMany(Address::class);
      }
  
      public function deliveryBoyAccount()
      {
          return $this->hasOne(DeliveryBoyAccount::class, 'user_id', 'id');
      }
  
      public function waiter()
      {
          return $this->hasOne(Waiter::class, 'user_id', 'id');
      }
  
      public function restaurant()
      {
          return $this->hasOne(Restaurant::class);
      }
  
      public function balance()
      {
          return $this->belongsTo(Balance::class);
      }
  
    //   public function getImageAttribute()
    //   {
    //       if (!empty($this->getFirstMediaUrl('user'))) {
    //           return asset($this->getFirstMediaUrl('user'));
    //       }
    //       return asset('themes/images/user-avatar.png');
    //   }
  
      public function OnModelCreating()
      {
          $balance               = new Balance();
          $balance->name         = $this->email;
          $balance->type         = BalanceType::REGULAR;
          $balance->balance      = 0;
          $balance->creator_type = 1;
          $balance->creator_id   = 1;
          $balance->editor_type  = 1;
          $balance->editor_id    = 1;
          $balance->save();
  
          $this->balance_id = $balance->id;
      }
  
      public function OnModelCreated()
      {
          $deposit                 = new UserDeposit;
          $deposit->user_id        = $this->id;
          $deposit->deposit_amount = 0;
          $deposit->limit_amount   = 0;
          $deposit->save();
      }
  
      public function routeNotificationForTwilio()
      {
          return $this->phone;
      }
  
      /**
       * Route notifications for the FCM channel.
       *
       * @param  \Illuminate\Notifications\Notification  $notification
       * @return string
       */
      public function routeNotificationForFcm($notification)
      {
          return $this->device_token;
      }
  
      public function getMyroleAttribute()
      {
          return $this->roles->pluck('id', 'id')->first();
      }
  
      public function getrole()
      {
          return $this->hasOne(Role::class, 'id', 'myrole');
      }
  
      public function deposit()
      {
          return $this->hasOne(UserDeposit::class);
      }
  
      public function bank()
      {
          return $this->hasOne(Bank::class);
      }
  
      public function getMyStatusAttribute()
      {
          return trans('user_statuses.' . $this->status);
      }
  
  
      public function presentUpcomingInvoice()
      {
          if (!$invoice = $this->upcomingInvoice()) {
              return null;
          }
  
          return new InvoicePresenter($invoice->asStripeInvoice());
      }
  
      public function presentCustomer()
      {
          if (!$this->hasStripeId()) {
              return null;
          }
  
          return new CustomerPresenter($this->asStripeCustomer());
      }
  
      /**
       * Check if user's team limit reached.
       *
       * @return bool
       */
      public function productLimitReached()
      {
          return $this->shop->products->count() === $this->plan->product_limit;
      }
  
      public function usage()
      {
          return $this->shop->products->count;
      }
  

      //for restaurant end

    use HasPermissionsTrait; //Import The Trait
}
