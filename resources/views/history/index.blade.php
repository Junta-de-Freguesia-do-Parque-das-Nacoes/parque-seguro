@extends('layouts.default')

@section('title', 'Histórico de Checkouts de ' . $utente->name)

@section('content')
    <h1>Histórico de Checkouts de {{ $utente->name }}</h1>

    <table class="table">
        <thead>
            <tr>
                <th>ID do Checkout</th>
                <th>Responsável</th>
                <th>Data do Checkout</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checkouts as $checkout)
                <tr>
                    <td>{{ $checkout->id }}</td>
                    <td>{{ $checkout->user_id }}</td> <!-- Aqui você pode buscar o nome do responsável se necessário -->
                    <td>{{ $checkout->created_at }}</td>
                    <td>{{ $checkout->note }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Nenhum checkout encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <a href="{{ route('utentes.index') }}">Voltar para a lista de utentes</a>
@endsection

