<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        $all_stores = Store::all();
        return view('admins.index', compact('all_stores'));
    }

    public function show($id){
        $store = Store::with('user')->findOrFail($id);
        return view('admins.show', compact('store'));
    }
}
