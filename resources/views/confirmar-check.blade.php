@extends('layout-qrcode')

@push('styles')
<style>
    .content {
        margin-bottom: 100px;
    }

    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .responsavel-card {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        margin: 10px 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        flex-wrap: wrap;
    }

    .responsavel-card img {
        width: 100px;
        height: 200px;
        object-fit: cover;
        border-radius: 5px;
        cursor: pointer;
    }

    .responsavel-info {
    flex: 1;
    margin-left: 15px;
    font-size: 0.9em;
    text-align: left; /* <-- Alinha o texto à esquerda */
}

    .responsavel-info strong {
        font-size: 1.1em;
        color: #333;
    }

    .button-card {
        background-color: #f44336;
        color: white;
        padding: 10px 15px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        border-radius: 5px;
        transition: background-color 0.3s;
        position: absolute;
        bottom: 15px;
        right: 15px;
    }

    /* Estilos uniformes para botões do modal */
    .button-card1,
    .modal-content .button-cancel {
        padding: 10px 20px;
        font-size: 14px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    .button-card1 {
        background-color: #f44336;
        color: white;
        transition: background-color 0.3s;
    }

    .button-card1:hover {
        background-color: #c0392b;
    }

    .modal-content .button-cancel {
        background-color: #808080;
        color: white;
    }

    .modal-content .button-cancel:hover {
        background-color: #6c757d;
    }

    .fixed-buttons {
        position: fixed;
        left: 0;
        bottom: 20px;
        width: 100%;
        background-color: #ffffff;
        padding: 20px 0;
        box-shadow: 0 -2px 6px rgba(0, 0, 0, 0.1);
        text-align: center;
        z-index: 1000;
    }

    .button-container {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .button-container .button-footer {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .button-container .button-footer:hover {
        background-color: #45a049;
    }

    .button-container .button-blue {
        background-color: #007BFF;
    }

    .button-container .button-blue:hover {
        background-color: #0056b3;
    }

    .button-container .button-checkout-footer {
        background-color: #f44336;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .button-container .button-checkout-footer:hover {
        background-color: #c0392b;
    }

    .image-modal,
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        justify-content: center;
        align-items: center;
    }

    .image-modal img {
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
    }

    .modal-content {
        background-color: white;
        border-radius: 10px;
        padding: 20px;
        width: 90%;
        max-width: 400px;
        text-align: center;
    }

    .modal-content label {
        display: block;
        text-align: left;
        margin: 10px 0 5px;
    }

    .modal-content input,
    .modal-content select {
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .modal .close,
    .image-modal .close {
        position: absolute;
        top: 10px;
        right: 10px;
        color: white;
        font-size: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    .ee-indicator {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 5px 8px;
        border-radius: 5px;
    }

    .sair-sozinho-indicator {
        margin-top: 8px;
        background-color: #28a745;
        color: white;
        font-size: 14px;
        font-weight: bold;
        padding: 6px 10px;
        border-radius: 5px;
        display: block;
        text-align: center;
    }
</style>

@endpush

@section('content')
<div class="content">
<h2>Confirmar Entrada ou Saída para {{ $utente->nome_apelido ?? 'Nome não disponível' }}?</h2>

@php
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/**
 * Função que processa datas individuais ou intervalos e devolve coleção de Carbon dos dias correspondentes.
 */
function expandirDatas($stringDatas) {
    return collect(preg_split('/[\r\n,]+/', $stringDatas ?? ''))
        ->map(fn($data) => trim($data))
        ->filter()
        ->flatMap(function ($item) {
            if (
                preg_match('/(\d{2}\/\d{2}\/\d{4})\s*-\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches) ||
                preg_match('/(\d{2}\/\d{2}\/\d{4})\s*a\s*(\d{2}\/\d{2}\/\d{4})/', $item, $matches)
            ) {
                try {
                    $dataInicio = Carbon::createFromFormat('d/m/Y', trim($matches[1]));
                    $dataFim = Carbon::createFromFormat('d/m/Y', trim($matches[2]));
                    $datas = [];

                    while ($dataInicio->lte($dataFim)) {
                        $datas[] = $dataInicio->copy();
                        $dataInicio->addDay();
                    }

                    return collect($datas);
                } catch (\Exception $e) {
                    return collect();
                }
            } else {
                try {
                    return collect([Carbon::createFromFormat('d/m/Y', $item)]);
                } catch (\Exception $e) {
                    return collect();
                }
            }
        });
}

$customFields = $utente->toArray();
$programasCheckbox = [];
$iconesProgramas = [];

foreach ($customFields as $campo => $valor) {
    if (Str::startsWith($campo, '_snipeit_') && is_string($valor) && Str::contains($valor, '/')) {
        $nomeCampo = Str::after($campo, '_snipeit_');
$nomeCampo = preg_replace('/_\d+$/', '', $nomeCampo); // remove _79

// Remover "programa_" do início se existir
$nomeCampo = preg_replace('/^programa_/', '', $nomeCampo);

$nomeCampo = ucwords(str_replace('_', ' ', $nomeCampo));


        $programasCheckbox[$campo] = trim($nomeCampo);
        $iconesProgramas[$campo] = '📅';
    }
}


$hoje = Carbon::today();
$programasHoje = [];

foreach ($programasCheckbox as $campo => $nomePrograma) {
    $datas = expandirDatas($utente->$campo ?? '');
    if ($datas->contains(fn($d) => $d->isSameDay($hoje))) {
        $programasHoje[] = [
            'icone' => $iconesProgramas[$campo] ?? '🗓️',
            'nome' => $nomePrograma,
        ];
    }
}
@endphp

@if (!empty($programasHoje))
    <div style="background-color: #f0f8ff; border: 1px solid #90caf9; padding: 12px 15px; border-radius: 8px; margin: 10px auto 20px; max-width: 600px; color: #1565c0; font-size: 15px;">
       <div style="text-align: center; font-weight: bold; margin-bottom: 8px;">
    🗓️ <strong>Hoje está inscrito em:</strong>
</div>

        <ul style="text-align: left; padding-left: 18px;">
            @foreach ($programasHoje as $programa)
                <li>{{ $programa['icone'] }} <strong>{{ $programa['nome'] }}</strong></li>
            @endforeach
        </ul>
    </div>
@endif






    @if (!empty($utente->company))
        <p><strong>Em</strong> {{ $utente->company['name'] ?? 'Escola não disponível' }}</p>
    @endif

    <div style="text-align: center;">
        <div style="display: inline-block;">
            <img src="{{ $utente->image ? route('assets.foto', ['filename' => basename($utente->image)]) : asset('img/anoninochild.jpg') }}"
                alt="Foto da Criança"
                style="width: 150px; height: auto; border-radius: 8px;"
                onclick="openImageModal(this.src)">

            @if (!empty($utente->_snipeit_pode_sair_sozinho_66) && in_array(strtolower(trim($utente->_snipeit_pode_sair_sozinho_66)), ['sim', '1', 'true']))
                <div class="sair-sozinho-indicator" title="Autorizado a sair sozinho">
                    ✅ Autorizado a sair sozinho
                </div>
            @endif
        </div>
    </div>

    @if($manutencao)
        <div class="alert alert-warning" style="padding: 15px; background-color: #ffecb3; border-radius: 5px; margin-top: 20px;">
            <strong>⚠️ Alerta!</strong><br>
            Houve um incidente hoje: "<strong>{{ $manutencao->title }}</strong>".<br>
            {{ $manutencao->notes }}
        </div>
    @endif

    <p>&nbsp;</p>
    <b>Pessoas autorizadas a recolher:</b>

    @if ($utente->responsaveis->where('pivot.estado_autorizacao', 'Autorizado')->isEmpty())
        <p style="color: red; margin-top: 20px; font-weight: bold;">
            Sem responsável autorizado definido, por favor definir a pessoa em backoffice.
        </p>
    @else
        @foreach ($utente->responsaveis->sortByDesc(fn($r) => $r->pivot->tipo_responsavel === 'Encarregado de Educacao') as $responsavel)
            @if ($responsavel->pivot->estado_autorizacao === 'Autorizado')
                <div class="responsavel-card">
                    @if ($responsavel->pivot->tipo_responsavel === 'Encarregado de Educacao')
                        <div class="ee-indicator">EE</div>
                    @endif

                    <img src="{{ $responsavel->foto ? route('responsaveis.foto', ['filename' => basename($responsavel->foto)]) : asset('img/anonimoadulto.png') }}"
                         alt="Imagem do Responsável"
                         onclick="openImageModal(this.src)">

                         <div class="responsavel-info">
    <p>
        <strong>{{ $responsavel->nome_completo }}</strong><br>
        Nº ID: <strong>{{ $responsavel->nr_identificacao }}</strong><br>
        Parentesco: {{ $responsavel->pivot->grau_parentesco ?? 'Não informado' }}<br>
        Contacto:
        <a href="tel:{{ $responsavel->contacto ?? '' }}">
            {{ $responsavel->contacto ?? 'Não disponível' }}
        </a><br>
        Observações: {{ $responsavel->pivot->observacoes ?? 'Sem observações.' }}

        @if (!empty($responsavel->pivot->dias_nao_autorizados))
            @php
                $dias = collect(explode(',', $responsavel->pivot->dias_nao_autorizados))
                    ->map(fn($d) => trim($d))
                    ->filter()
                    ->implode(', ');
            @endphp
            <div style="margin-top: 10px; padding: 8px; background-color: #ffebee; color: #b71c1c; border-radius: 5px; font-size: 1em; line-height: 1.5;">
                ❌ <strong style="font-weight: 500;">Não autorizado a recolher às:</strong><br>
                <span style="font-size: 1em;">{{ $dias }}</span>
            </div>
        @endif
    </p>
</div>

<!-- Botão de Saída -->
<form action="{{ route('utente.checkout', ['id' => $utente->id]) }}" method="POST">
    @csrf
    <input type="hidden" name="responsavel_id" value="{{ $responsavel->id }}">
    <button type="submit" class="button-card">Saída</button>
</form>


                </div>
            @endif
        @endforeach
    @endif

    {{-- Modal imagem --}}
    <div class="image-modal" id="imageModal">
        <span class="close" onclick="closeImageModal()">&times;</span>
        <img id="modalImage" src="" alt="Imagem Ampliada">
    </div>

    {{-- Botões fixos --}}
    <div class="fixed-buttons">
        <div class="button-container">
            <form action="{{ url('/utente/' . $utente->id . '/checkin') }}" method="POST">
                @csrf
                <button type="submit" class="button-footer">Entrada</button>
            </form>

            <a href="{{ url('/qr-code-scanner') }}" style="text-decoration: none;">
                <button class="button-blue button-footer">Ler QR Code</button>
            </a>

            <button type="button" class="button-checkout-footer" onclick="openModal()">Saída 👨‍👦🚶‍♂️</button>
        </div>
    </div>

    {{-- Modal de Saída Direta --}}
    <div id="checkoutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Motivo da Saída</h2>
        <form action="{{ route('utente.checkout.direct', ['id' => $utente->id]) }}" method="POST">
            @csrf
            <label for="motivo_checkout">Escolha o motivo:</label>
            <select id="motivo_checkout" name="motivo_checkout" required>
                <option value="" disabled selected>Selecione um motivo</option>
                <option value="Aluno saiu de forma autônoma">Utente saiu de forma autónoma 🚶‍♀️</option>
                <option value="Pessoa com autorização excepcional">Pessoa com autorização excepcional 👨‍👦</option>
            </select>

            <div id="responsavel-fields" style="display: none;">
                <label for="nome_responsavel">Nome:</label>
                <input id="nome_responsavel" type="text" name="nome_responsavel">
                <label for="nr_cc">Número de identificação:</label>
                <input id="nr_cc" type="text" name="nr_cc">
            </div>

            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
                <button type="submit" class="button-card1">✅ Confirmar Saída</button>
                <button type="button" class="button-cancel" onclick="closeModal()">❌ Cancelar</button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection

@push('scripts')
<script>
    function openImageModal(src) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = src;
        modal.style.display = 'flex';
    }

    function closeImageModal() {
        document.getElementById('imageModal').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const checkoutModal = document.getElementById('checkoutModal');
        const motivoCheckout = document.getElementById('motivo_checkout');
        const responsavelFields = document.getElementById('responsavel-fields');
        const nomeResponsavel = document.getElementById('nome_responsavel');
        const nrCc = document.getElementById('nr_cc');

        window.openModal = function () {
            checkoutModal.style.display = 'flex';
        };

        window.closeModal = function () {
            checkoutModal.style.display = 'none';
            motivoCheckout.value = '';
            nomeResponsavel.value = '';
            nrCc.value = '';
            responsavelFields.style.display = 'none';
        };

        motivoCheckout.addEventListener('change', function () {
            responsavelFields.style.display = this.value === 'Pessoa com autorização excepcional' ? 'block' : 'none';
        });
    });
</script>
@endpush
