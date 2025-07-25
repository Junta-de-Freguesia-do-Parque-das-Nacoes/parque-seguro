<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Mensagem</th>
            <th>Lida</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody id="notificacoesTableBody">
        @forelse($notificacoes as $notificacao)
            <tr id="notificacao-{{ $notificacao->id }}" class="{{ $notificacao->pivot && $notificacao->pivot->lida ? 'notificacao-lida' : '' }}">
                <td>{{ $notificacao->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @php
                        $cores = [
                            'nota_adicionada' => 'primary',
                            'nota_eliminada' => 'danger',
                            'ficheiro_anexado' => 'info',
                            'preferencia_individual' => 'warning',
                            'preferencias_gerais' => 'default',
                            'novo_responsavel' => 'success',
                            'remocao_responsavel' => 'danger',
                            'responsavel_atualizado' => 'info',
                            // Adicione mais tipos e cores conforme necessário
                        ];
                        $cor = $cores[$notificacao->tipo] ?? 'default';
                    @endphp
                    <span class="label label-{{ $cor }}">
                        {{ ucfirst(str_replace('_', ' ', $notificacao->tipo)) }}
                    </span>
                </td>
                <td>{!! $notificacao->mensagem !!}</td>
                <td class="status-lida-cell">
                    {{-- Verifica se a relação pivot existe antes de aceder a 'lida' --}}
                    @if($notificacao->pivot && $notificacao->pivot->lida)
                        <span class="label label-success">Lida</span>
                    @else
                        <span class="label label-warning">Por ler</span>
                    @endif
                </td>
                <td>
                    {{-- Verifica se a relação pivot existe e se não está lida --}}
                    @if($notificacao->pivot && !$notificacao->pivot->lida)
                        <button class="btn btn-xs btn-primary btn-marcar-lida" data-id="{{ $notificacao->id }}">
                            Marcar como lida
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Sem notificações para mostrar.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- Paginação com manutenção de filtros --}}
@if ($notificacoes->hasPages())
<div class="box-footer clearfix">
    {{ $notificacoes->appends(request()->query())->links() }}
</div>
@endif
