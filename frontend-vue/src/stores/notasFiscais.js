import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import apiClient from '@/services/api'

export const useNotasFiscaisStore = defineStore('notasFiscais', () => {
  // Estado
  const notas = ref([])
  const nota = ref(null)
  const estatisticas = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const meta = ref({})
  const filtros = ref({
    cnpj: '',
    data_vencimento: '',
    status: '',
    nome_empresa: '',
    sort_by: 'created_at',
    sort_order: 'desc',
    per_page: 15,
    page: 1
  })

  // Debug inicial
  console.log('🏪 Store inicializado - Estado inicial:', {
    notas: notas.value,
    meta: meta.value,
    filtros: filtros.value
  })

  // Getters computados
  const notasTotal = computed(() => meta.value.total || 0)
  const hasNotas = computed(() => {
    const hasNotasValue = notas.value.length > 0
    console.log('🔍 hasNotas computed:', hasNotasValue, 'notas.length:', notas.value.length)
    return hasNotasValue
  })
  const isLoading = computed(() => loading.value)
  const hasError = computed(() => error.value !== null)

  // Actions
  const resetError = () => {
    error.value = null
  }

  const setLoading = (status) => {
    loading.value = status
  }

  const listarNotas = async (params = {}) => {
    try {
      setLoading(true)
      resetError()
      
      console.log('🚀 listarNotas iniciado - params recebidos:', params)

      // Teste direto com parâmetros simples
      const simpleParams = {
        per_page: params.per_page || 5,
        sort_by: 'created_at',
        sort_order: 'desc',
        page: 1
      }
      
      console.log('🚀 listarNotas - Parâmetros simplificados:', simpleParams)
      
      const response = await apiClient.get('/notas-fiscais', { params: simpleParams })
      
      console.log('✅ listarNotas - Resposta completa:', response)
      console.log('✅ listarNotas - response.data:', response.data)
      console.log('✅ listarNotas - response.data.data:', response.data.data)
      
      if (response.data && response.data.data && Array.isArray(response.data.data)) {
        notas.value = response.data.data
        meta.value = response.data.meta || {}
        
        console.log('📝 listarNotas - Notas atribuídas:', notas.value)
        console.log('📝 listarNotas - Meta atribuída:', meta.value)
        console.log('📝 listarNotas - Quantidade de notas:', notas.value.length)
      } else {
        console.warn('⚠️ listarNotas - Estrutura de resposta inesperada ou dados não são array')
        console.warn('⚠️ Resposta:', response.data)
        notas.value = []
        meta.value = {}
      }
      
      // Atualizar filtros aplicados apenas com os novos parâmetros
      Object.assign(filtros.value, params)
      
      console.log('🎯 listarNotas - Estado final do store:', {
        'notas.value': notas.value,
        'notas.length': notas.value.length,
        'meta.value': meta.value
      })
      
      return response.data
    } catch (err) {
      error.value = 'Erro ao carregar as notas fiscais'
      console.error('❌ Erro ao listar notas:', err)
      console.error('❌ Detalhes do erro:', err.response?.data || err.message)
      throw err
    } finally {
      setLoading(false)
      console.log('🏁 listarNotas finalizado - loading:', loading.value)
    }
  }

  const buscarNotaPorId = async (id) => {
    try {
      setLoading(true)
      resetError()
      
      const response = await apiClient.get(`/notas-fiscais/${id}`)
      nota.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = 'Erro ao carregar os detalhes da nota fiscal'
      console.error('Erro ao buscar nota:', err)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const uploadNota = async (formData) => {
    try {
      setLoading(true)
      resetError()
      
      const response = await apiClient.post('/notas-fiscais', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      
      // Adicionar a nova nota ao início da lista
      if (response.data.data) {
        notas.value.unshift(response.data.data)
      }
      
      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao fazer upload da nota fiscal'
      console.error('Erro no upload:', err)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const atualizarNota = async (id, dados) => {
    try {
      setLoading(true)
      resetError()
      
      const response = await apiClient.put(`/notas-fiscais/${id}`, dados)
      
      // Atualizar a nota na lista
      const index = notas.value.findIndex(n => n.id === id)
      if (index !== -1) {
        notas.value[index] = { ...notas.value[index], ...response.data.data }
      }
      
      // Atualizar nota atual se é a mesma
      if (nota.value && nota.value.id === id) {
        nota.value = { ...nota.value, ...response.data.data }
      }
      
      return response.data
    } catch (err) {
      error.value = 'Erro ao atualizar a nota fiscal'
      console.error('Erro ao atualizar nota:', err)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const removerNota = async (id) => {
    try {
      setLoading(true)
      resetError()
      
      const response = await apiClient.delete(`/notas-fiscais/${id}`)
      
      // Remover da lista
      notas.value = notas.value.filter(n => n.id !== id)
      
      // Limpar nota atual se é a mesma
      if (nota.value && nota.value.id === id) {
        nota.value = null
      }
      
      return response.data
    } catch (err) {
      error.value = 'Erro ao remover a nota fiscal'
      console.error('Erro ao remover nota:', err)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const downloadNota = async (id, nomeOriginal) => {
    try {
      const response = await apiClient.get(`/notas-fiscais/${id}/download`, {
        responseType: 'blob'
      })
      
      // Criar link temporário para download
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      link.download = nomeOriginal || `nota_fiscal_${id}`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      window.URL.revokeObjectURL(url)
      
      return true
    } catch (err) {
      error.value = 'Erro ao fazer download da nota fiscal'
      console.error('Erro no download:', err)
      throw err
    }
  }

  const carregarEstatisticas = async () => {
    try {
      setLoading(true)
      resetError()
      
      const response = await apiClient.get('/notas-fiscais/estatisticas')
      estatisticas.value = response.data.data
      
      return response.data
    } catch (err) {
      error.value = 'Erro ao carregar as estatísticas'
      console.error('Erro ao carregar estatísticas:', err)
      throw err
    } finally {
      setLoading(false)
    }
  }

  const limparFiltros = () => {
    filtros.value = {
      cnpj: '',
      data_vencimento: '',
      status: '',
      nome_empresa: '',
      sort_by: 'created_at',
      sort_order: 'desc',
      per_page: 15,
      page: 1
    }
  }

  const setPagina = (page) => {
    filtros.value.page = page
    return listarNotas()
  }

  return {
    // Estado
    notas,
    nota,
    estatisticas,
    loading,
    error,
    meta,
    filtros,
    
    // Getters
    notasTotal,
    hasNotas,
    isLoading,
    hasError,
    
    // Actions
    resetError,
    setLoading,
    listarNotas,
    buscarNotaPorId,
    uploadNota,
    atualizarNota,
    removerNota,
    downloadNota,
    carregarEstatisticas,
    limparFiltros,
    setPagina
  }
}) 