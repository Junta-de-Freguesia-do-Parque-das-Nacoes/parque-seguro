<?php

namespace App\Http\Controllers\Licenses;

use App\Events\CheckoutableCheckedIn;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\LicenseSeat;
use App\Models\User;
use App\Models\Asset;
use App\Notifications\CheckinLicenseSeatNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LicenseCheckinController extends Controller
{
    /**
     * Mostra o formulário para devolver uma licença.
     */
    public function create($seatId = null, $backTo = null)
    {
        if (is_null($licenseSeat = LicenseSeat::find($seatId)) || is_null($license = License::find($licenseSeat->license_id))) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.not_found'));
        }

        $this->authorize('checkout', $license);

        return view('licenses/checkin', compact('licenseSeat'))->with('backto', $backTo);
    }

    /**
     * Processa a devolução da licença e envia notificações.
     */
    public function store(Request $request, $seatId = null, $backTo = null)
    {
        if (is_null($licenseSeat = LicenseSeat::find($seatId))) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.not_found'));
        }

        $license = License::find($licenseSeat->license_id);

        if (is_null($licenseSeat->assigned_to) && is_null($licenseSeat->asset_id)) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.checkin.error'));
        }

        $this->authorize('checkout', $license);

        if (!$license->reassignable) {
            Session::flash('error', 'License not reassignable.');
            return redirect()->back()->withInput();
        }

        // 🔥 **Guarda o asset_id antes da remoção**
        $storedAssetId = $licenseSeat->asset_id;

        if ($licenseSeat->assigned_to != null) {
            $return_to = User::find($licenseSeat->assigned_to);
        } else {
            $return_to = Asset::find($storedAssetId);
        }

        // ✅ **Registrar a nota recebida no request**
        $notaRecebida = $request->input('notes', 'Check-in efetuado');
        Log::debug('Valor recebido no request para notes:', ['notes' => $notaRecebida]);

        // **Atualizar dados**
        $licenseSeat->assigned_to = null;
        $licenseSeat->asset_id = null;
        $licenseSeat->notes = $notaRecebida;

        session()->put(['redirect_option' => $request->get('redirect_option')]);

        // ✅ **Salvar alterações antes de enviar a notificação**
        if ($licenseSeat->save()) {
            event(new CheckoutableCheckedIn($licenseSeat, $return_to, auth()->user(), $notaRecebida, $storedAssetId));

            // 🔔 **Enviar Notificação para o EE**
            if ($storedAssetId) {
                $utente = Asset::find($storedAssetId);
                if ($utente) {
                    Log::debug('Utente encontrado:', ['utente_id' => $utente->id]);

                    $encarregado = $utente->responsaveis()
                        ->wherePivot('tipo_responsavel', 'Encarregado de Educacao')
                        ->wherePivot('estado_autorizacao', 'Autorizado')
                        ->first();

                    if ($encarregado && $encarregado->email) {
                        Log::debug('Enviando notificação para EE:', [
                            'id' => $encarregado->id,
                            'email' => $encarregado->email,
                            'nome' => $encarregado->nome_completo
                        ]);

                        $encarregado->notify(new CheckinLicenseSeatNotification($licenseSeat, $encarregado, auth()->user(), $notaRecebida, $storedAssetId));
                    } else {
                        Log::warning('Nenhum EE encontrado para utente.', ['utente_id' => $utente->id]);
                    }
                } else {
                    Log::warning('Utente não encontrado.', ['asset_id' => $storedAssetId]);
                }
            }

            return redirect()->to(Helper::getRedirectOption($request, $license->id, 'Licenses'))
                ->with('success', trans('admin/licenses/message.checkin.success'));
        }

        return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.checkin.error'));
    }
    public function bulkCheckin(Request $request, $licenseId) {

        $license = License::findOrFail($licenseId);
        $this->authorize('checkin', $license);

        if (! $license->reassignable) {
            // Not allowed to checkin
            Session::flash('error', 'License not reassignable.');

            return redirect()->back()->withInput();
        }

        $licenseSeatsByUser = LicenseSeat::where('license_id', '=', $licenseId)
            ->whereNotNull('assigned_to')
            ->with('user')
            ->get();

        foreach ($licenseSeatsByUser as $user_seat) {
            $user_seat->assigned_to = null;

            if ($user_seat->save()) {
                Log::debug('Checking in '.$license->name.' from user '.$user_seat->username);
                $user_seat->logCheckin($user_seat->user, trans('admin/licenses/general.bulk.checkin_all.log_msg'));
            }
        }

        $licenseSeatsByAsset = LicenseSeat::where('license_id', '=', $licenseId)
            ->whereNotNull('asset_id')
            ->with('asset')
            ->get();

        $count = 0;
        foreach ($licenseSeatsByAsset as $asset_seat) {
            $asset_seat->asset_id = null;

            if ($asset_seat->save()) {
                Log::debug('Checking in '.$license->name.' from asset '.$asset_seat->asset_tag);
                $asset_seat->logCheckin($asset_seat->asset, trans('admin/licenses/general.bulk.checkin_all.log_msg'));
                $count++;
            }
        }

        return redirect()->back()->with('success', trans_choice('admin/licenses/general.bulk.checkin_all.success', 2, ['count' => $count] ));

    }

}
