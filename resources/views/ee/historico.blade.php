@extends('layouts.ee-master')

@section('title', 'Histórico de Entradas e Saídas de ' . $utente->name)

@section('content')
<div class="card-custom" style="padding: 20px;">

    @php
        $nomeEe = collect(explode(' ', $responsavel->nome_completo));
        $nomeFormatado = $nomeEe->only(0)->merge($nomeEe->slice(-1))->implode(' ');
    @endphp

    <div style="font-size: 1.4rem; margin-bottom: 12px;">
        📋 Histórico de entradas e saídas de <strong>{{ $utente->name }}</strong><br>
    </div>

    <a href="{{ route('ee.gestao') }}" class="btn-voltar-dashboard">← Voltar ao Dashboard</a>

    <form method="GET" style="margin-top: 20px;">
        <div class="row g-3">
            <div class="col-md-3">
                <label>Data de Início:</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label>Data de Fim:</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label>Tipo:</label>
                <select name="action_type" class="form-control">
                    <option value="">Todos</option>
                    <option value="checkin" {{ request('action_type') == 'checkin' ? 'selected' : '' }}>Entrada</option>
                    <option value="checkout" {{ request('action_type') == 'checkout' ? 'selected' : '' }}>Saída</option>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary w-100">🔍 Filtrar</button>
            </div>
        </div>
    </form>

    <div class="table-responsive mt-4">
        <table class="table table-striped" style="font-size: 1.4rem;">
            <thead class="table-light">
                <tr>
                    <th>Ação</th>
                    <th>Nota</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>
                            @switch($log->action_type)
                                @case('checkin') 🟢 Entrada @break
                                @case('checkout') 🔴 Saída @break
                                @default {{ ucfirst($log->action_type) }}
                            @endswitch
                        </td>
                        <td>{{ $log->note ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->action_date)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">Nenhum registo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $logs->withQueryString()->links() }}
    </div>
</div>
@endsection

<style>
.btn-voltar-dashboard {
    display: inline-block;
    margin-bottom: 1.5rem;
    padding: 10px 18px;
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 8px;
    text-decoration: none;
    font-size: 1rem;
    transition: background-color 0.2s ease;
}
.btn-voltar-dashboard:hover {
    background-color: #e0e0e0;
    color: #000;
}
</style>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const expiresAt = {{ session('ee_session_expires_at') ?? 'null' }};
    const contadorEl = document.getElementById('contador');

    if (expiresAt && contadorEl) {
        function atualizarContador() {
            const agora = Math.floor(Date.now() / 1000);
            const segundosRestantes = expiresAt - agora;

            if (segundosRestantes <= 0) {
                contadorEl.innerText = '00:00';
                clearInterval(intervaloContador);
                window.location.href = '/ee/logout-redirect';
                return;
            }

            const minutos = Math.floor(segundosRestantes / 60).toString().padStart(2, '0');
            const segundos = (segundosRestantes % 60).toString().padStart(2, '0');
            contadorEl.innerText = `${minutos}:${segundos}`;
        }

        atualizarContador();
        var intervaloContador = setInterval(atualizarContador, 1000);
    }
});
</script>
@endpush
