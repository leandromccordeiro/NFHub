<template>
  <div class="container">
    <!-- Header da página -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h1 class="h2 mb-0">
            <i class="bi bi-speedometer2 me-2"></i>
            Dashboard
          </h1>
          <RouterLink to="/upload" class="btn btn-primary">
            <i class="bi bi-cloud-upload me-2"></i>
            Nova Nota Fiscal
          </RouterLink>
        </div>
        <p class="text-muted mt-2">Visão geral do sistema de notas fiscais</p>
      </div>
    </div>

    <!-- Loading Spinner -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="loading-spinner"></div>
      <p class="mt-3 text-muted">Carregando estatísticas...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="hasError" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
      <button @click="carregarDados" class="btn btn-sm btn-outline-danger ms-3">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Tentar Novamente
      </button>
    </div>

    <!-- Conteúdo Principal -->
    <div v-else>
      <!-- Cards de Estatísticas -->
      <div class="row mb-4">
        <div class="col-md-3 mb-3">
          <div class="card bg-primary text-white card-hover">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title mb-0">Total de Notas</h6>
                  <h2 class="fw-bold">{{ estatisticas?.resumo_geral?.total_notas || 0 }}</h2>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-receipt-cutoff fs-1 opacity-75"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="card bg-success text-white card-hover">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title mb-0">Tamanho Total</h6>
                  <h4 class="fw-bold">{{ estatisticas?.resumo_geral?.tamanho_total?.formatado || '0 MB' }}</h4>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-hdd fs-1 opacity-75"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="card bg-info text-white card-hover">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title mb-0">Uploads Hoje</h6>
                  <h2 class="fw-bold">{{ estatisticas?.metricas_temporais?.uploads_hoje || 0 }}</h2>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-calendar-day fs-1 opacity-75"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-3 mb-3">
          <div class="card bg-warning text-dark card-hover">
            <div class="card-body">
              <div class="d-flex justify-content-between">
                <div>
                  <h6 class="card-title mb-0">Média Diária</h6>
                  <h2 class="fw-bold">{{ estatisticas?.metricas_temporais?.media_diaria || 0 }}</h2>
                </div>
                <div class="align-self-center">
                  <i class="bi bi-graph-up fs-1 opacity-75"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Distribuições e Ações Rápidas -->
      <div class="row">
        <!-- Distribuição por Tipo -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100 card-hover">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">
                <i class="bi bi-pie-chart me-2"></i>
                Distribuição por Tipo
              </h5>
            </div>
            <div class="card-body">
              <div v-if="estatisticas?.distribuicao_por_tipo?.length" class="row">
                <div 
                  v-for="tipo in estatisticas.distribuicao_por_tipo" 
                  :key="tipo.tipo"
                  class="col-6 mb-3"
                >
                  <div class="text-center">
                    <div 
                      class="rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center"
                      :style="{ backgroundColor: tipo.cor, width: '60px', height: '60px' }"
                    >
                      <span class="text-white fw-bold">{{ tipo.tipo }}</span>
                    </div>
                    <h4 class="mb-0">{{ tipo.quantidade }}</h4>
                    <small class="text-muted">{{ tipo.percentual }}%</small>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">Nenhum dado disponível</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100 card-hover">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">
                <i class="bi bi-lightning me-2"></i>
                Ações Rápidas
              </h5>
            </div>
            <div class="card-body">
              <div class="d-grid gap-3">
                <RouterLink to="/upload" class="btn btn-primary btn-lg shadow-sm">
                  <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-cloud-upload fs-4 me-3"></i>
                    <div class="text-start">
                      <div class="fw-bold">Fazer Upload</div>
                      <small class="opacity-75">Adicionar nova nota fiscal</small>
                    </div>
                  </div>
                </RouterLink>
                
                <RouterLink to="/notas" class="btn btn-outline-secondary btn-lg shadow-sm">
                  <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-list-ul fs-4 me-3"></i>
                    <div class="text-start">
                      <div class="fw-bold">Ver Todas as Notas</div>
                      <small class="opacity-75">Gerenciar todas as notas</small>
                    </div>
                  </div>
                </RouterLink>
                
                <RouterLink to="/estatisticas" class="btn btn-outline-info btn-lg shadow-sm">
                  <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-bar-chart fs-4 me-3"></i>
                    <div class="text-start">
                      <div class="fw-bold">Relatórios</div>
                      <small class="opacity-75">Análises detalhadas</small>
                    </div>
                  </div>
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertas do Sistema -->
      <div v-if="estatisticas?.alertas?.length" class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h5 class="card-title mb-0">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Alertas do Sistema
              </h5>
            </div>
            <div class="card-body">
              <div 
                v-for="(alerta, index) in estatisticas.alertas" 
                :key="index"
                :class="`alert alert-${alerta.tipo} d-flex align-items-start`"
                role="alert"
              >
                <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                <div class="flex-grow-1">
                  <h6 class="alert-heading mb-1">{{ alerta.titulo }}</h6>
                  <p class="mb-1">{{ alerta.mensagem }}</p>
                  <small><strong>Ação sugerida:</strong> {{ alerta.acao_sugerida }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas Notas Fiscais -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card card-hover">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">
                <i class="bi bi-clock-history me-2"></i>
                Últimas Notas Fiscais
              </h5>
              <RouterLink to="/notas" class="btn btn-sm btn-outline-primary">
                Ver Todas
              </RouterLink>
            </div>
            <div class="card-body">
              <div v-if="hasNotas" class="table-container">
                <div class="table-responsive">
                  <table class="table table-hover align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Empresa</th>
                        <th>CNPJ</th>
                        <th>Data Upload</th>
                        <th>Status</th>
                        <th class="text-center" style="min-width: 200px;">Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="nota in notas.slice(0, 5)" :key="nota.id">
                        <td>
                          <div class="fw-medium">{{ nota.nome_empresa }}</div>
                          <small class="text-muted">{{ nota.arquivo?.nome_original }}</small>
                        </td>
                        <td>
                          <span class="font-monospace">{{ nota.cnpj }}</span>
                        </td>
                        <td>
                          <div>{{ nota.datas?.upload?.formatada }}</div>
                          <small class="text-muted">{{ nota.datas?.upload?.humana }}</small>
                        </td>
                        <td>
                          <span 
                            :class="`badge bg-${nota.status?.cor || 'secondary'} fs-6 px-3 py-2`"
                          >
                            {{ nota.status?.descricao }}
                          </span>
                        </td>
                        <td>
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
                              @click="downloadNota(nota)"
                              class="btn btn-outline-success btn-sm"
                              :title="`Download ${nota.arquivo?.nome_original}`"
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
                                <li><hr class="dropdown-divider"></li>
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
                              </ul>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div v-else class="text-center py-5">
                <div class="mb-4">
                  <i class="bi bi-inbox display-1 text-muted"></i>
                </div>
                <h5 class="text-muted mb-3">Nenhuma nota fiscal encontrada</h5>
                <p class="text-muted mb-4">Comece adicionando sua primeira nota fiscal ao sistema</p>
                <RouterLink to="/upload" class="btn btn-primary btn-lg">
                  <i class="bi bi-plus-circle me-2"></i>
                  Adicionar Primeira Nota
                </RouterLink>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed } from 'vue'
import { useNotasFiscaisStore } from '@/stores/notasFiscais'
import { useToast } from 'vue-toastification'

const store = useNotasFiscaisStore()
const toast = useToast()

// Computed properties
const estatisticas = computed(() => store.estatisticas)
const notas = computed(() => store.notas)
const isLoading = computed(() => store.isLoading)
const hasError = computed(() => store.hasError)
const error = computed(() => store.error)
const hasNotas = computed(() => store.hasNotas)

// Methods
const carregarDados = async () => {
  try {
    await Promise.all([
      store.carregarEstatisticas(),
      store.listarNotas({ per_page: 5 })
    ])
  } catch (error) {
    toast.error('Erro ao carregar dados do dashboard')
  }
}

// Ações das notas fiscais
const downloadNota = async (nota) => {
  try {
    await store.downloadNota(nota.id, nota.arquivo?.nome_original)
    toast.success(`Download de ${nota.arquivo?.nome_original} iniciado`)
  } catch (error) {
    toast.error('Erro ao fazer download da nota fiscal')
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
  carregarDados()
})
</script>

<style scoped>
.card-hover:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease-in-out;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

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
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.dropdown-menu {
  border: 1px solid #dee2e6;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item:hover {
  background-color: #f8f9fa;
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
</style> 