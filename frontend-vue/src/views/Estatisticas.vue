<template>
  <div class="container">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="h2 mb-0">
          <i class="bi bi-bar-chart me-2"></i>
          Estatísticas e Relatórios
        </h1>
        <p class="text-muted mt-2">Análise detalhada do sistema de notas fiscais</p>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="text-center py-5">
      <div class="loading-spinner"></div>
      <p class="mt-3 text-muted">Carregando estatísticas...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="hasError" class="alert alert-danger" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      {{ error }}
      <button @click="carregarEstatisticas" class="btn btn-sm btn-outline-danger ms-3">
        <i class="bi bi-arrow-clockwise me-1"></i>
        Tentar Novamente
      </button>
    </div>

    <!-- Conteúdo -->
    <div v-else-if="estatisticas">
      <!-- Resumo Geral -->
      <div class="row mb-5">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h4 class="card-title mb-0">
                <i class="bi bi-graph-up me-2"></i>
                Resumo Geral do Sistema
              </h4>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h2 class="display-6 fw-bold text-primary">
                      {{ estatisticas.resumo_geral?.total_notas || 0 }}
                    </h2>
                    <p class="mb-0 text-muted">Total de Notas Fiscais</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h3 class="display-6 fw-bold text-success">
                      {{ estatisticas.resumo_geral?.tamanho_total?.formatado || '0 MB' }}
                    </h3>
                    <p class="mb-0 text-muted">Espaço Utilizado</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h3 class="display-6 fw-bold text-info">
                      {{ estatisticas.resumo_geral?.tamanho_total?.em_gb?.toFixed(2) || '0.00' }} GB
                    </h3>
                    <p class="mb-0 text-muted">Total em Gigabytes</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 bg-light rounded">
                    <h3 class="display-6 fw-bold text-warning">
                      {{ estatisticas.metricas_temporais?.media_diaria || 0 }}
                    </h3>
                    <p class="mb-0 text-muted">Média Diária</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Distribuições -->
      <div class="row mb-5">
        <!-- Distribuição por Tipo -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header bg-info text-white">
              <h5 class="card-title mb-0">
                <i class="bi bi-pie-chart me-2"></i>
                Distribuição por Tipo de Arquivo
              </h5>
            </div>
            <div class="card-body">
              <div v-if="estatisticas.distribuicao_por_tipo?.length" class="row">
                <div 
                  v-for="tipo in estatisticas.distribuicao_por_tipo" 
                  :key="tipo.tipo"
                  class="col-12 mb-3"
                >
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div 
                        class="rounded-circle me-3"
                        :style="{ 
                          backgroundColor: tipo.cor, 
                          width: '40px', 
                          height: '40px',
                          display: 'flex',
                          alignItems: 'center',
                          justifyContent: 'center'
                        }"
                      >
                        <span class="text-white fw-bold">{{ tipo.tipo }}</span>
                      </div>
                      <div>
                        <h6 class="mb-0">Arquivos {{ tipo.tipo }}</h6>
                        <small class="text-muted">{{ tipo.quantidade }} arquivo{{ tipo.quantidade !== 1 ? 's' : '' }}</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <h5 class="mb-0">{{ tipo.percentual }}%</h5>
                    </div>
                  </div>
                  <!-- Barra de progresso -->
                  <div class="progress mt-2" style="height: 8px;">
                    <div 
                      class="progress-bar"
                      :style="{ 
                        width: `${tipo.percentual}%`,
                        backgroundColor: tipo.cor
                      }"
                    ></div>
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

        <!-- Distribuição por Status -->
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header bg-warning text-dark">
              <h5 class="card-title mb-0">
                <i class="bi bi-clipboard-data me-2"></i>
                Distribuição por Status
              </h5>
            </div>
            <div class="card-body">
              <div v-if="estatisticas.distribuicao_por_status?.length" class="row">
                <div 
                  v-for="status in estatisticas.distribuicao_por_status" 
                  :key="status.status"
                  class="col-12 mb-3"
                >
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <span 
                        :class="`badge bg-${status.cor} me-3`"
                        style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
                      >
                        <i class="bi bi-check-circle"></i>
                      </span>
                      <div>
                        <h6 class="mb-0">{{ status.descricao }}</h6>
                        <small class="text-muted">{{ status.quantidade }} nota{{ status.quantidade !== 1 ? 's' : '' }}</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <h5 class="mb-0">{{ status.percentual }}%</h5>
                    </div>
                  </div>
                  <!-- Barra de progresso -->
                  <div class="progress mt-2" style="height: 8px;">
                    <div 
                      :class="`progress-bar bg-${status.cor}`"
                      :style="{ width: `${status.percentual}%` }"
                    ></div>
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
      </div>

      <!-- Métricas Temporais -->
      <div class="row mb-5">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-secondary text-white">
              <h5 class="card-title mb-0">
                <i class="bi bi-calendar-week me-2"></i>
                Métricas Temporais
              </h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-3 mb-3">
                  <div class="p-3 border rounded">
                    <i class="bi bi-calendar-day fs-1 text-primary mb-2"></i>
                    <h3 class="fw-bold">{{ estatisticas.metricas_temporais?.uploads_hoje || 0 }}</h3>
                    <p class="mb-0 text-muted">Uploads Hoje</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 border rounded">
                    <i class="bi bi-calendar-week fs-1 text-success mb-2"></i>
                    <h3 class="fw-bold">{{ estatisticas.metricas_temporais?.uploads_semana || 0 }}</h3>
                    <p class="mb-0 text-muted">Uploads esta Semana</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 border rounded">
                    <i class="bi bi-calendar-month fs-1 text-info mb-2"></i>
                    <h3 class="fw-bold">{{ estatisticas.metricas_temporais?.uploads_mes || 0 }}</h3>
                    <p class="mb-0 text-muted">Uploads este Mês</p>
                  </div>
                </div>
                <div class="col-md-3 mb-3">
                  <div class="p-3 border rounded">
                    <i class="bi bi-graph-up fs-1 text-warning mb-2"></i>
                    <h3 class="fw-bold">{{ estatisticas.metricas_temporais?.media_diaria || 0 }}</h3>
                    <p class="mb-0 text-muted">Média Diária</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertas do Sistema -->
      <div v-if="estatisticas.alertas?.length" class="row mb-5">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-danger text-white">
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
                <div class="me-3 mt-1">
                  <i 
                    :class="{
                      'bi bi-info-circle-fill': alerta.tipo === 'info',
                      'bi bi-exclamation-triangle-fill': alerta.tipo === 'warning',
                      'bi bi-x-circle-fill': alerta.tipo === 'danger'
                    }"
                  ></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="alert-heading mb-2">{{ alerta.titulo }}</h6>
                  <p class="mb-2">{{ alerta.mensagem }}</p>
                  <hr>
                  <p class="mb-0">
                    <strong>Ação sugerida:</strong> {{ alerta.acao_sugerida }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Metadados -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-light">
              <h6 class="card-title mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Informações do Relatório
              </h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <strong>Atualizado em:</strong><br>
                  <span class="text-muted">{{ estatisticas.meta?.atualizado_em }}</span>
                </div>
                <div class="col-md-4">
                  <strong>Período de análise:</strong><br>
                  <span class="text-muted">{{ estatisticas.meta?.periodo_analise }}</span>
                </div>
                <div class="col-md-4">
                  <strong>Fonte dos dados:</strong><br>
                  <span class="text-muted">{{ estatisticas.meta?.fonte_dados }}</span>
                </div>
              </div>
              
              <div class="mt-3 d-flex justify-content-end">
                <button 
                  @click="carregarEstatisticas" 
                  class="btn btn-outline-primary"
                  :disabled="isLoading"
                >
                  <i class="bi bi-arrow-clockwise me-2"></i>
                  Atualizar Dados
                </button>
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
const isLoading = computed(() => store.isLoading)
const hasError = computed(() => store.hasError)
const error = computed(() => store.error)

// Methods
const carregarEstatisticas = async () => {
  try {
    await store.carregarEstatisticas()
  } catch (error) {
    toast.error('Erro ao carregar estatísticas')
  }
}

// Lifecycle
onMounted(() => {
  carregarEstatisticas()
})
</script> 