<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponsavelUtente extends Model
{
    use HasFactory;

    // Se a tabela não for 'responsavel_utentes' (o padrão plural), você pode definir manualmente:
    protected $table = 'responsaveis_utentes';

    // Definir os campos que podem ser preenchidos
    protected $fillable = [
        'responsavel_id',
        'utente_id',
        'data_inicio_autorizacao',
        'data_fim_autorizacao',
        'grau_parentesco',
        'tipo_responsavel',
        'observacoes',
    ];
}
