<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page
     */
    public function index(Request $request)
    {
        $user = $request->user();
        return view('profile.index', compact('user'));
    }
}
