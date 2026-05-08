<?php

namespace App\Http\Controllers;

use App\Facades\Split;

class DashboardController extends Controller
{
    public function index()
    {
        return Split::respond(component: 'Dashboard');
    }
}
