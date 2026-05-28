<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Split;

final class DashboardController extends Controller
{
    public function index()
    {
        return Split::respond(component: 'Dashboard');
    }
}
