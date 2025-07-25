<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class HistoryExport implements FromQuery, WithHeadings
{
    protected $id;
    protected $startDate;
    protected $endDate;

    public function __construct($id, $startDate, $endDate)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        // Construa a consulta para obter os dados
        return DB::table('action_logs as al')
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')
            ->select(
                'al.id as log_id',
                DB::raw("
                    CASE 
                        WHEN al.action_type = 'checkin' THEN 'Entrada'
                        WHEN al.action_type = 'checkout' THEN 'Saída'
                        ELSE al.action_type 
                    END as action_type
                "), // Traduz "checkin" e "checkout" para "Entrada" e "Saída"
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as responsible_name"), // Nome completo do responsável
                'al.note as action_note', // Nota da ação
                'al.created_at as action_date' // Data da ação
            )
            ->where('al.target_id', $this->id)
            ->when($this->startDate, function ($query) {
                return $query->where('al.created_at', '>=', $this->startDate . ' 00:00:00');
            })
            ->when($this->endDate, function ($query) {
                return $query->where('al.created_at', '<=', $this->endDate . ' 23:59:59');
            })
            ->orderBy('al.created_at', 'desc'); // Ordenação por data de ação
    }

    public function headings(): array
    {
        return [
            'ID do Log',
            'Tipo de Ação', // "Entrada" ou "Saída"
            'Responsável JFPN', // Nome completo do responsável
            'Nota da Ação', // Informação adicional sobre a ação
            'Data da Ação', // Data e hora
        ];
    }
}
