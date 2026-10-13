<?php

namespace App\Http\Controllers\restaurant;

use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public $data = [];

    public function __construct()
    {
        $this->data['site_title'] = 'Home';
    }
}