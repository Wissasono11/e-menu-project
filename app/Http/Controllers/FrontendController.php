<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request) {
        $store = User::where('username', $request->username)->first();

        if(!$store) {
            abort(404);
        }

        return view('pages.index', compact('store'));
    }
}
