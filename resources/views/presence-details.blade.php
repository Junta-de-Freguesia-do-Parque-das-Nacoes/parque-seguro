@extends('layout-qrcode')

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status_id == 23 ? 'Presentes' : 'Ausentes' }} em {{ $schoolName }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            padding-bottom: 60px; /* Espaço suficiente para o rodapé */
        }

        h1 {
            text-align: center;
            color: #004080;
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Responsivo */
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            text-align: center;
        }

        .card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .card .details {
            padding: 10px;
        }

        .card .details h3 {
            margin: 10px 0;
            color: #333;
            font-size: 1em;
        }

        .fixed-buttons {
            position: fixed;
            left: 0;
            bottom: 15px; /* Espaço suficiente acima do rodapé */
            width: 100%;
            background-color: #ffffff;
            padding: 15px 0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
        }
		

        .button-container a {
            text-decoration: none;
        }

        .button-blue {
            display: inline-block;
            padding: 12px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .fixed-title {
    position: fixed;
    top: 22px;

    left: 0;
    width: 100%;
    background-color: white; /* Garante que não sobreponha outros conteúdos */
    padding: 10px;
    text-align: center;
    font-size: 1.5em;
    font-weight: bold;
    z-index: 1000; /* Garante que fica acima de outros elementos */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Adiciona sombra para destaque */
    
}
.title-spacer {
    height: 60px; /* Ajuste este valor para igualar à altura do título */
}
        .button-blue:hover {
            background-color: #45a049;
        }

        @media screen and (max-width: 600px) {
            h1 {
                font-size: 1.5em;
            }

            .card .details h3 {
                font-size: 0.9em; /* Ajusta o tamanho da fonte */
            }

            .button-blue {
                padding: 10px 15px; /* Reduz o tamanho dos botões em telas menores */
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
@section('content')

<h1 class="fixed-title">{{ $status_id == 23 ? 'Presentes' : 'Ausentes' }} em {{ $schoolName }}</h1>

<div class="title-spacer"></div>

<!-- Formulário de pesquisa -->
<form method="GET" action="{{ route('presence.details') }}" style="text-align: center; margin-bottom: 20px; display: flex; justify-content: center; gap: 10px;">
    <input type="hidden" name="school_id" value="{{ request('school_id') }}">
    <input type="hidden" name="status_id" value="{{ request('status_id') }}">

    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome..." 
           style="padding: 8px; width: 50%; border-radius: 5px; border: 1px solid #ccc;">
    
    <button type="submit" style="padding: 8px 15px; border-radius: 5px; border: none; background-color: #004080; color: white; cursor: pointer;">
        Pesquisar
    </button>

    @if(request('search'))
        <a href="{{ route('presence.details', ['school_id' => request('school_id'), 'status_id' => request('status_id')]) }}" 
           style="padding: 8px 15px; border-radius: 5px; border: none; background-color: #ccc; color: black; text-decoration: none; cursor: pointer;">
            Limpar
        </a>
    @endif
</form>
@if(!empty($message))
    <p style="text-align: center; color: red;">{{ $message }}</p>
@else
    <div class="cards first-content">
        @if($assets->isEmpty())
            <p style="text-align: center; color: gray;">Nenhum aluno encontrado.</p>
        @else
            @foreach($assets as $asset)
                <div class="card">
                    <img src="{{ $asset->image ? route('assets.foto', ['filename' => basename($asset->image)]) : asset('img/anoninochild.jpg') }}" alt="{{ $asset->name }}">
                    <div class="details">
                        <h3>{{ $asset->name }}</h3>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endif


    <div class="fixed-buttons">
        <div class="button-container">
            <a href="{{ route('qr-code-scanner') }}" class="button-blue">Voltar para o Leitor de QR Code</a>
        </div>
    </div>
@endsection
</body>
</html>
