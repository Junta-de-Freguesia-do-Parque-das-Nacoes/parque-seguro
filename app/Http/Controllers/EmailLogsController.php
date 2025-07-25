<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EmailLogsExport;
use Carbon\Carbon;


class EmailLogsController extends Controller
{
    public function index(Request $request)
{
    $query = DB::table('email_logs');

    // Aplicando filtros
    if ($request->filled('email')) {
        $query->where('email', 'like', '%' . $request->email . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filtros de Data
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = Carbon::parse($request->start_date)->startOfDay(); // Início do dia
        $endDate = Carbon::parse($request->end_date)->endOfDay(); // Final do dia
        $query->whereBetween('sent_at', [$startDate, $endDate]);
    } elseif ($request->filled('start_date')) {
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $query->whereDate('sent_at', '>=', $startDate);
    } elseif ($request->filled('end_date')) {
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $query->whereDate('sent_at', '<=', $endDate);
    }

    $emailLogs = $query->orderBy('sent_at', 'desc')->paginate(10);

    return view('email_logs.index', compact('emailLogs'));
}


public function export(Request $request)
{
    $filters = $request->only(['email', 'status', 'start_date', 'end_date']);

    return Excel::download(new EmailLogsExport($filters), 'logs_emails.xlsx');
}

public function autocomplete(Request $request)
{
    $term = $request->get('query', '');

    $emails = DB::table('email_logs')
        ->select('email')
        ->where('email', 'like', '%' . $term . '%')
        ->groupBy('email')
        ->limit(10)
        ->get();

    return response()->json($emails);
}

}