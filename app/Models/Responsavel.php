<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// IMPORTANTE: Mudar de Model para Authenticatable
use Illuminate\Foundation\Auth\User as Authenticatable; // <<-- ESSENCIAL: ADICIONA ESTA LINHA
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

// O teu modelo Responsavel AGORA DEVE ESTENDER Authenticatable
class Responsavel extends Authenticatable // <<-- ALTERA ISTO: de 'Model' para 'Authenticatable'
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'responsaveis';

    protected $fillable = [
        'foto',
        'nome_completo',
        'nr_identificacao',
        'contacto',
        'email',
        'password', // <<-- IMPORTANTE: Adiciona 'password' aqui, pois é um campo que Authenticatable espera
        'adicionado_por',
        'adicionado_em',
        'modificado_por',
        'modificado_em',
        'ativo'
    ];

    // IMPORTANTE: Oculta o campo 'password' quando o modelo é serializado (por exemplo, para JSON)
    protected $hidden = [
        'password',
    ];

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    // 🔗 Relacionamentos
    // Nota: A função 'utente()' parece ser uma relação belongsTo, o que é atípico se um responsável tiver *vários* utentes.
    // A função 'utentes()' (plural) abaixo é a de muitos-para-muitos, que parece ser a principal para os educandos.
    public function utente()
    {
        return $this->belongsTo(Asset::class, 'utente_id');
    }

    public function adicionadoPor()
    {
        return $this->belongsTo(User::class, 'adicionado_por');
    }

    public function modificadoPor()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class);
    }

    // Relação com os utentes através da tabela 'responsaveis_utentes' (pivot)
    public function utentes()
    {
        return $this->belongsToMany(Asset::class, 'responsaveis_utentes', 'responsavel_id', 'utente_id')
                    ->withPivot('data_inicio_autorizacao', 'data_fim_autorizacao', 'estado_autorizacao', 'tipo_responsavel', 'grau_parentesco', 'observacoes');
    }

    public function isEncarregadoDeEducacao()
    {
        return $this->utentes()->wherePivot('tipo_responsavel', 'Encarregado de Educacao')->exists();
    }

    // Relacionamento inverso (se um utente tiver vários responsáveis)
    // Nota: O nome da função 'responsaveis()' pode ser confuso já que o nome do modelo é Responsavel.
    // Poderia ser 'responsaveisDoUtente' ou similar para maior clareza se for usada a partir de Asset.
    public function responsaveis()
    {
        return $this->belongsToMany(Responsavel::class, 'responsaveis_utentes', 'utente_id', 'responsavel_id');
    }

    public function notas()
    {
        return $this->hasMany(NotaResponsavel::class);
    }

    // 🛠 Atualiza e retorna o estado da autorização com base nas datas
    public function atualizarEstadoAutorizacao($utenteId)
    {
        $responsavelUtente = $this->utentes()->where('utente_id', $utenteId)->first();

        if (!$responsavelUtente) {
            return 'Autorizado';
        }

        $hoje = now();
        $inicio = $responsavelUtente->pivot->data_inicio_autorizacao ? Carbon::parse($responsavelUtente->pivot->data_inicio_autorizacao) : null;
        $fim = $responsavelUtente->pivot->data_fim_autorizacao ? Carbon::parse($responsavelUtente->pivot->data_fim_autorizacao) : null;

        if (!$fim) {
            return 'Autorizado';
        }

        if ($hoje->greaterThan($fim)) {
            return 'Autorizacao Expirada';
        }

        if ($inicio && $hoje->lessThan($inicio)) {
            return 'Nao Iniciado';
        }

        return 'Autorizado';
    }


    // 🚀 Registra mudanças e histórico de alterações
    protected static function boot()
{
    parent::boot();

    static::saved(function ($responsavel) {
        if (!$responsavel->id) {
            return;
        }

        // ⚠️ Impede gravação se o responsável autenticado for um EE
        if (session()->has('ee_responsavel_id')) {
            return;
        }

        $utenteId = DB::table('responsaveis_utentes')
            ->where('responsavel_id', $responsavel->id)
            ->value('utente_id');

        if (!$utenteId) {
            return;
        }

        $alteracoes = [];
        $ignorarCampos = ['updated_at', 'modificado_por', 'modificado_em', 'password'];

        foreach ($responsavel->getDirty() as $campo => $novoValor) {
            if (!in_array($campo, $ignorarCampos)) {
                $valorAntigo = $responsavel->getOriginal($campo);
                if ($valorAntigo != $novoValor) {
                    $alteracoes[] = "{$campo}: '{$valorAntigo}' → '{$novoValor}'";
                }
            }
        }

        if (!empty($alteracoes)) {
            DB::table('responsaveis_historico')->insert([
                'responsavel_id' => $responsavel->id,
                'utente_id' => $utenteId,
                'nome_completo' => $responsavel->nome_completo ?? 'N/A',
                'nr_identificacao' => $responsavel->nr_identificacao ?? 'N/A',
                'contacto' => $responsavel->contacto ?? 'N/A',
                'email' => $responsavel->email ?? 'N/A',
                'tipo_responsavel' => $responsavel->tipo_responsavel ?? 'N/A',
                'grau_parentesco' => $responsavel->grau_parentesco ?? 'N/A',
                'data_inicio_autorizacao' => $responsavel->data_inicio_autorizacao,
                'data_fim_autorizacao' => $responsavel->data_fim_autorizacao,
                'estado_autorizacao' => $responsavel->estado_autorizacao ?? 'N/A',
                'alterado_por' => Auth::id(), // já filtrado acima
                'alterado_em' => now(),
                'motivo' => implode(', ', $alteracoes) ?: 'Criação do responsável'
            ]);
        }
    });
}


    public function getDisplayNameAttribute()
    {
        return $this->nome_completo ?? 'Responsável sem nome';
    }
}