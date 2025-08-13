<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($action) }} para {{ $utente->nome_apelido}}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }

        .banner {
            background-color: #ffffff;
            padding: 10px 0;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        .banner img {
            max-width: 80%;
            max-height: 150px;
            height: auto;
            display: inline-block;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            max-width: 700px;
            margin: 20px auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            line-height: 1.6;
        }

        h1 {
            color: #1d3557;
            margin-bottom: 20px;
            font-size: 1.8em;
            text-align: center;
        }

        p {
            color: #555;
            margin: 10px 0;
            line-height: 1.6;
        }

        .responsavel-info {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
            border-radius: 5px;
            margin: 20px 0;
        }

        .responsavel-info img {
            width: 50px;            /* Largura da imagem */
            height: 70px;           /* Altura da imagem */
            border-radius: 20%;     /* Borda reta, para um estilo de foto tipo passe */
            margin-right: 15px;     /* Espaçamento entre a imagem e o texto */
            object-fit: cover;      /* Garante que a imagem ocupe o espaço sem distorção */
        }

        .responsavel-info .info {
            flex: 1;
        }

        .alert-box {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 0.95em;
            line-height: 1.5;
        }

        footer {
            text-align: center;
            font-size: 0.85em;
            color: #666;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        footer p {
            margin: 5px 0;
        }

        .preferences-link {
            margin: 20px 0;
            text-align: center; /* Centralizar o link */
        }

        .preferences-link a {
            display: inline-block;
            padding: 10px 15px;
            background-color: #1976d2;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .preferences-link a:hover {
            background-color: #115293;
        }

        /* Centralizar a imagem e o texto do link */
        .preferences-link a {
            display: flex;
            justify-content: center; /* Alinhar horizontalmente */
            align-items: center; /* Alinhar verticalmente */
            text-align: center; /* Garantir que o texto esteja centralizado */
        }

        .preferences-link img {
            margin-right: 8px; /* Espaço entre a imagem e o texto */
        }
    </style>
</head>
<body>

    <!-- Banner -->
    <div class="banner">
        <img src="{{ asset('img/parquesegurobanneremail.png') }}" alt="Banner">
    </div>

    <!-- Conteúdo Principal -->
    <div class="container">
        <h1>Saída realizada para {{ $utente->nome_apelido}}</h1>

        <!-- Data e Hora -->
        <p><strong>Data e Hora:</strong> {{ $dataHoraAcao->format('d/m/Y H:i') }}</p>

@php
use Carbon\Carbon;
use Illuminate\Support\Str;

// Data atual
$hoje = Carbon::today();

// Nomes fixos de programas com ícones (se quiseres personalizar)
$programasCheckbox = [
    '_snipeit_ha_ferias_no_parque_67' => 'Há Férias no Parque',
    '_snipeit_parque_em_movimento_verao_68' => 'Parque em Movimento Verão',
    '_snipeit_parque_em_movimento_pascoa_69' => 'Parque em Movimento Páscoa',
    '_snipeit_aaaf_caf_ferias_pascoa_70' => 'AAAF/CAF Férias Páscoa',
    '_snipeit_aaaf_caf_ferias_verao_71' => 'AAAF/CAF Férias Verão',
    '_snipeit_parque_em_movimento_natal_72' => 'Parque em Movimento Natal',
    '_snipeit_aaaf_caf_ferias_carnaval_73' => 'AAAF/CAF Férias Carnaval',
];

// Lista de programas ativos hoje
$programasHoje = [];

foreach ($utente->getAttributes() as $campo => $valor) {
    if (
        (Str::startsWith($campo, '_snipeit_programa_') || array_key_exists($campo, $programasCheckbox))
        && !empty($valor)
    ) {
        // Nome do programa
        if (isset($programasCheckbox[$campo])) {
            $nomePrograma = $programasCheckbox[$campo];
        } else {
            $nomeRaw = Str::beforeLast(Str::after($campo, '_snipeit_programa_'), '_');
            $nomePrograma = ucwords(str_replace('_', ' ', $nomeRaw));
        }

        // Verificar datas
        $datas = collect(preg_split('/[\r\n,]+/', $valor))
            ->map(fn($d) => trim($d))
            ->filter()
            ->flatMap(function ($item) {
                if (preg_match('/(\d{2}\/\d{2}\/\d{4})\s*[-a]\s*(\d{2}\/\d{2}\/\d{4})/', $item, $m)) {
                    try {
                        $di = Carbon::createFromFormat('d/m/Y', trim($m[1]));
                        $df = Carbon::createFromFormat('d/m/Y', trim($m[2]));
                        return collect()->range(0, $di->diffInDays($df))->map(fn($i) => $di->copy()->addDays($i));
                    } catch (\Exception $e) {
                        return collect();
                    }
                }
                try {
                    return collect([Carbon::createFromFormat('d/m/Y', $item)]);
                } catch (\Exception $e) {
                    return collect();
                }
            });

        if ($datas->contains(fn($d) => $d->isSameDay($hoje))) {
            $programasHoje[] = $nomePrograma;
        }
    }
}
@endphp


<p><strong>Escola/Programas:</strong>
    {{ is_array($utente->company) ? ($utente->company['name'] ?? 'Local não disponível') : ($utente->company->name ?? 'Local não disponível') }}
    @if (!empty($programasHoje))
        — {{ implode(', ', $programasHoje) }}
    @endif
</p>

        <!-- Alerta de incidente para Check-out -->
        @if ($action === 'Saída' && $manutencao)
            <div class="alert-box">
                <strong>⚠️ Alerta de Incidente:</strong>
                <p>Foi reportado um incidente hoje: <strong>{{ $manutencao->title }}</strong></p>
                <p><em>{{ $manutencao->notes }}</em></p>
            </div>
        @endif

        <!-- Informação sobre a saída -->
        @if ($action === 'Saída')
            @if ($nomeResponsavel) <!-- Verifica se há nome da pessoa com autorização excepcional -->
                <div class="alert-box">
                    <p><strong>Saiu com uma pessoa com autorização excepcional:</strong> {{ $nomeResponsavel }}</p>
                    @if ($nrCC)
                        <p><strong>Documento de Identificação:</strong> {{ $nrCC }}</p>
                    @endif
                </div>
            @else
                <div class="alert-box">
                    <strong>⚠️ Informação:</strong>
                    <p>O aluno saiu de forma autónoma. 🚶‍️</p>
                </div>
            @endif
        @endif

        <p><strong>Operação de {{ ucfirst($action) }} realizada por:</strong> 
            {{ $utilizadorBackoffice->first_name ?? 'Não informado' }} {{ $utilizadorBackoffice->last_name ?? '' }}
        </p>

        <!-- Link para o Portal do Encarregado de Educação -->
<div class="preferences-link" style="display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 20px;">
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/ee/login" style="display: inline-flex; align-items: center; padding: 10px 15px; background-color: #1976d2; color: #fff; text-decoration: none; border-radius: 5px; font-size: 0.9em; width: auto;">
        <img src="https://parque-seguro.jf-parquedasnacoes.pt:8126/img/logoportal_ee.png" alt="Logo do Portal EE" style="width: 24px; height: auto; margin-right: 8px;">
        Aceder ao Portal do Encarregado de Educação
    </a>
</div>
      <p>
            Aceda ao <strong>Portal do Encarregado de Educação</strong> para gerir as suas preferências de notificação, atualizar os dados dos seus educandos, adicionar ou editar responsáveis autorizados, e acompanhar todas as informações importantes relacionadas ao seu educando.
            <br>
            Faça login e tenha acesso a uma gestão simples e eficaz das suas autorizações e notificações.
        </p>
    </div>

    <!-- Rodapé -->
    <footer>
        <p>Este é um e-mail gerado automaticamente. Por favor, não responda.</p>
        <p>
            Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações |
            <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer">
                Política de Privacidade
            </a>
        </p>
    </footer>

</body>
</html>
