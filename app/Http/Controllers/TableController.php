<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $tables = Table::where('is_active', true)
            ->where('user_id', $user->id) // 👈 ここで店舗ユーザーのみに絞る
            ->withCount(['orders as open_count' => function ($q) {
                $q->where('status', 'open');
            }])
            ->orderBy('number')
            ->get();

        return view('managers.tables.tables', compact('tables'));
    }
}
