let diferencialIndex = 0;

const diferenciaisDisponiveis = window.diferenciaisOptions || [];

function criarOptions() {
    let html = `
        <option value="">- Selecione -</option>
    `;

    diferenciaisDisponiveis.forEach((item) => {
        html += `
            <option value="${item.id}">
                ${item.nome}
            </option>
        `;
    });

    html += `
        <option value="personalizado">
            Outros (personalizado)
        </option>
    `;

    return html;
}

function adicionarDiferencial() {
    const container = document.getElementById('diferenciaisContainer');

    const html = `
        <div class="diferencial-item" data-index="${diferencialIndex}">
            <div class="form-field">
                <label class="form-label">
                    Tipo de diferencial
                </label>

                <select
                    class="form-select diferencial-select"
                    name="diferenciais[${diferencialIndex}][selecionado]"
                    onchange="togglePersonalizado(this, ${diferencialIndex})"
                >
                    ${criarOptions()}
                </select>
            </div>

            <div
                class="personalizado-fields"
                id="personalizado-${diferencialIndex}"
                style="display:none;"
            >
                <div class="form-field">
                    <label class="form-label">
                        Nome personalizado
                    </label>

                    <input
                        type="text"
                        class="form-input"
                        name="diferenciais[${diferencialIndex}][titulo]"
                    >
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Ícone
                    </label>

                    <input
                        type="text"
                        class="form-input"
                        name="diferenciais[${diferencialIndex}][icone]"
                    >
                </div>

                <div class="form-field">
                    <label class="form-label">
                        Descrição
                    </label>

                    <textarea
                        class="form-textarea"
                        name="diferenciais[${diferencialIndex}][descricao]"
                    ></textarea>
                </div>
            </div>

            <button
                type="button"
                class="btn-form btn-form--outline"
                onclick="removerDiferencial(${diferencialIndex})"
            >
                Remover
            </button>

            <hr>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);

    diferencialIndex++;
}

window.togglePersonalizado = function (select, index) {
    const box = document.getElementById(`personalizado-${index}`);

    if (select.value === 'personalizado') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
};

window.removerDiferencial = function (index) {
    const item = document.querySelector(
        `[data-index="${index}"]`
    );

    if (item) {
        item.remove();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnAddDiferencial');

    if (btn) {
        btn.addEventListener('click', adicionarDiferencial);
    }

    adicionarDiferencial();
});