<?php

namespace App\Http\Controllers\Licenses;

use App\Events\CheckoutableCheckedOut;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LicenseCheckoutRequest;
use App\Models\License;
use App\Models\LicenseSeat;
use App\Models\User;
use App\Models\Asset;
use App\Notifications\CheckoutLicenseSeatNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LicenseCheckoutController extends Controller
{
    /**
     * Mostra o formulário de checkout para uma licença.
     */
    public function create($id)
    {
        if ($license = License::find($id)) {
            $this->authorize('checkout', $license);

            if ($license->category) {
                if ($license->availCount()->count() < 1) {
                    return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.checkout.not_enough_seats'));
                }

                return view('licenses/checkout', compact('license'));
            }

            return redirect()->route('licenses.edit', ['license' => $license->id])
                ->with('error', trans('general.invalid_item_category_single', ['type' => trans('general.license')]));
        }

        return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.not_found'));
    }

    /**
     * Processa o checkout e envia notificações.
     */
    public function store(LicenseCheckoutRequest $request, $licenseId, $seatId = null)
    {
        if (!$license = License::find($licenseId)) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.not_found'));
        }

        $this->authorize('checkout', $license);

        $licenseSeat = $this->findLicenseSeatToCheckout($license, $seatId);
        $licenseSeat->user_id = Auth::id();
        $licenseSeat->notes = $request->input('notes'); // ✅ Guarda a nota recebida do request

        Log::debug('Nota salva no LicenseSeat:', ['notes' => $licenseSeat->notes]);

        if ($request->filled('asset_id')) {
            $checkoutTarget = $this->checkoutToAsset($licenseSeat);
        } elseif ($request->filled('assigned_to')) {
            $checkoutTarget = $this->checkoutToUser($licenseSeat);
        } else {
            return redirect()->route('licenses.index')->with('error', trans('Something went wrong handling this checkout.'));
        }

        if ($checkoutTarget) {
            $storedAssetId = $licenseSeat->asset_id;

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
                            'email' => $encarregado->email
                        ]);

                        // ✅ Passa a nota corretamente para a notificação
                        $encarregado->notify(new CheckoutLicenseSeatNotification(
                            $licenseSeat,
                            $utente,
                            $encarregado,
                            auth()->user(),
                            'Licença alocada',
                            $licenseSeat->notes
                        ));
                        
                    } else {
                        Log::warning('Nenhum EE encontrado para utente.', ['utente_id' => $utente->id]);
                    }
                } else {
                    Log::warning('Utente não encontrado.', ['asset_id' => $storedAssetId]);
                }
            }

            return redirect()->to(Helper::getRedirectOption($request, $license->id, 'Licenses'))
                ->with('success', trans('admin/licenses/message.checkout.success'));
        }

        return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.checkout.error'));
    }



    protected function findLicenseSeatToCheckout($license, $seatId)
    {
        $licenseSeat = LicenseSeat::find($seatId) ?? $license->freeSeat();

        if (!$licenseSeat) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.checkout.not_enough_seats'));
        }

        return $licenseSeat;
    }

    protected function checkoutToAsset($licenseSeat)
    {
        if (is_null($target = Asset::find(request('asset_id')))) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.asset_does_not_exist'));
        }
        $licenseSeat->asset_id = request('asset_id');

        if ($licenseSeat->save()) {
            event(new CheckoutableCheckedOut($licenseSeat, $target, auth()->user(), request('notes')));
            return $target;
        }

        return false;
    }

    protected function checkoutToUser($licenseSeat)
    {
        if (is_null($target = User::find(request('assigned_to')))) {
            return redirect()->route('licenses.index')->with('error', trans('admin/licenses/message.user_does_not_exist'));
        }
        $licenseSeat->assigned_to = request('assigned_to');

        if ($licenseSeat->save()) {
            event(new CheckoutableCheckedOut($licenseSeat, $target, auth()->user(), request('notes')));
            return $target;
        }

        return false;
    }
}
