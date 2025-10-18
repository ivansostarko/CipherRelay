<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class LandingController extends Controller
{
    public function index()
    {
        return view('user.landing');
    }
}
