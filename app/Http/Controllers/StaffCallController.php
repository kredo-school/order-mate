<?php

namespace App\Http\Controllers;

use App\Models\StaffCall;
use App\Models\Table;
use Illuminate\Http\Request;

class StaffCallController extends Controller
{
    // Guest が Yes を押したとき
    public function store($storeName, $tableUuid)
    {
        $table = Table::where('uuid', $tableUuid)->firstOrFail();

        StaffCall::create([
            'table_id' => $table->id,
        ]);

        return view('guests.call-complete', compact('storeName', 'tableUuid'))
               ->with('isGuestPage', true);
    }

    // Manager 側で最新5件取得
    public function index()
    {
        $calls = StaffCall::where('is_read', false)
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        return response()->json($calls);
    }

    // Manager がタップして既読にする
    public function markAsRead(StaffCall $staffCall)
    {
        $staffCall->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
