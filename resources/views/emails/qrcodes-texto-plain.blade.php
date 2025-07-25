Olá {{ $responsavel->nome_completo }},

Segue em anexo os QR Codes dos seguintes educandos:

@forelse ($utentes as $item)
    @php $utente = $item['model']; @endphp
    - {{ $utente->name }} (ficheiro: {{ $item['ficheiro'] }})
@empty
    - Nenhum educando encontrado.
@endforelse

—
Núcleo de Sistemas de Informação  
JF Parque das Nações
