<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Protected dashboard data',
            'user' => $request->user(),
            'timestamp' => now(),
        ]);
    }
}

