@extends('layouts.default')

@section('title', 'Gestão Inscrições por Programa')
@section('content')
<div class="box">
    <div class="box-body">

        {{-- Mensagens de sucesso ou erro --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Informação de contexto --}}
<div class="alert alert-info">
    <strong>ℹ️ Informação:</strong><br>
    Para renovar as datas de um programa, é necessário primeiro <strong>limpar as inscrições existentes</strong> dos utentes.<br>
    Não te preocupes! As datas anteriores já frequentadas estão <strong>guardadas no histórico do utente</strong> e não se perdem.
</div>

        {{-- Filtro de Programa --}}
        <form method="GET" action="{{ route('programas.gestao') }}" id="filtroProgramaForm" class="form-inline mb-4">
            <label for="programa" class="mr-2 font-weight-bold">Escolhe o programa:</label>
            <select name="programa" id="programa" class="form-control mr-2" onchange="document.getElementById('filtroProgramaForm').submit();">
                <option value="">-- Seleciona --</option>
                @foreach($program_fields as $key => $label)
                    <option value="{{ $key }}" {{ $programaSelecionado == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>

        {{-- Listagem de utentes inscritos --}}
        @if($programaSelecionado && $assets->count())
        <form id="limparForm" action="{{ route('programas.gestao.post') }}" method="POST">
            @csrf
            <input type="hidden" name="programa" value="{{ $programaSelecionado }}">

            <div class="mb-2">
                <strong>Utentes inscritos:</strong> {{ $assets->count() }}
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Nome</th>
                        <th>Inscrição {{ $program_fields[$programaSelecionado] ?? '' }}</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                        <tr>
                            <td><input type="checkbox" name="asset_ids[]" value="{{ $asset->id }}"></td>
                            <td>{{ $asset->name }}</td>
                            <td>{{ $asset->{$programaSelecionado} }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Botão para acionar modal de limpeza --}}
            <button type="button" class="btn btn-danger mt-2" id="abrirModal">
                🧹 Limpar inscrições selecionadas
            </button>
            <a href="{{ route('programas.exportar', ['programa' => $programaSelecionado]) }}" class="btn btn-outline-success mb-3">
    📤 Exportar para Excel
</a>


            {{-- Modal de confirmação --}}
            @include('programas.partials.modal_confirmar_limpeza', ['programaSelecionado' => $programaSelecionado, 'program_fields' => $program_fields])

            {{-- Modal sem seleção --}}
            @include('programas.partials.modal_sem_selecao')

        </form>
        @elseif($programaSelecionado)
            <p><em>Não existem utentes inscritos neste programa.</em></p>
        @endif

        {{-- Secção de edição de valores --}}
        @php
            $customField = \App\Models\CustomField::where('db_column', $programaSelecionado)->first();
            $temInscricoes = \App\Models\Asset::whereNotNull($programaSelecionado)->exists();
        @endphp

        @if($customField)
            <hr>
            <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="modal-title" id="modalOpcoesLabel">
    Editar as datas do Programa <strong>{{ $program_fields[$programaSelecionado] ?? '' }}</strong>
</h5>

                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#modalOpcoes">
    ✏️ Editar valores
</button>
            </div>
        @endif
    </div>
</div>

{{-- Modal de edição de opções --}}
@if($customField)
<div class="modal fade" id="modalOpcoes" tabindex="-1" aria-labelledby="modalOpcoesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" action="{{ route('programas.opcoes.atualizar', ['field_id' => $customField->id]) }}">
        @csrf
        @method('PUT')
        <div class="modal-content">
          <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalOpcoesLabel">
    Editar as datas do Programa <strong>{{ $program_fields[$programaSelecionado] ?? '' }}</strong>
</h5>

            
          </div>
          <div class="modal-body">
              <div class="mb-3">
                  <label for="valores"><strong>Datas do programa</strong></label>
                  <textarea name="valores" class="form-control" rows="8" {{ $temInscricoes ? 'disabled' : '' }}>{{ old('valores', $customField->field_values) }}</textarea>
              </div>
              <div class="alert alert-info">
                  <strong>Formato das datas:</strong><br>
                  - Uma data por linha (ex: <code>10/04/2025</code>)<br>
                  - Para um intervalo de dias, usa o formato <code>10/04/2025-14/04/2025</code>
              </div>

              @if($temInscricoes)
                  <div class="alert alert-warning mt-3">
                      ⚠️ Não é possível editar os valores enquanto existirem utentes com inscrições neste programa.<br>
                      Para poder editar, <strong>limpa primeiro todas as inscrições</strong>.
                  </div>
              @endif
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
    ❌ Fechar
</button>


            @if(!$temInscricoes)
              <button type="submit" class="btn btn-success">💾 Atualizar</button>
            @endif
          </div>
        </div>
    </form>
  </div>



</div>
@endif
@stop

@section('moar_scripts')
    @include('partials.bootstrap-table')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const abrirModal = document.getElementById('abrirModal');
    const confirmarLimpeza = document.getElementById('confirmarLimpeza');

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('input[name="asset_ids[]"]').forEach(cb => cb.checked = this.checked);
        });
    }

    if (abrirModal) {
        abrirModal.addEventListener('click', function () {
            const selecionados = document.querySelectorAll('input[name="asset_ids[]"]:checked');
            if (selecionados.length === 0) {
                $('#noSelectionModal').modal('show');
            } else {
                $('#confirmModal').modal('show');
            }
        });
    }

    if (confirmarLimpeza) {
        confirmarLimpeza.addEventListener('click', function () {
            document.getElementById('limparForm').submit();
        });
    }
});

    </script>
@stop
