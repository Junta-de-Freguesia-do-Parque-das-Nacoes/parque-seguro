<!-- Modal de confirmação de limpeza -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar limpeza</h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        Tens a certeza que queres limpar as inscrições dos utentes selecionados no programa
        <strong>{{ $program_fields[$programaSelecionado] ?? '' }}</strong>?<br>
        <span class="text-danger">Esta ação não pode ser desfeita.</span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmarLimpeza">Sim, limpar</button>

      </div>
    </div>
  </div>
</div>
