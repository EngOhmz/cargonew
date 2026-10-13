<?php

namespace App\Http\Controllers\restaurant;

use App\Http\Controllers\Controller;

class BackendController extends Controller
{
    public $data = [];

    public function __construct()
    {
        $this->data['siteTitle'] = 'Dashboard';
    }
}

