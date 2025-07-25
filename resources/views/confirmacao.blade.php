@extends('layout-qrcode')
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            height: 100vh;
            margin: 0;
            background-color: #eaeaea;
        }

        .container {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            flex: 1;
        }

        h1 {
            color: #004080;
            margin-bottom: 20px;
        }

        .message {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }

            /* Botões fixos logo acima do rodapé */
    .fixed-buttons {
        position: fixed;
        left: 0;
        bottom: 20px; /* Espaço suficiente acima do rodapé */
        width: 100%;
        background-color: #ffffff;
        padding: 20px 0;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        text-align: center;
    }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .button-blue {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .button-blue:hover {
            background-color: #0056b3;
        }

        
    </style>
</head>
<body>
@section('content')
    <div class="container">
        <h1>Operação realizada com sucesso!</h1>
        <p class="message">{{ session('status') }}</p>
    </div>

    <div class="fixed-buttons">
        <div class="button-container">
            <a href="{{ route('qr-code-scanner') }}" class="button-blue">Voltar para o Leitor de QR Code</a>
        </div>
    </div>

@endsection
</body>
</html>
