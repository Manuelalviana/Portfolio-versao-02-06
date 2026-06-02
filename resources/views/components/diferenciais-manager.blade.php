@php
    // Extrai os nomes dos ícones disponíveis
    $iconNames = collect($icones)->pluck('name')->filter()->unique()->values()->all();
    
    if (empty($iconNames) && is_array($icones)) {
        $iconNames = $icones;
    }
    
    $diferenciaisPadraoNomes = [
        'Adaptável', 'Amplo espectro', 'Automatizado', 'Autônomo', 'Baixa concorrência',
        'Customizável', 'Digital', 'Diminuição toxicidade', 'Escalonáveis', 'Especificidade',
        'Fácil manuseio', 'Georreferenciamento', 'Gerenciamento de dados', 'Inteligência artificial',
        'Linguagem simples', 'Maior sensibilidade', 'Mais eficiente', 'Menor custo',
        'Menores efeitos colaterais', 'Microdispositivos', 'Modelo validado', 'Multiparamétrico',
        'Plataforma tecnológica', 'Portátil', 'Preciso', 'Prestação de serviço especializado',
        'Produto Estável', 'Redução de custos', 'Redução de tempo', 'Reprodutíveis',
        'Saúde global', 'Segurança', 'Sem uso de animais', 'Sem uso de animais para experimentação',
        'Sustentável', 'Tecnologia Nacional', 'Teleatendimento', 'Tempo real'
    ];
    
    $diferenciaisPadrao = collect($diferenciaisDisponiveis)
        ->filter(function ($item) use ($diferenciaisPadraoNomes) {
            // suporta objeto Eloquent e array
            $nome = is_array($item) ? ($item['nome'] ?? null) : $item->nome;
            return in_array($nome, $diferenciaisPadraoNomes);
        })
        ->map(function ($item) {
            // suporta objeto Eloquent e array
            $id    = is_array($item) ? ($item['id']    ?? null) : $item->id;
            $nome  = is_array($item) ? ($item['nome']  ?? '')   : $item->nome;
            $icone = is_array($item) ? ($item['icone'] ?? 'star') : ($item->icone ?? 'star');
            return [
                'id'    => $id,
                'nome'  => $nome,
                'icone' => $icone,
            ];
        })
        ->values()
        ->all();
    
    // selectedIds: suporta coleção de objetos e array de arrays
    $selectedIds = collect($oldDiferenciais)
        ->map(fn($i) => is_array($i) ? ($i['id'] ?? null) : ($i->id ?? null))
        ->filter()
        ->values()
        ->all();

    if (empty($selectedIds)) {
        $selectedIds = collect($selectedDiferenciais)
            ->map(fn($i) => is_array($i) ? ($i['id'] ?? null) : ($i->id ?? null))
            ->filter()
            ->values()
            ->all();
    }
    
    // customSelected: itens sem id (personalizados)
    $customSelected = collect($oldDiferenciais)
        ->filter(function ($item) {
            $id   = is_array($item) ? ($item['id']   ?? null) : ($item->id   ?? null);
            $tipo = is_array($item) ? ($item['tipo'] ?? null) : ($item->tipo ?? null);
            return is_null($id) || $tipo === 'personalizado';
        })
        ->values()
        ->all();

    if (empty($customSelected)) {
        $customSelected = collect($selectedDiferenciais)
            ->filter(function ($item) {
                $id   = is_array($item) ? ($item['id']   ?? null) : ($item->id   ?? null);
                $tipo = is_array($item) ? ($item['tipo'] ?? null) : ($item->tipo ?? null);
                return is_null($id) || $tipo === 'personalizado';
            })
            ->values()
            ->all();
    }
@endphp

<div class="form-section" x-data="diferenciaisSelect()" x-init="init()">
    <h3 class="form-section-title"><b> Diferenciais </b></h3>
    <p class="form-hint">Selecione os diferenciais que se aplicam à sua tecnologia:</p>

    <!-- Select customizado com ícones -->
    <div class="form-field">
        <label class="form-label">Adicionar diferencial</label>
        
        <div class="custom-select-wrapper">
            <!-- Dropdown button -->
            <button type="button" class="custom-select-button" @click="toggleDropdown()">
                <template x-if="selectedOption">
                    <span class="selected-option-display">
                        <span class="material-symbols-outlined" x-text="selectedOption.icone || 'help_outline'"></span>
                        <span x-text="selectedOption.nome || 'Selecione...'"></span>
                    </span>
                </template>
                <template x-if="!selectedOption">
                    <span>- Selecione um diferencial -</span>
                </template>
                <span class="material-symbols-outlined select-arrow" :class="{ 'rotated': showDropdown }">expand_more</span>
            </button>
            
            <!-- Dropdown menu com ícones -->
            <div class="custom-select-dropdown" x-show="showDropdown" x-cloak @click.away="showDropdown = false">
                <div class="dropdown-search">
                    <span class="material-symbols-outlined">search</span>
                    <input type="text" x-model="searchQuery" placeholder="Buscar diferencial..." class="dropdown-search-input" @click.stop>
                </div>
                
                <div class="dropdown-options">
                    <!-- Optgroup: Diferenciais Padrão -->
                    <div class="dropdown-optgroup">
                        <div class="dropdown-optgroup-label">Diferenciais Padrão</div>
                        <template x-for="diff in filteredPadrao" :key="diff.id">
                            <div class="dropdown-option" @click="selectOption(diff)">
                                <span class="material-symbols-outlined" x-text="diff.icone"></span>
                                <span x-text="diff.nome"></span>
                                <span class="material-symbols-outlined check-mark" x-show="isSelected(diff.id)">check</span>
                            </div>
                        </template>
                        <div x-show="filteredPadrao.length === 0" class="dropdown-empty">
                            Nenhum diferencial encontrado
                        </div>
                    </div>
                    
                    <!-- Optgroup: Criar Novo -->
                    <div class="dropdown-optgroup">
                        <div class="dropdown-optgroup-label">Criar Novo</div>
                        <div class="dropdown-option option-create" @click="openCustomModal()">
                            <span class="material-symbols-outlined">add</span>
                            <span>Criar diferencial personalizado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de diferenciais selecionados (cards) -->
    <div class="selected-diferenciais-list" x-show="selectedDiferenciais.length > 0">
        <div class="diferenciais-grid">
            <template x-for="(diff, idx) in selectedDiferenciais" :key="idx">
                <div class="differencial-card selected">
                    <div class="diferencial-card-content">
                        <div class="diferencial-icon">
                            <span class="material-symbols-outlined" x-text="diff.icone"></span>
                        </div>
                        <div class="diferencial-info">
                            <span class="diferencial-nome" x-text="diff.nome"></span>
                            <span class="diferencial-tipo" x-text="diff.tipo === 'padrao' ? 'Padrão' : 'Personalizado'"></span>
                        </div>
                        <div class="diferencial-actions">
                            <button type="button" class="btn-remove" @click="removeDiferencial(idx)" aria-label="Remover">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Campos hidden para envio no form -->
                    <input type="hidden" :name="'diferenciais[' + idx + '][id]'" :value="diff.tipo === 'padrao' ? diff.id : ''">
                    <input type="hidden" :name="'diferenciais[' + idx + '][nome]'" :value="diff.nome">
                    <input type="hidden" :name="'diferenciais[' + idx + '][icone]'" :value="diff.icone">
                    <input type="hidden" :name="'diferenciais[' + idx + '][tipo]'" :value="diff.tipo">
                    <template x-if="diff.tipo === 'personalizado'">
                        <input type="hidden" :name="'diferenciais[' + idx + '][descricao]'" :value="diff.descricao">
                    </template>
                </div>
            </template>
        </div>
        
        <p class="form-hint" style="margin-top: 0.5rem;">
            <span x-text="selectedDiferenciais.length"></span> diferencial(is) selecionado(s)
        </p>
    </div>

    <!-- Modal para criar diferencial personalizado -->
    <div x-show="showCustomModal" class="modal-overlay" x-cloak>
        <div class="modal-content" @click.away="closeModal()">
            <div class="modal-header">
                <h3 class="modal-title">Criar diferencial personalizado</h3>
                <button type="button" class="modal-close" @click="closeModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            
            <div class="modal-body">
                <div class="form-field">
                    <label class="form-label">Nome do diferencial <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        x-model="customDiferencial.nome" 
                        class="form-input" 
                        maxlength="40" 
                        placeholder="Digite o nome do diferencial"
                    >
                    <div class="form-counter" x-text="(40 - (customDiferencial.nome || '').length) + ' caracteres restantes'"></div>
                </div>

                <div class="form-field">
                    <label class="form-label">Ícone</label>
                    <div class="diferencial-icone-row">
                        <div class="diferencial-icone-preview">
                            <span class="material-symbols-outlined" x-text="customDiferencial.icone || 'help'"></span>
                        </div>
                        <select x-model="customDiferencial.icone" class="form-select">
                            <option value="">Escolha um ícone</option>
                            @foreach($iconNames as $icone)
                                <option value="{{ $icone }}">{{ $icone }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-field">
                    <label class="form-label">Descrição (opcional)</label>
                    <textarea 
                        x-model="customDiferencial.descricao" 
                        rows="3" 
                        class="form-textarea" 
                        placeholder="Descrição do diferencial personalizados (até 200 caracteres)"
                        maxlength="200"
                    ></textarea>
                    <div class="form-counter" x-text="(200 - (customDiferencial.descricao || '').length) + ' caracteres restantes'"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-form btn-form--outline" @click="closeModal()">Cancelar</button>
                <button type="button" class="btn-form btn-form--primary" @click="saveCustomDiferencial()">Adicionar</button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom Select */
    .custom-select-wrapper {
        position: relative;
        width: 100%;
    }
    
    .custom-select-button {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.625rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        background: white;
        font-size: 0.875rem;
        color: #1e293b;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .custom-select-button:hover {
        border-color: #cbd5e1;
    }
    
    .custom-select-button:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .selected-option-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .selected-option-display .material-symbols-outlined {
        font-size: 1.25rem;
        color: #4a5568;
    }
    
    .select-arrow {
        font-size: 1.25rem;
        color: #64748b;
        transition: transform 0.2s;
    }
    
    .select-arrow.rotated {
        transform: rotate(180deg);
    }
    
    .custom-select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.25rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 350px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .dropdown-search {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .dropdown-search .material-symbols-outlined {
        font-size: 1.25rem;
        color: #64748b;
    }
    
    .dropdown-search-input {
        flex: 1;
        border: none;
        outline: none;
        font-size: 0.875rem;
    }
    
    .dropdown-search-input::placeholder {
        color: #94a3b8;
    }
    
    .dropdown-options {
        overflow-y: auto;
        max-height: 280px;
    }
    
    .dropdown-optgroup {
        padding: 0.5rem 0;
    }
    
    .dropdown-optgroup-label {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .dropdown-option {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        transition: background 0.15s;
    }
    
    .dropdown-option:hover {
        background: #f1f5f9;
    }
    
    .dropdown-option .material-symbols-outlined {
        font-size: 1.25rem;
        color: #4a5568;
    }
    
    .check-mark {
        margin-left: auto;
        color: #3b82f6;
        font-size: 1rem;
    }
    
    .option-create {
        color: #3b82f6;
        font-weight: 500;
    }
    
    .option-create .material-symbols-outlined {
        color: #3b82f6;
    }
    
    .dropdown-empty {
        padding: 0.75rem;
        text-align: center;
        color: #94a3b8;
        font-size: 0.875rem;
    }
    
    /* Selected list */
    .selected-diferenciais-list {
        margin-top: 1rem;
    }
    
    .diferenciais-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }
    
    .diferencial-card {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        background: white;
        transition: all 0.2s;
    }
    
    .diferencial-card:hover {
        border-color: #cbd5e1;
    }
    
    .diferencial-card.selected {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    
    .diferencial-card-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
    }
    
    .diferencial-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 0.5rem;
    }
    
    .diferencial-icon .material-symbols-outlined {
        font-size: 1.5rem;
        color: #4a5568;
    }
    
    .diferencial-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.125rem;
    }
    
    .diferencial-nome {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
    }
    
    .diferencial-tipo {
        font-size: 0.75rem;
        color: #64748b;
    }
    
    .diferencial-actions {
        flex-shrink: 0;
    }
    
    .btn-remove {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        color: #94a3b8;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-remove:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    
    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1rem;
    }
    
    .modal-content {
        background: white;
        border-radius: 0.75rem;
        width: 100%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .modal-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
    }
    
    .modal-close {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border: none;
        background: transparent;
        color: #64748b;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .modal-close:hover {
        background: #f1f5f9;
        color: #1e293b;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
    }
    
    .diferencial-icone-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .diferencial-icone-preview {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 0.5rem;
        border: 1px solid #e2e8f0;
    }
    
    .diferencial-icone-preview .material-symbols-outlined {
        font-size: 1.75rem;
        color: #4a5568;
    }
    
    /* Botões */
    .btn-form {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .btn-form--outline {
        background: transparent;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    
    .btn-form--outline:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    
    .btn-form--primary {
        background: #3b82f6;
        border: 1px solid #3b82f6;
        color: white;
    }
    
    .btn-form--primary:hover {
        background: #2563eb;
        border-color: #2563eb;
    }
    
    .text-danger { color: #ef4444; }
    .form-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.25rem; }
    .form-counter { font-size: 0.75rem; color: #94a3b8; text-align: right; margin-top: 0.25rem; }
</style>
<script>
function diferenciaisSelect() {
    return {
        selectedOption: null,
        showDropdown: false,
        searchQuery: '',
        selectedDiferenciais: [],
        showCustomModal: false,
        customDiferencial: { nome: '', icone: 'help', descricao: '' },
        availableDiferenciais: @json($diferenciaisPadrao),

        get filteredPadrao() {
            if (!this.searchQuery) return this.availableDiferenciais;
            const query = this.searchQuery.toLowerCase();
            return this.availableDiferenciais.filter(d => d.nome.toLowerCase().includes(query));
        },

        init() {
            const selectedIds = @json($selectedIds);
            const customSelected = @json($customSelected);
            const self = this;
            const adicionados = new Set();

            if (selectedIds && selectedIds.length > 0) {
                selectedIds.forEach(function(id) {
                    if (adicionados.has('padrao_' + id)) return;
                    const diff = self.availableDiferenciais.find(d => d.id == id);
                    if (diff) {
                        self.selectedDiferenciais.push({
                            id: diff.id,
                            nome: diff.nome,
                            icone: diff.icone,
                            tipo: 'padrao'
                        });
                        adicionados.add('padrao_' + id);
                    }
                });
            }

            if (customSelected && customSelected.length > 0) {
                customSelected.forEach(function(item) {
                    const nome = item.titulo || item.nome || '';
                    if (!nome || adicionados.has('custom_' + nome)) return;
                    self.selectedDiferenciais.push({
                        id: null,
                        nome: nome,
                        icone: item.icone || 'help',
                        descricao: item.descricao || '',
                        tipo: 'personalizado'
                    });
                    adicionados.add('custom_' + nome);
                });
            }
        },

        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (!this.showDropdown) this.searchQuery = '';
        },

        selectOption(diff) {
            if (this.isSelected(diff.id)) {
                alert('Este diferencial já foi adicionado!');
                this.showDropdown = false;
                this.searchQuery = '';
                return;
            }
            this.selectedDiferenciais.push({
                id: diff.id, nome: diff.nome, icone: diff.icone, tipo: 'padrao'
            });
            this.showDropdown = false;
            this.searchQuery = '';
            this.selectedOption = null;
        },

        isSelected(id) {
            return this.selectedDiferenciais.some(d => d.id == id);
        },

        openCustomModal() {
            this.showDropdown = false;
            this.searchQuery = '';
            this.showCustomModal = true;
        },

        removeDiferencial(index) {
            this.selectedDiferenciais.splice(index, 1);
        },

        saveCustomDiferencial() {
            if (!this.customDiferencial.nome.trim()) {
                alert('Por favor, digite o nome do diferencial');
                return;
            }
            this.selectedDiferenciais.push({
                id: null,
                nome: this.customDiferencial.nome,
                icone: this.customDiferencial.icone || 'help',
                descricao: this.customDiferencial.descricao,
                tipo: 'personalizado'
            });
            this.customDiferencial = { nome: '', icone: 'help', descricao: '' };
            this.closeModal();
        },

        closeModal() {
            this.showCustomModal = false;
            this.customDiferencial = { nome: '', icone: 'help', descricao: '' };
        }
    }
}
</script>