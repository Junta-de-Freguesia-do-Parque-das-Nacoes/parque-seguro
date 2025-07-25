@extends('layout-qrcode')
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de QR Code</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0px;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
		overflow-x: hidden;
    }
	header {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 60px;
    background-color: #004080;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    text-align: center;
    font-size: 1.2em;
}

    h1 {
        font-size: 1.8em;
        margin: 10px 0;
        color: #004080;
        text-align: center;
    }

    .container {
        width: 100%;
        max-width: 600px;
        text-align: center;
        margin: 0px auto;
    }



    #qr-result {
        font-size: 1.2em;
        color: green;
        font-weight: bold;
        margin-top: 10px;
        text-align: center;
    }

    .presence-section {
        width: 100%;
        max-width: 600px;
        margin: 20px auto;
        background: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .presence-header {
        background-color: #004080;
        color: white;
        padding: 15px;
        font-size: 1.2em;
        text-align: center;
    }

    .presence-table {
        width: 100%;
        border-collapse: collapse;
    }

    .presence-table th,
    .presence-table td {
        padding: 15px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .presence-table th {
        background-color: #f0f0f0;
        font-weight: bold;
    }

    .presence-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .presence-table tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .presence-table tr:hover {
        background-color: #f1f1f1;
    }

    .btn-presence {
        display: inline-block;
        padding: 10px 15px;
        background-color: #004080;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1em;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .btn-presence:hover {
        background-color: #45a049;
    }

    

    .no-records {
        text-align: center;
        color: #999;
        font-size: 1.2em;
        margin-top: 20px;
    }



    /* Ajustes de alinhamento para dispositivos móveis */
    @media screen and (max-width: 600px) {
        h1 {
            font-size: 1.5em;
        }
video {
    display: block; /* Remove espaços ao redor */
    width: 38%; /* Ocupa toda a largura disponível no container */
    max-width: 400px; /* Define um tamanho máximo para telas maiores */
    height: auto; /* Mantém a proporção correta */
    aspect-ratio: 4 / 3; /* Garante proporção fixa */
    border: 2px solid #004080;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    margin: 0 auto; /* Centraliza o vídeo */
}
       
    }
</style>




</head>
<body>


    <div class="container">
        <h1>Scanner de QR Code</h1>
        <video id="video" autoplay playsinline></video>
        <canvas id="qr-canvas" style="display: none;"></canvas>
        <div id="qr-result"></div>
    </div>

    <div class="presence-section">
        <div class="presence-header">Presenças e Ausências</div>
        <table class="presence-table">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Presentes</th>
                    <th>Ausentes</th>
                </tr>
            </thead>
            <tbody>
    @foreach ($presenceData as $data)
        @php
            $isProgram = !empty($data->is_program) && $data->is_program;
            $total = $data->total_present + $data->total_absent;
        @endphp

        @if ($total === 0)
            @continue
        @endif

        @if ($isProgram)
            <tr style="background-color: #e0f7fa;">
        @else
            <tr>
        @endif
            <td>{{ $data->school_name }}</td>
            <td>
                <a href="{{ route('presence.details', ['school_id' => $data->school_id, 'status_id' => 23]) }}" class="btn-presence">
                    {{ $data->total_present }}
                </a>
            </td>
            <td>
                <a href="{{ route('presence.details', ['school_id' => $data->school_id, 'status_id' => 25]) }}" class="btn-presence">
                    {{ $data->total_absent }}
                </a>
            </td>
        </tr>
    @endforeach
</tbody>


        </table>
    </div>


    <script src="js/jsQR.js"></script>
    <script>
        const video = document.getElementById('video');
        const canvasElement = document.getElementById('qr-canvas');
		const canvas = canvasElement.getContext('2d', { willReadFrequently: true });
       
        const qrResult = document.getElementById('qr-result');

        function startCamera() {
            video.style.height = "200px"; // Define altura fixa inicial
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function (stream) {
                    video.srcObject = stream;
                    video.setAttribute('playsinline', true);
                    video.play();
                    scanQRCode();
                })
                .catch(function (err) {
                    qrResult.textContent = "Erro ao acessar a câmera: " + err.message;
                });
        }

        function scanQRCode() {
            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                canvasElement.width = video.videoWidth;
                canvasElement.height = video.videoHeight;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });

                if (code) {
                    qrResult.textContent = "QR Code detectado: " + code.data;
                    window.location.href = code.data;
                } else {
                    requestAnimationFrame(scanQRCode);
                }
            } else {
                requestAnimationFrame(scanQRCode);
            }
        }

        window.onload = startCamera;
    </script>
	
</body>
</html>
