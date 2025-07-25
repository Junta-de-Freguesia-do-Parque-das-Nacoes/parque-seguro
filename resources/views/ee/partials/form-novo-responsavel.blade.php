<button type="button"
        onclick="toggleNovoResponsavel()"
        class="btn-utente verde"
        style="margin-bottom: 15px; font-size: 1.5rem;">
    ➕ Adicionar Novo Autorizado à Recolha da Criança
</button>


<div id="novoResponsavelWrapper" style="display: none; overflow: hidden; transition: max-height 0.4s ease;">
    <div style="margin-top: 20px; background-color: #f1f9f1; border: 1px solid #ccc; border-radius: 8px; padding: 20px;">
        <h4 style="font-size: 2rem; margin-bottom: 15px;">➕ Adicionar Novo Autorizado</h4>

        <form style="font-size: 1.4rem;" id="formNovoResponsavelGlobal" method="POST" action="{{ route('ee.responsaveis.criar-global') }}" enctype="multipart/form-data">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px;">
                <div>
                    <label>Foto</label>
                    <input type="file" name="foto" accept="image/*" onchange="previewFotoGlobal(event)" class="form-control">
                    <img id="preview-foto-global" src="{{ asset('img/anonimoadulto.png') }}" alt="Preview" style="max-height: 100px; margin-top: 10px; border-radius: 5px; border: 1px solid #ccc;">
                </div>

                <div>
                    <label>Nr Identificação *</label>
                    <input type="text" name="nr_identificacao" class="form-control" required>
                </div>

                <div>
                    <label>Nome Completo *</label>
                    <input type="text" name="nome_completo" class="form-control" required>
                </div>

                <div>
                    <label>Contacto</label>
                    <input type="text" name="contacto" class="form-control">
                </div>

                <div>
                    <label>Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-success" style="font-size: 1.4rem;">💾 Criar e Associar</button>

        </form>
    </div>
</div>

@php
    $fallbackFotoUrl = asset('img/anonimoadulto.png');
@endphp
@push('js')
<script>
const fallbackFotoUrl = @json($fallbackFotoUrl);

function toggleNovoResponsavel() {
    const wrapper = document.getElementById('novoResponsavelWrapper');
    if (!wrapper) return;

    if (wrapper.style.display === 'none' || wrapper.style.display === '') {
        wrapper.style.display = 'block';
        wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
    } else {
        wrapper.style.maxHeight = 0;
        setTimeout(() => wrapper.style.display = 'none', 400);
    }
}

function previewFotoGlobal(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-foto-global');
    if (file && preview) {
        const reader = new FileReader();
        reader.onload = (e) => preview.src = e.target.result;
        reader.readAsDataURL(file);
    }
}

function adicionarResponsavelAoDOM(responsavel) {
    const container = document.querySelector('#listaResponsaveis');
    if (!container) return;

    const card = document.createElement('div');
    card.classList.add('card-responsavel');
    card.id = 'responsavel-card-' + responsavel.id;
    card.innerHTML = `
        <div style="display: flex; gap: 20px; align-items: flex-start;">
            <div class="col-foto">
                <div class="foto-wrapper">
                    <img src="${responsavel.foto_url || fallbackFotoUrl}"
                         alt="Foto de ${responsavel.nome_completo}"
                         class="utente-foto">
                </div>
            </div>
            <div class="col-info" style="flex-grow: 1;">
                <div class="utente-nome">${responsavel.nome_completo}</div>
                <div class="utente-info" style="font-size: 1.4rem; margin-bottom: 10px;">
                    📇 <strong>Nº Identificação: </strong> ${responsavel.nr_identificacao}<br>
                    📧 <strong>E-mail: </strong> ${responsavel.email || '-'}<br>
                    📞 <strong>Contacto: </strong> ${responsavel.contacto || '-'}
                </div>
                <button type="button" class="btn-utente azul" onclick="editarResponsavel(${responsavel.id})">
                    ✏️ Editar dados do autorizado
                </button>
                <button type="button" class="btn-utente btn-danger" onclick="removerResponsavel(${responsavel.id})" style="margin-left: 10px;">
                    🗑️ Remover autorizado
                </button>
            </div>
        </div>
    `;

    container.prepend(card);
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formNovoResponsavelGlobal');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(form);
            const url = form.getAttribute('action');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();

                if (!res.ok) {
                    const mensagens = Object.values(data.errors || {}).flat().join('\n');
                    mostrarToast('⚠️ ' + mensagens, 'error');
                    throw new Error('Erro de validação');
                }

                mostrarToast(data.message || '✅ Autorizado criado.', 'success');

                form.reset();
                document.getElementById('preview-foto-global').src = '{{ asset('img/anonimoadulto.png') }}';
                toggleNovoResponsavel();

                setTimeout(() => window.location.reload(), 800);
            })
            .catch(error => {
                if (error.message !== 'Erro de validação') {
                    console.error('Erro inesperado:', error);
                    mostrarToast('❌ Erro inesperado ao criar o responsável.', 'error');
                }
            });
        });
    }
});

function mostrarToast(mensagem, tipo = 'success') {
    const toast = document.createElement('div');
    toast.innerText = mensagem;
    toast.style.position = 'fixed';
    toast.style.bottom = '30px';
    toast.style.right = '30px';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '8px';
    toast.style.fontSize = '1.4rem';
    toast.style.color = 'white';
    toast.style.zIndex = 9999;
    toast.style.boxShadow = '0 4px 10px rgba(0,0,0,0.2)';
    toast.style.opacity = 0;
    toast.style.transition = 'opacity 0.4s ease';
    toast.style.backgroundColor = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107'
    }[tipo] || '#17a2b8';
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.style.opacity = 1);
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.remove(), 500);
    }, 3500);
}
</script>

@endpush
