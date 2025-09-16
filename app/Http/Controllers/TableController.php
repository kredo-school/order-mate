<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index()
    {
        $tables = Table::where('is_active', true)
        ->withCount(['orders as history_count' => function ($q) {
            $q->where('status', 'open');
        }])
        ->get();
    
    dd($tables->toArray());
    
    
        return view('managers.tables.index', compact('tables'));
    }
    
}
