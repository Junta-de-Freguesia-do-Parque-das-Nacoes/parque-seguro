<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\CheckinCheckoutNotification;
use Illuminate\Support\Facades\DB;

class SendCheckinCheckoutNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $utente;
    protected $utilizadorBackoffice;
    protected $responsavel;
    protected $action;

    /**
     * Cria uma nova instância do job.
     *
     * @param \App\Models\Asset $utente
     * @param \App\Models\User $utilizadorBackoffice
     * @param \App\Models\Responsavel|null $responsavel
     * @param string $action
     */
    public function __construct($utente, $utilizadorBackoffice, $responsavel, $action)
    {
        $this->utente = $utente->load('company');
        $this->utilizadorBackoffice = $utilizadorBackoffice;
        $this->responsavel = $responsavel;
        $this->action = $action;
    }

    /**
     * Processa o job.
     */
    public function handle()
{
    try {
        // Buscar o Encarregado de Educação com e-mail válido
        $responsavelEE = DB::table('responsaveis_utentes')
            ->join('responsaveis', 'responsaveis_utentes.responsavel_id', '=', 'responsaveis.id')
            ->where('responsaveis_utentes.utente_id', $this->utente->id)
            ->where('responsaveis_utentes.tipo_responsavel', 'Encarregado de Educacao')
            ->whereNotNull('responsaveis.email')
            ->whereRaw("LENGTH(responsaveis.email) > 5")
            // Remover a condição de `responsaveis.estado`
            ->where('responsaveis.estado', 'Ativo') // Remover ou substituir por outra verificação, se necessário
            ->select('responsaveis.id', 'responsaveis.nome_completo', 'responsaveis.email')
            ->first();

        if (!$responsavelEE) {
            \Log::warning("⚠️ Nenhum Encarregado de Educação com e-mail válido encontrado para o utente ID: {$this->utente->id}");
            return;
        }

        // Definir a data/hora da ação corretamente
        $dataHoraAcao = now(); // Certifica que a variável tem valor válido

        // Enviar e-mail
        Mail::to($responsavelEE->email)
            ->send(new CheckinCheckoutNotification(
                $this->utente,
                $this->utilizadorBackoffice,
                null, // Manutenção não se aplica aqui
                $this->action,
                $responsavelEE,
                $responsavelEE->nome_completo,
                null, // Não há número de CC neste contexto
                $dataHoraAcao, // Agora garantidamente tem um valor
                null // Grau de parentesco não é necessário para o checkin
            ));

        \Log::info("✅ E-mail de Check-in enviado para {$responsavelEE->email} (Utente ID: {$this->utente->id})");

    } catch (\Exception $e) {
        \Log::error("❌ Erro ao enviar e-mail no job SendCheckinCheckoutNotification: {$e->getMessage()}", [
            'utente_id' => $this->utente->id,
            'utilizadorBackoffice_id' => $this->utilizadorBackoffice->id,
            'responsavel_id' => $responsavelEE->id ?? null,
            'action' => $this->action,
        ]);
    }
}
}