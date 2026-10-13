<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PacelHistory22 extends Model
{
    use HasFactory;
    
    protected $table = "pacel_histories22";

    protected $guarded = ['id'];


    public function customerPacel(){

        return $this->belogsTo('App\Models\Customer\Customer','pacel_id');
    }
}
