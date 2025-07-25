<?php

namespace App\Notifications;

use App\Models\LicenseSeat;
use App\Models\Setting;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CheckinLicenseSeatNotification extends Notification
{
    use Queueable;

    private $target;
    private $item;
    private $admin;
    private $note;
    private $settings;
    private $encarregado;
    private $utente;
    private $programa;

    public function __construct(LicenseSeat $licenseSeat, $checkedOutTo, User $checkedInBy, $note, $storedAssetId = null)
    {
        $this->target = $checkedOutTo;
        $this->item = $licenseSeat->license;
        $this->admin = $checkedInBy;
        
        // ✅ Garante que a nota correta está a ser usada
        $this->note = !empty($note) ? $note : 'Check-in efetuado';

        $this->settings = Setting::getSettings();

        // ✅ Obtém o nome do programa/licença
        $this->programa = $licenseSeat->license ? $licenseSeat->license->name : "Programa Desconhecido";

        // 🔥 **Buscar Asset via `storedAssetId`**
        $assetId = $storedAssetId ?? $licenseSeat->asset_id;

        if (!$assetId) {
            Log::warning('LicenseSeat sem asset_id válido no check-in.', ['license_seat_id' => $licenseSeat->id]);
            return;
        }

        $this->utente = Asset::find($assetId);
        if (!$this->utente) {
            Log::warning('Utente não encontrado.', ['license_seat_id' => $licenseSeat->id, 'asset_id' => $assetId]);
            return;
        }

        Log::debug('Utente encontrado:', ['utente_id' => $this->utente->id]);

        // 🔎 **Buscar o EE**
        $this->encarregado = $this->utente->responsaveis()
            ->wherePivot('tipo_responsavel', 'Encarregado de Educacao')
            ->wherePivot('estado_autorizacao', 'Autorizado')
            ->first();

        if ($this->encarregado) {
            Log::debug('EE encontrado:', [
                'id' => $this->encarregado->id,
                'email' => $this->encarregado->email,
                'nome' => $this->encarregado->nome_completo
            ]);
        } else {
            Log::warning('Nenhum EE encontrado para o utente.', ['utente_id' => $this->utente->id]);
        }
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail()
    {
        Log::debug('Enviando e-mail de check-in com os seguintes dados:', [
            'item'        => $this->item->name,
            'admin'       => $this->admin->name ?? 'Administrador Desconhecido',
            'note'        => $this->note,
            'target'      => $this->target->name ?? 'Desconhecido',
            'encarregado' => $this->encarregado->nome_completo ?? 'Sem EE',
            'utente'      => $this->utente->name ?? 'Sem Utente',
            'programa'    => $this->programa,
        ]);

        return (new MailMessage)
            ->markdown('notifications.markdown.checkin-license', [
                'item'        => $this->item,
                'admin'       => $this->admin,
                'note'        => $this->note,
                'target'      => $this->target,
                'encarregado' => $this->encarregado,
                'utente'      => $this->utente,
                'programa'    => $this->programa,
            ])
            ->subject(trans('mail.License_Checkin_Notification', ['programa' => $this->programa]));
    }
}
