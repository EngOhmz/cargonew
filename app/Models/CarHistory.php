<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarHistory extends Model
{
    use HasFactory;

    protected $table = 'car_history';


    protected $guarded = ['id'];

    public function car(){

        return $this->belongsTo('App\Models\Management\Car','car_id');
    }

    public function driver(){

        return $this->belongsTo('App\Models\Management\Driver','driver_id');
    }

    public function user(){

        return $this->belongsTo('App\Models\User','user_id');
    }
}
