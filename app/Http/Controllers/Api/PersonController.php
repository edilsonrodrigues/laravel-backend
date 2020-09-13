<?php

namespace App\Http\Controllers\Api;

use App\Models\Person;
use App\Http\Controllers\Controller;

class PersonController extends Controller
{

    public function __construct()
    {
        $this->class = new Person();
    }
   
}