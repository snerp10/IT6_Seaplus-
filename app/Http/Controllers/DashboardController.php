<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Delivery;
use App\Models\SalesReport;


class DashboardController extends Controller
{
    public function index()
    {
        return view('customer.dashboard.index');
    }
}
