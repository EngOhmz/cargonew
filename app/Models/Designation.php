<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $table = 'designations';

    protected $fillable = [
        'name','status','department_id'
    ];

public function department(){
    
        return $this->belongsTo('App\Models\Department','department_id');
      }
}
