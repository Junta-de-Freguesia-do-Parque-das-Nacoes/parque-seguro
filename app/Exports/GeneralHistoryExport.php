<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GeneralHistoryExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retorna os dados filtrados para exportação.
     */
    public function collection()
    {
        $query = DB::table('action_logs as al')
            ->leftJoin('assets as a', 'al.target_id', '=', 'a.id') // Utentes
            ->leftJoin('users as u', 'al.user_id', '=', 'u.id')    // Responsáveis
            ->select(
                'al.id as log_id',
                'a.name as utente_name',
                'a.asset_tag as utente_code',
                DB::raw("
                    CASE 
                        WHEN al.action_type = 'checkin' THEN 'Entrada'
                        WHEN al.action_type = 'checkout' THEN 'Saída'
                        ELSE al.action_type
                    END as action_type
                "), // Traduz "checkin" e "checkout" para "Entrada" e "Saída"
                DB::raw("CONCAT(u.first_name, ' ', u.last_name) as responsible"),
                'al.note as action_note',
                'al.created_at as action_date'
            )
            ->whereIn('al.action_type', ['checkin', 'checkout']);

        // Aplicar filtros
        if (!empty($this->filters['asset_name'])) {
            $query->where('a.name', 'like', '%' . $this->filters['asset_name'] . '%');
        }

        if (!empty($this->filters['start_date'])) {
            $query->where('al.created_at', '>=', $this->filters['start_date'] . ' 00:00:00');
        }

        if (!empty($this->filters['end_date'])) {
            $query->where('al.created_at', '<=', $this->filters['end_date'] . ' 23:59:59');
        }

        if (!empty($this->filters['action_type'])) {
            $query->where('al.action_type', $this->filters['action_type']);
        }

        return $query->orderBy('al.created_at', 'desc')->get();
    }

    /**
     * Define os cabeçalhos da planilha.
     */
    public function headings(): array
    {
        return [
            'ID do Log',
            'Nome do Utente',
            'Código do Utente',
            'Tipo de Ação', // Agora traduzido como "Entrada" ou "Saída"
            'Responsável', // Nome completo do responsável
            'Nota', // Informação adicional
            'Data da Ação', // Data e hora da ação
        ];
    }
}
