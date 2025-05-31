<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Club;

class SuperAdminController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        // Get club list
        $clubs = Club::all();
        

        return view('SuperAdmin.dashboard', ['clubs' => $clubs]);
    }
}
