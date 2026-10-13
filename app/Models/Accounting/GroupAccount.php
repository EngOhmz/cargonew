<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Model;

class GroupAccount extends Model
{
    /**
     * Use the gl_account_group table (not gl_account_groupOld).
     */
    protected $table = 'gl_account_group';

    protected $fillable = [
        'name',
        'class',
        'type',
        'group_id',
        'order_no',
        'added_by',
    ];

    protected $guarded = ['id'];
}
