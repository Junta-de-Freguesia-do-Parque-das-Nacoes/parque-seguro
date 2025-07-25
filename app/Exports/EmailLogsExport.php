<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

class EmailLogsExport implements FromQuery
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
{
    $query = DB::table('email_logs');

    // Aplicar filtros
    if (!empty($this->filters['email'])) {
        $query->where('email', 'like', '%' . $this->filters['email'] . '%');
    }

    if (!empty($this->filters['status'])) {
        $query->where('status', $this->filters['status']);
    }

    if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
        $query->whereBetween('sent_at', [$this->filters['start_date'], $this->filters['end_date']]);
    } elseif (!empty($this->filters['start_date'])) {
        $query->whereDate('sent_at', '>=', $this->filters['start_date']);
    } elseif (!empty($this->filters['end_date'])) {
        $query->whereDate('sent_at', '<=', $this->filters['end_date']);
    }

    // Ordenar pelos mais recentes primeiro
    $query->orderBy('sent_at', 'desc');

    return $query->select('id', 'email', 'subject', 'status', 'sent_at', 'body');
}


}

