<template>
  <div class="container">
    <!-- Loading -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="loading-spinner"></div>
      <p class="mt-3 text-muted">Carregando detalhes da nota fiscal...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="hasError" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
      <div class="mt-3">
        <button @click="carregarNota" class="btn btn-outline-danger me-2">
          <i class="bi bi-arrow-clockwise me-1"></i>
          Tentar Novamente
        </button>
        <RouterLink to="/notas" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-1"></i>
          Voltar à Lista
        </RouterLink>
      </div>
    </div>

    <!-- Conteúdo -->
    <div v-else-if="nota">
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h1 class="h2 mb-0">
                <i class="bi bi-receipt me-2"></i>
                Nota Fiscal #{{ nota.id }}
              </h1>
              <p class="text-muted mt-2">{{ nota.nome_empresa }} - {{ nota.cnpj }}</p>
            </div>
            <div class="d-flex gap-2">
              <button
                class="btn btn-success"
                @click="downloadNota"
                :disabled="downloading"
              >
                <div v-if="downloading" class="spinner-border spinner-border-sm me-2"></div>
                <i v-else class="bi bi-download me-2"></i>
                Download
              </button>
              <button
                class="btn btn-outline-danger"
                @click="confirmarExclusao"
              >
                <i class="bi bi-trash me-2"></i>
                Excluir
              </button>
              <RouterLink to="/notas" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Voltar
              </RouterLink>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Badge -->
      <div class="row mb-4">
        <div class="col-12">
          <span 
            :class="`badge bg-${nota.status?.cor || 'secondary'} fs-6 px-3 py-2`"
          >
            <i class="bi bi-info-circle me-2"></i>
            {{ nota.status?.descricao }}
          </span>
        </div>
      </div>

      <!-- Informações Principais -->
      <div class="row mb-4">
        <!-- Dados da Empresa -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header bg-primary text-white">
              <h5 class="card-title mb-0">
                <i class="bi bi-building me-2"></i>
                Dados da Empresa
              </h5>
            </div>
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Nome:</dt>
                <dd class="col-sm-8">{{ nota.nome_empresa }}</dd>
                
                <dt class="col-sm-4">CNPJ:</dt>
                <dd class="col-sm-8">
                  <span class="font-monospace">{{ nota.cnpj }}</span>
                </dd>
                
                <dt class="col-sm-4">CNPJ Numérico:</dt>
                <dd class="col-sm-8">
                  <span class="font-monospace small text-muted">{{ nota.cnpj_numerico }}</span>
                </dd>
              </dl>
            </div>
          </div>
        </div>

        <!-- Dados do Arquivo -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header bg-info text-white">
              <h5 class="card-title mb-0">
                <i class="bi bi-file-earmark me-2"></i>
                Dados do Arquivo
              </h5>
            </div>
            <div class="card-body">
              <dl class="row mb-0">
                <dt class="col-sm-4">Nome Original:</dt>
                <dd class="col-sm-8">
                  <div class="d-flex align-items-center">
                    <i 
                      :class="getFileIcon(nota.arquivo?.tipo)"
                      :style="{ color: getFileColor(nota.arquivo?.tipo) }"
                      class="me-2"
                    ></i>
                    {{ nota.arquivo?.nome_original }}
                  </div>
                </dd>
                
                <dt class="col-sm-4">Tipo:</dt>
                <dd class="col-sm-8">
                  <span :class="`badge bg-${getTypeBadgeColor(nota.arquivo?.tipo)}`">
                    {{ nota.arquivo?.tipo }}
                  </span>
                </dd>
                
                <dt class="col-sm-4">Tamanho:</dt>
                <dd class="col-sm-8">{{ nota.arquivo?.tamanho_formatado }}</dd>
                
                <dt class="col-sm-4">Hash:</dt>
                <dd class="col-sm-8">
                  <code class="small">{{ nota.arquivo?.hash?.substring(0, 16) }}...</code>
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <!-- Datas e Timeline -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h5 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Datas e Timeline
              </h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <h6 class="text-muted mb-2">Data de Vencimento</h6>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-event me-2 text-warning"></i>
                    <div>
                      <div class="fw-bold">{{ nota.data_vencimento?.formatada }}</div>
                      <small class="text-muted">{{ nota.data_vencimento?.original }}</small>
                    </div>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <h6 class="text-muted mb-2">Data de Upload</h6>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-upload me-2 text-primary"></i>
                    <div>
                      <div class="fw-bold">{{ nota.datas?.upload?.formatada }}</div>
                      <small class="text-muted">{{ nota.datas?.upload?.humana }}</small>
                    </div>
                  </div>
                </div>

                <div class="col-md-4 mb-3">
                  <h6 class="text-muted mb-2">Última Atualização</h6>
                  <div class="d-flex align-items-center">
                    <i class="bi bi-arrow-clockwise me-2 text-success"></i>
                    <div>
                      <div class="fw-bold">{{ nota.datas?.atualizacao?.formatada || 'N/A' }}</div>
                      <small class="text-muted">{{ nota.datas?.atualizacao?.humana || 'Nunca atualizada' }}</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Edição de Status e Observações -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="card-title mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Editar Status e Observações
              </h5>
            </div>
            <div class="card-body">
              <form @submit.prevent="salvarAlteracoes">
                <div class="row">
                  <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select 
                      v-model="form.status" 
                      class="form-select"
                      :class="{ 'is-invalid': errors.status }"
                    >
                      <option value="pendente">Pendente</option>
                      <option value="processado">Processado</option>
                      <option value="erro">Erro</option>
                    </select>
                    <div v-if="errors.status" class="invalid-feedback">
                      {{ errors.status[0] }}
                    </div>
                  </div>

                  <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Observações</label>
                    <textarea
                      v-model="form.observacoes"
                      class="form-control"
                      :class="{ 'is-invalid': errors.observacoes }"
                      rows="3"
                      placeholder="Adicione observações sobre esta nota fiscal..."
                      maxlength="1000"
                    ></textarea>
                    <div v-if="errors.observacoes" class="invalid-feedback">
                      {{ errors.observacoes[0] }}
                    </div>
                    <div class="form-text">
                      {{ form.observacoes?.length || 0 }}/1000 caracteres
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="resetarForm"
                  >
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Resetar
                  </button>
                  <button
                    type="submit"
                    class="btn btn-primary"
                    :disabled="saving || !formAlterado"
                  >
                    <div v-if="saving" class="spinner-border spinner-border-sm me-2"></div>
                    <i v-else class="bi bi-check-lg me-2"></i>
                    {{ saving ? 'Salvando...' : 'Salvar Alterações' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Observações Atuais -->
      <div v-if="nota.observacoes" class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">
                <i class="bi bi-chat-text me-2"></i>
                Observações Atuais
              </h6>
            </div>
            <div class="card-body">
              <p class="mb-0">{{ nota.observacoes }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useNotasFiscaisStore } from '@/stores/notasFiscais'
import { useToast } from 'vue-toastification'
import Swal from 'sweetalert2'

const route = useRoute()
const router = useRouter()
const store = useNotasFiscaisStore()
const toast = useToast()

// Props do componente
const props = defineProps({
  id: {
    type: [String, Number],
    required: true
  }
})

// Estado reativo
const downloading = ref(false)
const saving = ref(false)
const errors = ref({})

const form = ref({
  status: '',
  observacoes: ''
})

// Computed properties
const nota = computed(() => store.nota)
const isLoading = computed(() => store.isLoading)
const hasError = computed(() => store.hasError)
const error = computed(() => store.error)

const formAlterado = computed(() => {
  if (!nota.value) return false
  return form.value.status !== nota.value.status?.codigo ||
         form.value.observacoes !== (nota.value.observacoes || '')
})

// Methods
const inicializarForm = () => {
  if (nota.value) {
    form.value.status = nota.value.status?.codigo || 'pendente'
    form.value.observacoes = nota.value.observacoes || ''
  }
}

const resetarForm = () => {
  inicializarForm()
  errors.value = {}
}

const carregarNota = async () => {
  try {
    await store.buscarNotaPorId(props.id)
    inicializarForm()
  } catch (error) {
    console.error('Erro ao carregar nota:', error)
  }
}

const downloadNota = async () => {
  try {
    downloading.value = true
    await store.downloadNota(nota.value.id, nota.value.arquivo?.nome_original)
    toast.success('Download iniciado!')
  } catch (error) {
    toast.error('Erro ao fazer download da nota fiscal')
  } finally {
    downloading.value = false
  }
}

const salvarAlteracoes = async () => {
  try {
    saving.value = true
    errors.value = {}

    await store.atualizarNota(nota.value.id, form.value)
    toast.success('Nota fiscal atualizada com sucesso!')
    
  } catch (error) {
    if (error.validationErrors) {
      errors.value = error.validationErrors
      toast.error('Corrija os erros no formulário')
    } else {
      toast.error('Erro ao salvar alterações')
    }
  } finally {
    saving.value = false
  }
}

const confirmarExclusao = async () => {
  const result = await Swal.fire({
    title: 'Confirmar Exclusão',
    html: `
      Tem certeza que deseja excluir esta nota fiscal?<br>
      <strong>ID:</strong> ${nota.value.id}<br>
      <strong>Empresa:</strong> ${nota.value.nome_empresa}<br>
      <strong>CNPJ:</strong> ${nota.value.cnpj}
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
      await store.removerNota(nota.value.id)
      toast.success('Nota fiscal excluída com sucesso!')
      router.push('/notas')
    } catch (error) {
      toast.error('Erro ao excluir nota fiscal')
    }
  }
}

const getFileIcon = (tipo) => {
  return tipo === 'PDF' ? 'bi bi-file-pdf' : 'bi bi-file-code'
}

const getFileColor = (tipo) => {
  return tipo === 'PDF' ? '#dc3545' : '#28a745'
}

const getTypeBadgeColor = (tipo) => {
  return tipo === 'PDF' ? 'danger' : 'success'
}

// Lifecycle
onMounted(() => {
  carregarNota()
})

// Watchers
watch(() => nota.value, () => {
  if (nota.value) {
    inicializarForm()
  }
}, { deep: true })
</script> 