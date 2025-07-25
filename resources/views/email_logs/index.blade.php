@extends('layouts.default')

@section('title', 'Logs de Emails Enviados')

@section('content')
<p></p>
<div class="container" style="padding-bottom: 100px;">
    <h1>Logs de Emails Enviados</h1>
	
	 <!-- Aviso -->
    <div class="alert alert-info">
    <strong>Aviso:</strong> O status <span class="text-success">"Sucesso"</span> confirma que a mensagem foi enviada com êxito ao servidor de email, garantindo que o sistema processou o envio corretamente. No entanto, é importante observar que não é possível verificar a entrega final ao destinatário, pois fatores externos, como configurações do provedor de email ou filtros de spam, podem interferir no recebimento.
</div>


    <!-- Formulário de Filtro -->
    <form method="GET" action="{{ route('email-logs.index') }}">
    <div class="form-row">
        <div class="col">
    <label for="email">E-mail:</label>
    <input type="text" id="email" name="email" class="form-control" placeholder="Digite o e-mail" value="{{ request('email') }}">
    <div id="autocomplete-results" style="position: absolute; z-index: 9999; background: white; border: 1px solid #ccc;"></div>
</div>

        <div class="col">
            <label for="status">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="">Todos</option>
                <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Sucesso</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Falhou</option>
            </select>
        </div>
        <div class="col">
            <label for="start_date">Data de Início:</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col">
            <label for="end_date">Data de Fim:</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
		<p></p>
        <div class="col mt-4">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <button type="button" class="btn btn-primary" onclick="window.location='{{ route('email-logs.index') }}'">Limpar</button>
        </div>
    </div>
</form>
<p></p>

    <!-- Tabela de Resultados -->
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Assunto</th>
                <th>Status</th>
                <th>Data de Envio</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($emailLogs as $log)
                <tr>
                    <td>{{ $log->id }}</td>
                    <td>{{ $log->email }}</td>
                    <td>{{ $log->subject }}</td>
                    <td class="{{ $log->status === 'success' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($log->status) }}
                    </td>
                    <td>{{ \Carbon\Carbon::parse($log->sent_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $log->body }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Nenhum log encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Paginação -->
    <div class="d-flex justify-content-between">
        <div>
            Mostrar {{ $emailLogs->firstItem() }} a {{ $emailLogs->lastItem() }} de {{ $emailLogs->total() }} registros
        </div>
        <div>
            {{ $emailLogs->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Botão de Exportação -->
    <div class="text-right" style="margin-top: 20px;">
    <a href="{{ route('email-logs.export', request()->all()) }}" class="btn btn-primary">
        Exportar para Excel
    </a>
</div>

</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let debounceTimeout;

        $('#email').on('keyup', function () {
            const query = $(this).val();

            if (query.length < 2) {
                $('#autocomplete-results').html('');
                return;
            }

            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {
                $.ajax({
                    url: "{{ route('email-logs.autocomplete') }}",
                    type: "GET",
                    data: { query: query },
                    success: function (data) {
                        let html = '';
                        if (data.length > 0) {
                            data.forEach(item => {
                                html += `<div class="autocomplete-item" style="padding: 5px; cursor: pointer;" data-email="${item.email}">
                                            ${item.email}
                                         </div>`;
                            });
                        } else {
                            html = '<div style="padding: 5px;">Nenhum resultado encontrado</div>';
                        }

                        $('#autocomplete-results').html(html).show();
                    }
                });
            }, 300);
        });

        // Selecionar um item da lista de autocomplete
        $(document).on('click', '.autocomplete-item', function () {
            const selectedEmail = $(this).data('email');
            $('#email').val(selectedEmail);
            $('#autocomplete-results').html('');
        });

        // Esconder a lista quando clicar fora
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#email, #autocomplete-results').length) {
                $('#autocomplete-results').html('');
            }
        });
    });
</script>
