<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Checkouts e Checkins de {{ $utente->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Histórico de Checkouts e Checkins de {{ $utente->name }}</h1>
    <p><strong>Data de Geração:</strong> {{ $generatedDate }}</p>
    <p><strong>Gerado por:</strong> {{ $userName }}</p>
    <p><strong>Data de Início:</strong> {{ $startDate }}</p>
    <p><strong>Data de Fim:</strong> {{ $endDate }}</p>
    
    <table>
        <thead>
            <tr>
                <th>ID do Log</th>
                <th>Tipo de Ação</th>
                <th>Responsável JFPN</th>
                <th>Pessoa Autorizada</th>
                <th>Data da Ação</th>
                <th>Nota</th>
            </tr>
        </thead>
        <tbody>
            @forelse($checkoutsAndCheckins as $log)
                <tr>
                    <td>{{ $log->log_id }}</td>
                    <td>{{ ucfirst($log->action_type) }}</td>
                    <td>{{ $log->responsible_first_name }} {{ $log->responsible_last_name }}</td>
                    <td>{{ $log->action_note }}</td>
                    <td>{{ $log->action_date }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Nenhum log encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

