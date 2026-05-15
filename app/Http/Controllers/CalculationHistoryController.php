<?php

namespace App\Http\Controllers;

use App\Models\CalculationHistory;

class CalculationHistoryController extends Controller
{
    public function index()
    {
        $histories = CalculationHistory::query()
            ->latest()
            ->paginate(15);

        return view('history.index', compact('histories'));
    }
}
