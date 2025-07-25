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

class CheckoutLicenseSeatNotification extends Notification
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

    public function __construct(LicenseSeat $licenseSeat, Asset $utente, $encarregado, ?User $checkedOutBy, $acceptance, $note = null)


    {
        $this->item        = $licenseSeat->license;
$this->admin       = $checkedOutBy;
$this->note        = !empty($note) ? preg_replace('/[\r\n]+/', ' ', $note) : 'Sem notas adicionais';
$this->settings    = Setting::getSettings();
$this->utente      = $utente;
$this->encarregado = $encarregado;
$this->programa    = $this->item ? $this->item->name : 'Programa Desconhecido';

        // ✅ Buscar o utente (asset)
        $assetId = $licenseSeat->asset_id;
        if (!$assetId) {
            Log::warning('LicenseSeat sem asset_id válido na alocação.', ['license_seat_id' => $licenseSeat->id]);
            return;
        }

        $this->utente = Asset::find($assetId);
        if (!$this->utente) {
            Log::warning('Utente não encontrado para LicenseSeat no checkout.', ['license_seat_id' => $licenseSeat->id, 'asset_id' => $assetId]);
            return;
        }

        Log::debug('Utente encontrado:', ['utente_id' => $this->utente->id]);

        // ✅ Buscar o Encarregado de Educação (EE)
        $this->encarregado = $this->utente->responsaveis()
            ->wherePivot('tipo_responsavel', 'Encarregado de Educacao')
            ->wherePivot('estado_autorizacao', 'Autorizado')
            ->first();

        if ($this->encarregado) {
            Log::debug('EE encontrado:', ['id' => $this->encarregado->id, 'email' => $this->encarregado->email, 'nome' => $this->encarregado->nome_completo]);
        } else {
            Log::warning('Nenhum Encarregado de Educação encontrado para o utente no checkout.', ['utente_id' => $this->utente->id]);
        }
    }

    public function via()
    {
        return ['mail'];
    }

    public function toMail()

    {
        Log::debug('Enviando e-mail com os seguintes dados:', [
            'item'        => $this->item->name,
            'admin'       => $this->admin->name ?? 'Administrador Desconhecido',
            'note'        => $this->note,
            'target'      => $this->target->name ?? 'Desconhecido',
            'encarregado' => $this->encarregado->nome_completo ?? 'Sem EE',
            'utente'      => $this->utente->name ?? 'Sem Utente',
            'programa'    => $this->programa,
        ]);
        

        return (new MailMessage)
            ->markdown('notifications.markdown.checkout-license', [
                'item'        => $this->item,
                'admin'       => $this->admin,
                'note'        => $this->note, // ✅ Envia a nota corretamente para o template
                'target'      => $this->target,
                'encarregado' => $this->encarregado,
                'utente'      => $this->utente,
                'programa'    => $this->programa,
                'eula' => $this->item->category->eula_text ?? null,
                'data_inicio'      => $this->item->purchase_date,
                'data_termino'     => $this->item->termination_date,

            ])
            ->subject(trans('mail.Confirm_license_delivery', ['programa' => $this->programa]));

    }
}
