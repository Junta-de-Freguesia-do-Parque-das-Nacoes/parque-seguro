<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code para {{ $asset->name }}</title>
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

        .qr-box {
            text-align: center;
            margin: 20px 0;
        }

        .qr-box img {
            width: 200px;
            height: 200px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .highlight {
            font-weight: bold;
            color: #333;
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
            text-align: center;
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

        .responsaveis {
            margin-top: 20px;
        }

        .responsavel {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .responsavel img {
            width: 80px;  /* Largura padrão para foto tipo passe */
            height: 100px; /* Altura maior para formato passe */
            object-fit: cover; /* Garante que a imagem preenche sem deformar */
            border-radius: 8px; /* Mantém bordas arredondadas */
            border: 1px solid #ddd; /* Adiciona uma borda suave */
            margin-right: 15px; /* Espaço entre a foto e o texto */
        }

        .responsavel-info {
            font-size: 0.9em;
        }

        .estado-autorization {
            display: inline-block;
            font-size: 0.9em;
            margin-top: 10px;
        }

        .badge {
    padding: 0;               /* Retirar o padding para deixar menor */
    border-radius: 50%;       /* Manter o border-radius para ser circular */
    display: inline-block;    /* Para o badge ficar no mesmo linha com o texto */
    width: 8px;               /* Tamanho menor, pode ajustar conforme necessário */
    height: 8px;              /* Tamanho menor, igual ao width */
    margin-right: 5px;        /* Margem para separar do próximo elemento */
}


.label-success { 
    background-color: #28a745; /* verde para autorizado */
}

.label-warning { 
    background-color: #ffc107; /* amarelo para pendente */
}

.label-danger { 
    background-color: #dc3545; /* vermelho para expirado */
}

.label-secondary { 
    background-color: #6c757d; /* cinza para estado não definido */
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
        <h1>QR Code para {{ $asset->nome_apelido }}</h1>

        <!-- Caixa de Aviso Amarelo -->
        <div class="alert-box">
            <p><strong>Atenção:</strong> Pode partilhar o QR Code com as pessoas previamente autorizadas para a recolha da criança.</p>
        </div>

        <!-- Exibir QR Code -->
        <div class="qr-box">
            <img src="{{ $publicUrl }}" alt="QR Code">
        </div>

        <!-- Lista de Responsáveis -->
        <div class="responsaveis">
            <h2>Registados para a recolha da criança</h2>
            @forelse ($responsaveis as $responsavel)
                <div class="responsavel">
                    <img src="{{ $responsavel->image ? asset($responsavel->image) : asset('img/anonimoadulto.png') }}" alt="Foto do Responsável">
                    <div class="responsavel-info">
                        <p><strong>{{ $responsavel->name }}</strong></p>
                        <p>Parentesco: {{ $responsavel->grau_parentesco ?? 'Não informado' }}</p>

                        <!-- Exibir estado de autorização, início e fim na mesma linha -->
                        <div class="autorization-info">
                            <p class="estado-autorization">
                                <!-- Estado -->
                                <span class="badge 
                                    @if ($responsavel->estado_autorizacao == 'Autorizado') 
                                        label-success
                                    @elseif ($responsavel->estado_autorizacao == 'Nao Iniciado') 
                                        label-warning
                                    @elseif ($responsavel->estado_autorizacao == 'Autorizacao Expirada') 
                                        label-danger
                                    @else 
                                        label-secondary
                                    @endif">
                                    &nbsp;
                                </span>
                                Estado: {{ ucfirst($responsavel->estado_autorizacao) }}

                                <!-- Início e Fim -->
                                <span>
                                    <strong>Início:</strong> 
                                    {{ isset($responsavel->data_inicio_autorizacao) ? \Carbon\Carbon::parse($responsavel->data_inicio_autorizacao)->format('d/m/Y') : 'Não definida' }}
                                </span> 
                                &nbsp;|&nbsp;
                                <span>
                                    <strong>Fim:</strong> 
                                    {{ isset($responsavel->data_fim_autorizacao) ? \Carbon\Carbon::parse($responsavel->data_fim_autorizacao)->format('d/m/Y') : 'Não definida' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: red;">Nenhum responsável autorizado definido.</p>
            @endforelse
        </div>

        <!-- Link para Configuração de Preferências -->
<div class="preferences-link" style="display: flex; justify-content: center; align-items: center; width: 100%; margin-top: 20px;">
    <a href="https://parque-seguro.jf-parquedasnacoes.pt:8126/ee/login" style="display: inline-flex; align-items: center; padding: 10px 15px; background-color: #1976d2; color: #fff; text-decoration: none; border-radius: 5px; font-size: 0.9em; width: auto;">
        <img src="https://parque-seguro.jf-parquedasnacoes.pt:8126/img/logoportal_ee.png" alt="Logo do Portal EE" style="width: 24px; height: auto; margin-right: 8px;">
        Aceder ao Portal do Encarregado de Educação
    </a>
</div>
    </div>

    <!-- Rodapé -->
    <footer>
    <p>Este é um e-mail gerado automaticamente. Por favor, não responda.</p>
    <p>
        Núcleo Sistemas de Informação © 2016–{{ date('Y') }} JF-Parque das Nações|
        <a href="https://www.jf-parquedasnacoes.pt/termosecondicoes" target="_blank" rel="noopener noreferrer">
            Política de Privacidade
        </a>
    </p>
</footer>
</body>
</html>
