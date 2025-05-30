import axios from 'axios'

// Configuração base do Axios
const apiClient = axios.create({
  baseURL: 'http://localhost:8002/api',
  timeout: 30000, // 30 segundos para uploads de arquivos
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  }
})

// Interceptor para requisições
apiClient.interceptors.request.use(
  (config) => {
    // Log da requisição (apenas em desenvolvimento)
    if (import.meta.env.DEV) {
      console.log(`🚀 ${config.method?.toUpperCase()} ${config.url}`, config.data || config.params)
    }
    
    return config
  },
  (error) => {
    console.error('❌ Erro na configuração da requisição:', error)
    return Promise.reject(error)
  }
)

// Interceptor para respostas
apiClient.interceptors.response.use(
  (response) => {
    // Log da resposta (apenas em desenvolvimento)
    if (import.meta.env.DEV) {
      console.log(`✅ ${response.config.method?.toUpperCase()} ${response.config.url}`, response.data)
    }
    
    return response
  },
  (error) => {
    // Log do erro
    if (import.meta.env.DEV) {
      console.error(`❌ ${error.config?.method?.toUpperCase()} ${error.config?.url}`, error.response?.data || error.message)
    }

    // Tratamento de erros específicos
    if (error.response) {
      // Erro com resposta do servidor
      const { status, data } = error.response
      
      switch (status) {
        case 422:
          // Erros de validação
          error.validationErrors = data.errors || {}
          break
        case 404:
          // Recurso não encontrado
          error.message = data.message || 'Recurso não encontrado'
          break
        case 500:
          // Erro interno do servidor
          error.message = 'Erro interno do servidor. Tente novamente mais tarde.'
          break
        default:
          error.message = data.message || `Erro ${status}: ${error.message}`
      }
    } else if (error.request) {
      // Erro de rede/conexão
      error.message = 'Erro de conexão. Verifique sua internet e tente novamente.'
    } else {
      // Outros erros
      error.message = error.message || 'Erro inesperado. Tente novamente.'
    }

    return Promise.reject(error)
  }
)

// Funções utilitárias para tipos específicos de requisição
export const apiUtils = {
  // Upload de arquivos com progress
  uploadFile: (url, formData, onProgress) => {
    return apiClient.post(url, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        if (onProgress && progressEvent.total) {
          const progress = Math.round((progressEvent.loaded * 100) / progressEvent.total)
          onProgress(progress)
        }
      }
    })
  },

  // Download de arquivos
  downloadFile: (url) => {
    return apiClient.get(url, {
      responseType: 'blob'
    })
  },

  // Formatação de dados para envio
  formatFormData: (data) => {
    const formData = new FormData()
    
    Object.keys(data).forEach(key => {
      const value = data[key]
      if (value !== null && value !== undefined) {
        if (value instanceof File) {
          formData.append(key, value)
        } else {
          formData.append(key, String(value))
        }
      }
    })
    
    return formData
  }
}

export default apiClient 