@extends('layouts.default')

@section('content')
    <div class="container">
        <h1>Importação de Dados CSV</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulário para Carregar o CSV --}}
        <form action="{{ route('importacao.index') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="arquivo">Selecione o arquivo CSV</label>
                <input type="file" name="arquivo" id="arquivo" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="fonte">Fonte dos Dados</label>
                <select name="fonte" id="fonte" class="form-control" required>
                    <option value="edubox">Edubox</option>
                    <option value="site">Site</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Pré-visualizar</button>
        </form>

        {{-- Exibir erros de validação --}}
        @if($errors->has('arquivo'))
            <div class="alert alert-danger">
                {{ $errors->first('arquivo') }}
            </div>
        @endif

        {{-- Exibir Pré-visualização dos Dados --}}
        @if(isset($preview))
            <h3>Pré-visualização dos Dados</h3>
            <table class="table">
                <thead>
                    <tr>
                        @foreach(array_keys($preview[0]) as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($preview as $row)
                        <tr>
                            @foreach($row as $data)
                                <td>{{ $data }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Formulário para Confirmar a Importação --}}
            <form action="{{ route('importacao.importar') }}" method="POST">
                @csrf
                <input type="hidden" name="arquivo_path" value="{{ Storage::path($filePath) }}">
                <input type="hidden" name="fonte" value="{{ request()->input('fonte') }}">
                <button type="submit" class="btn btn-success">Confirmar Importação</button>
            </form>
        @endif
    </div>
@endsection
