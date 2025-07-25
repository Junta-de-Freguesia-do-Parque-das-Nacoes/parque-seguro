<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>Idade</th>
            <th>Inscrição {{ $programaLabel }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assets as $asset)
            @php
                $dataNascimento = $asset->_snipeit_data_nascimento_34;
                $idade = $dataNascimento ? \Carbon\Carbon::parse($dataNascimento)->age : '';
            @endphp
            <tr>
                <td>{{ $asset->name }}</td>
                <td>{{ $idade }}</td>
                <td>{{ $asset->{$programa} }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
