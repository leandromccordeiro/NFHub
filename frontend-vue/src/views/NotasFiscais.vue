<template>
  <div class="container">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 mb-0">
              <i class="bi bi-list-ul me-2"></i>
              Notas Fiscais
            </h1>
            <p class="text-muted mt-2">
              Gerencie todas as notas fiscais do sistema
            </p>
          </div>
          <RouterLink to="/upload" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Nova Nota
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
      <div class="card-header bg-light">
        <h6 class="card-title mb-0">
          <i class="bi bi-funnel me-2"></i>
          Filtros de Busca
        </h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="aplicarFiltros">
          <div class="row">
            <div class="col-md-3 mb-3">
              <label class="form-label">CNPJ</label>
              <input
                v-model="filtros.cnpj"
                type="text"
                class="form-control"
                placeholder="00.000.000/0001-00"
                @input="formatarCNPJFiltro"
              />
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Nome da Empresa</label>
              <input
                v-model="filtros.nome_empresa"
                type="text"
                class="form-control"
                placeholder="Digite o nome..."
              />
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Status</label>
              <select v-model="filtros.status" class="form-select">
                <option value="">Todos</option>
                <option value="pendente">Pendente</option>
                <option value="processado">Processado</option>
                <option value="erro">Erro</option>
              </select>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Data Vencimento</label>
              <input
                v-model="filtros.data_vencimento"
                type="date"
                class="form-control"
              />
            </div>
            <div class="col-md-2 mb-3 d-flex align-items-end">
              <div class="btn-group w-100">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-search"></i>
                  Buscar
                </button>
                <button 
                  type="button" 
                  class="btn btn-outline-secondary"
                  @click="limparFiltros"
                >
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white">
          <div class="card-body text-center">
            <h3 class="fw-bold">{{ notasTotal }}</h3>
            <small>Total de Notas</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body text-center">
            <h3 class="fw-bold">{{ contarPorStatus('processado') }}</h3>
            <small>Processadas</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-dark">
          <div class="card-body text-center">
            <h3 class="fw-bold">{{ contarPorStatus('pendente') }}</h3>
            <small>Pendentes</small>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-danger text-white">
          <div class="card-body text-center">
            <h3 class="fw-bold">{{ contarPorStatus('erro') }}</h3>
            <small>Com Erro</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="loading-spinner"></div>
      <p class="mt-3 text-muted">Carregando notas fiscais...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="hasError" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
      <button @click="carregarNotas" class="btn btn-sm btn-outline-danger ms-3">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Tentar Novamente
      </button>
    </div>

    <!-- Lista de Notas -->
    <div v-else class="card">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
          Resultados ({{ notasTotal }} nota{{ notasTotal !== 1 ? 's' : '' }})
        </h6>
        
        <!-- Ordenação -->
        <div class="d-flex align-items-center gap-2">
          <small class="text-muted">Ordenar por:</small>
          <select 
            v-model="filtros.sort_by" 
            class="form-select form-select-sm" 
            style="width: auto;"
            @change="aplicarFiltros"
          >
            <option value="data_upload">Data Upload</option>
            <option value="data_vencimento">Data Vencimento</option>
            <option value="nome_empresa">Nome Empresa</option>
            <option value="cnpj">CNPJ</option>
          </select>
          <button 
            class="btn btn-sm btn-outline-secondary"
            @click="toggleOrdem"
          >
            <i :class="filtros.sort_order === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'"></i>
          </button>
        </div>
      </div>

      <!-- Tabela -->
      <div v-if="hasNotas" class="table-container">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Empresa</th>
              <th>CNPJ</th>
              <th>Arquivo</th>
              <th>Data Upload</th>
              <th>Data Vencimento</th>
              <th>Status</th>
              <th class="text-center" style="min-width: 220px;">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="nota in notas" :key="nota.id">
              <td>
                <div>
                  <strong>{{ nota.nome_empresa }}</strong>
                  <div v-if="nota.observacoes" class="small text-muted">
                    {{ nota.observacoes.substring(0, 50) }}{{ nota.observacoes.length > 50 ? '...' : '' }}
                  </div>
                </div>
              </td>
              <td>
                <span class="font-monospace">{{ nota.cnpj }}</span>
              </td>
              <td>
                <div class="d-flex align-items-center">
                  <i 
                    :class="getFileIcon(nota.arquivo?.tipo)"
                    :style="{ color: getFileColor(nota.arquivo?.tipo) }"
                    class="me-2"
                  ></i>
                  <div>
                    <div class="small fw-bold">{{ nota.arquivo?.nome_original }}</div>
                    <div class="small text-muted">{{ nota.arquivo?.tamanho_formatado }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div>{{ nota.datas?.upload?.formatada }}</div>
                <small class="text-muted">{{ nota.datas?.upload?.humana }}</small>
              </td>
              <td>{{ nota.data_vencimento?.formatada }}</td>
              <td>
                <span 
                  :class="`badge bg-${nota.status?.cor || 'secondary'}`"
                >
                  {{ nota.status?.descricao }}
                </span>
              </td>
              <td class="text-center">
                <div class="btn-group" role="group">
                  <RouterLink 
                    :to="`/notas/${nota.id}`"
                    class="btn btn-outline-primary btn-sm"
                    :title="`Ver detalhes de ${nota.nome_empresa}`"
                  >
                    <i class="bi bi-eye me-1"></i>
                    Ver
                  </RouterLink>
                  
                  <button
                    class="btn btn-outline-success btn-sm"
                    :title="`Download ${nota.arquivo?.nome_original}`"
                    @click="downloadNota(nota)"
                  >
                    <i class="bi bi-download me-1"></i>
                    Download
                  </button>
                  
                  <div class="btn-group" role="group">
                    <button 
                      type="button" 
                      class="btn btn-outline-secondary btn-sm dropdown-toggle dropdown-toggle-split" 
                      data-bs-toggle="dropdown" 
                      aria-expanded="false"
                      :title="'Mais ações para ' + nota.nome_empresa"
                    >
                      <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li>
                        <RouterLink 
                          :to="`/notas/${nota.id}/edit`" 
                          class="dropdown-item"
                        >
                          <i class="bi bi-pencil me-2"></i>
                          Editar
                        </RouterLink>
                      </li>
                      <li>
                        <button 
                          @click="copiarLink(nota)"
                          class="dropdown-item"
                        >
                          <i class="bi bi-link me-2"></i>
                          Copiar Link
                        </button>
                      </li>
                      <li>
                        <button 
                          @click="compartilhar(nota)"
                          class="dropdown-item"
                        >
                          <i class="bi bi-share me-2"></i>
                          Compartilhar
                        </button>
                      </li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <button
                          class="dropdown-item text-danger"
                          @click="confirmarExclusao(nota)"
                        >
                          <i class="bi bi-trash me-2"></i>
                          Excluir
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Estado Vazio -->
      <div v-else class="text-center py-5">
        <div class="mb-4">
          <i class="bi bi-inbox display-1 text-muted"></i>
        </div>
        <h5 class="text-muted mb-3">Nenhuma nota fiscal encontrada</h5>
        <p class="text-muted mb-4">
          Tente ajustar os filtros ou adicione uma nova nota fiscal ao sistema
        </p>
        <div class="d-flex justify-content-center gap-2">
          <button @click="limparFiltros" class="btn btn-outline-secondary">
            <i class="bi bi-funnel me-2"></i>
            Limpar Filtros
          </button>
          <RouterLink to="/upload" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            Adicionar Nova Nota
          </RouterLink>
        </div>
      </div>

      <!-- Paginação -->
      <div v-if="hasNotas && meta.last_page > 1" class="card-footer">
        <nav aria-label="Navegação das notas fiscais">
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item" :class="{ disabled: meta.current_page === 1 }">
              <button 
                class="page-link"
                @click="irParaPagina(meta.current_page - 1)"
                :disabled="meta.current_page === 1"
              >
                <i class="bi bi-chevron-left"></i>
              </button>
            </li>

            <li 
              v-for="page in paginasVisiveis"
              :key="page"
              class="page-item"
              :class="{ active: page === meta.current_page }"
            >
              <button 
                class="page-link"
                @click="irParaPagina(page)"
              >
                {{ page }}
              </button>
            </li>

            <li class="page-item" :class="{ disabled: meta.current_page === meta.last_page }">
              <button 
                class="page-link"
                @click="irParaPagina(meta.current_page + 1)"
                :disabled="meta.current_page === meta.last_page"
              >
                <i class="bi bi-chevron-right"></i>
              </button>
            </li>
          </ul>
        </nav>

        <div class="text-center mt-2">
          <small class="text-muted">
            Exibindo {{ meta.from }} a {{ meta.to }} de {{ meta.total }} registro{{ meta.total !== 1 ? 's' : '' }}
          </small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useNotasFiscaisStore } from '@/stores/notasFiscais'
import { useToast } from 'vue-toastification'
import Swal from 'sweetalert2'

const store = useNotasFiscaisStore()
const toast = useToast()

// Estado reativo
const filtros = ref({
  cnpj: '',
  nome_empresa: '',
  status: '',
  data_vencimento: '',
  sort_by: 'data_upload',
  sort_order: 'desc',
  per_page: 15,
  page: 1
})

// Computed properties
const notas = computed(() => store.notas)
const meta = computed(() => store.meta)
const isLoading = computed(() => store.isLoading)
const hasError = computed(() => store.hasError)
const error = computed(() => store.error)
const hasNotas = computed(() => store.hasNotas)
const notasTotal = computed(() => store.notasTotal)

const paginasVisiveis = computed(() => {
  const current = meta.value.current_page || 1
  const last = meta.value.last_page || 1
  const pages = []
  
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const formatarCNPJFiltro = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  value = value.replace(/^(\d{2})(\d)/, '$1.$2')
  value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
  value = value.replace(/\.(\d{3})(\d)/, '.$1/$2')
  value = value.replace(/(\d{4})(\d)/, '$1-$2')
  filtros.value.cnpj = value
}

const getFileIcon = (tipo) => {
  return tipo === 'PDF' ? 'bi bi-file-pdf' : 'bi bi-file-code'
}

const getFileColor = (tipo) => {
  return tipo === 'PDF' ? '#dc3545' : '#28a745'
}

const contarPorStatus = (status) => {
  return notas.value.filter(nota => nota.status?.codigo === status).length
}

const aplicarFiltros = async () => {
  filtros.value.page = 1
  await carregarNotas()
}

const limparFiltros = async () => {
  filtros.value = {
    cnpj: '',
    nome_empresa: '',
    status: '',
    data_vencimento: '',
    sort_by: 'data_upload',
    sort_order: 'desc',
    per_page: 15,
    page: 1
  }
  await carregarNotas()
}

const toggleOrdem = async () => {
  filtros.value.sort_order = filtros.value.sort_order === 'asc' ? 'desc' : 'asc'
  await carregarNotas()
}

const irParaPagina = async (page) => {
  if (page >= 1 && page <= meta.value.last_page) {
    filtros.value.page = page
    await carregarNotas()
  }
}

const carregarNotas = async () => {
  try {
    await store.listarNotas(filtros.value)
  } catch (error) {
    toast.error('Erro ao carregar notas fiscais')
  }
}

const downloadNota = async (nota) => {
  try {
    await store.downloadNota(nota.id, nota.arquivo?.nome_original)
    toast.success('Download iniciado!')
  } catch (error) {
    toast.error('Erro ao fazer download da nota fiscal')
  }
}

const confirmarExclusao = async (nota) => {
  const result = await Swal.fire({
    title: 'Confirmar Exclusão',
    html: `
      Tem certeza que deseja excluir esta nota fiscal?<br>
      <strong>Empresa:</strong> ${nota.nome_empresa}<br>
      <strong>CNPJ:</strong> ${nota.cnpj}
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sim, excluir',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d'
  })

  if (result.isConfirmed) {
    try {
      await store.removerNota(nota.id)
      toast.success('Nota fiscal excluída com sucesso!')
      
      // Recarregar se a página atual ficou vazia
      if (notas.value.length === 0 && filtros.value.page > 1) {
        filtros.value.page = filtros.value.page - 1
        await carregarNotas()
      }
    } catch (error) {
      toast.error('Erro ao excluir nota fiscal')
    }
  }
}

const copiarLink = async (nota) => {
  try {
    const url = `${window.location.origin}/notas/${nota.id}`
    await navigator.clipboard.writeText(url)
    toast.success('Link copiado para a área de transferência')
  } catch (error) {
    toast.error('Erro ao copiar link')
  }
}

const compartilhar = async (nota) => {
  try {
    if (navigator.share) {
      await navigator.share({
        title: `Nota Fiscal - ${nota.nome_empresa}`,
        text: `Nota fiscal de ${nota.nome_empresa} (${nota.cnpj})`,
        url: `${window.location.origin}/notas/${nota.id}`
      })
    } else {
      // Fallback para navegadores que não suportam Web Share API
      await copiarLink(nota)
      toast.info('Link copiado (navegador não suporta compartilhamento nativo)')
    }
  } catch (error) {
    if (error.name !== 'AbortError') {
      toast.error('Erro ao compartilhar')
    }
  }
}

// Lifecycle
onMounted(() => {
  carregarNotas()
})

// Watchers
watch(() => filtros.value.per_page, () => {
  filtros.value.page = 1
  carregarNotas()
})
</script>

<style scoped>
.btn-group .btn {
  border-radius: 0.375rem;
  margin-right: 2px;
}

.btn-group .btn:last-child {
  margin-right: 0;
}

.table-responsive {
  border-radius: 0.5rem;
  overflow: hidden;
}

.badge {
  border-radius: 0.5rem;
}

.loading-spinner {
  width: 2rem;
  height: 2rem;
  border: 3px solid #dee2e6;
  border-top: 3px solid #0d6efd;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.dropdown-menu {
  border: 1px solid #dee2e6;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
  border-radius: 0.5rem;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.dropdown-item.text-danger:hover {
  background-color: #f8d7da;
}

/* Melhorar aparência dos ícones */
.bi {
  vertical-align: baseline;
}

/* Espaçamento consistente */
.btn i {
  margin-right: 0.25rem;
}

/* Hover effects para botões */
.btn:hover {
  transform: translateY(-1px);
  transition: all 0.2s ease-in-out;
}

/* Estado empty melhorado */
.text-center .display-1 {
  opacity: 0.3;
}

/* Cards de estatísticas */
.card {
  border-radius: 0.5rem;
  border: 1px solid #dee2e6;
}

.card:hover {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s ease-in-out;
}

/* Melhorar tabela */
.table th {
  border-top: none;
  font-weight: 600;
  background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}

/* Paginação */
.page-link {
  border-radius: 0.375rem !important;
  margin: 0 2px;
  border: 1px solid #dee2e6;
}

.page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Filtros */
.form-control:focus,
.form-select:focus {
  border-color: #86b7fe;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style> 