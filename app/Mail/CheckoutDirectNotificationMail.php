<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckoutDirectNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $utente;
    public $utilizadorBackoffice;
    public $manutencao;
    public $action;
    public $responsavel;
    public $nomeResponsavel;
    public $nrCC;
    public $dataHoraAcao;
    public $grauParentesco;

    /**
     * Criar uma nova instância do Mailable.
     */
    public function __construct(
        $utente,
        $utilizadorBackoffice,
        $manutencao,
        $action,
        $responsavel = null, // Permitir null
        $nomeResponsavel = null,
        $nrCC = null,
        $dataHoraAcao = null,
        $grauParentesco = null
    ) {
        $this->utente = $utente;
        $this->utilizadorBackoffice = $utilizadorBackoffice;
        $this->manutencao = $manutencao;
        $this->action = $action;
        $this->responsavel = $responsavel;
        $this->nomeResponsavel = $nomeResponsavel;
        $this->nrCC = $nrCC;
        $this->dataHoraAcao = $dataHoraAcao;
        $this->grauParentesco = $grauParentesco;
    }

    /**
     * Constrói o e-mail.
     */
    public function build()
    {
        // 🔍 Buscar o Encarregado de Educação se não foi passado como parâmetro
        if (!$this->responsavel) {
            $this->responsavel = DB::table('responsaveis_utentes')
                ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
                ->where('responsaveis_utentes.utente_id', $this->utente->id)
                ->where('responsaveis.tipo_responsavel', 'Encarregado de Educacao')
                ->whereNotNull('responsaveis.email')
                ->where('responsaveis.email', '!=', '')
                ->select('responsaveis.id', 'responsaveis.nome_completo', 'responsaveis.email', 'responsaveis.grau_parentesco')
                ->first();

            // Se não houver Encarregado de Educação, logar o erro
            if (!$this->responsavel) {
                Log::error("⚠️ Nenhum Encarregado de Educação encontrado para utente ID: {$this->utente->id}");
                return;
            }
        }

        // Log para depuração, garantindo que os dados estão sendo passados corretamente
        Log::info('📩 Enviando e-mail de notificação', [
            'utente_id' => $this->utente->id,
            'utilizadorBackoffice' => $this->utilizadorBackoffice->id,
            'action' => $this->action,
            'responsavel_id' => $this->responsavel->id ?? 'Não encontrado',
            'responsavel_email' => $this->responsavel->email ?? 'Sem e-mail',
            'dataHoraAcao' => $this->dataHoraAcao,
            'grauParentesco' => $this->grauParentesco,
        ]);

        return $this->view('emails.checkout_direct_notification')
            ->with([
                'utente' => $this->utente,
                'utilizadorBackoffice' => $this->utilizadorBackoffice,
                'manutencao' => $this->manutencao,
                'action' => $this->action,
                'responsavel' => $this->responsavel,
                'nomeResponsavel' => $this->nomeResponsavel ?? $this->responsavel->nome_completo ?? 'Não informado',
                'nrCC' => $this->nrCC,
                'dataHoraAcao' => $this->dataHoraAcao,
                'grauParentesco' => $this->grauParentesco ?? $this->responsavel->grau_parentesco ?? 'Não informado',
            ])
            ->subject('Notificação de Saída '. $this->utente->name);
    }
}
