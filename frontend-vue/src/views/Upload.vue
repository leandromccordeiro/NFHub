<template>
  <div class="container">
    <!-- Header da página -->
    <div class="row mb-4">
      <div class="col-12">
        <h1 class="h2 mb-0">
          <i class="bi bi-cloud-upload me-2"></i>
          Upload de Nota Fiscal
        </h1>
        <p class="text-muted mt-2">Envie sua nota fiscal (XML ou PDF) para o sistema</p>
      </div>
    </div>

    <!-- Formulário de Upload -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">
              <i class="bi bi-upload me-2"></i>
              Dados da Nota Fiscal
            </h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="enviarNota">
              <!-- CNPJ -->
              <div class="mb-3">
                <label for="cnpj" class="form-label fw-bold">
                  CNPJ <span class="text-danger">*</span>
                </label>
                <input
                  id="cnpj"
                  v-model="form.cnpj"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.cnpj }"
                  placeholder="00.000.000/0001-00"
                  maxlength="18"
                  @input="formatarCNPJ"
                />
                <div v-if="errors.cnpj" class="invalid-feedback">
                  {{ errors.cnpj[0] }}
                </div>
                <div class="form-text">
                  Digite o CNPJ da empresa emissora da nota fiscal
                </div>
              </div>

              <!-- Nome da Empresa -->
              <div class="mb-3">
                <label for="nome_empresa" class="form-label fw-bold">
                  Nome da Empresa <span class="text-danger">*</span>
                </label>
                <input
                  id="nome_empresa"
                  v-model="form.nome_empresa"
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': errors.nome_empresa }"
                  placeholder="Ex: Empresa Exemplo LTDA"
                  maxlength="255"
                />
                <div v-if="errors.nome_empresa" class="invalid-feedback">
                  {{ errors.nome_empresa[0] }}
                </div>
              </div>

              <!-- Data de Vencimento -->
              <div class="mb-4">
                <label for="data_vencimento" class="form-label fw-bold">
                  Data de Vencimento <span class="text-danger">*</span>
                </label>
                <input
                  id="data_vencimento"
                  v-model="form.data_vencimento"
                  type="date"
                  class="form-control"
                  :class="{ 'is-invalid': errors.data_vencimento }"
                />
                <div v-if="errors.data_vencimento" class="invalid-feedback">
                  {{ errors.data_vencimento[0] }}
                </div>
              </div>

              <!-- Upload de Arquivo -->
              <div class="mb-4">
                <label class="form-label fw-bold">
                  Arquivo da Nota Fiscal <span class="text-danger">*</span>
                </label>
                
                <!-- Drop Zone -->
                <div
                  class="upload-zone"
                  :class="{ 
                    'drag-over': isDragOver, 
                    'has-file': form.arquivo,
                    'is-invalid': errors.arquivo 
                  }"
                  @drop="onDrop"
                  @dragover="onDragOver"
                  @dragleave="onDragLeave"
                  @click="$refs.fileInput.click()"
                >
                  <div v-if="!form.arquivo" class="text-center py-4">
                    <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                    <h5 class="mb-2">Arraste o arquivo aqui ou clique para selecionar</h5>
                    <p class="text-muted mb-0">
                      Formatos aceitos: XML, PDF (máximo 10MB)
                    </p>
                  </div>
                  
                  <!-- Arquivo Selecionado -->
                  <div v-else class="d-flex align-items-center justify-content-between p-3">
                    <div class="d-flex align-items-center">
                      <i 
                        :class="getFileIcon(form.arquivo.name)"
                        class="fs-2 me-3"
                        :style="{ color: getFileColor(form.arquivo.name) }"
                      ></i>
                      <div>
                        <h6 class="mb-0">{{ form.arquivo.name }}</h6>
                        <small class="text-muted">
                          {{ formatFileSize(form.arquivo.size) }}
                        </small>
                      </div>
                    </div>
                    <button
                      type="button"
                      class="btn btn-outline-danger btn-sm"
                      @click.stop="removerArquivo"
                    >
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>

                <!-- Input File Oculto -->
                <input
                  ref="fileInput"
                  type="file"
                  accept=".xml,.pdf"
                  style="display: none"
                  @change="onFileSelect"
                />

                <div v-if="errors.arquivo" class="text-danger small mt-2">
                  {{ errors.arquivo[0] }}
                </div>
              </div>

              <!-- Progress Bar -->
              <div v-if="uploading" class="mb-4">
                <label class="form-label fw-bold">Progresso do Upload</label>
                <div class="progress mb-2">
                  <div
                    class="progress-bar progress-bar-striped progress-bar-animated"
                    :style="{ width: `${uploadProgress}%` }"
                    role="progressbar"
                  >
                    {{ uploadProgress }}%
                  </div>
                </div>
                <small class="text-muted">Enviando arquivo...</small>
              </div>

              <!-- Botões -->
              <div class="d-flex justify-content-between">
                <RouterLink to="/" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-2"></i>
                  Voltar
                </RouterLink>
                
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="uploading || !isFormValid"
                >
                  <div v-if="uploading" class="spinner-border spinner-border-sm me-2"></div>
                  <i v-else class="bi bi-send me-2"></i>
                  {{ uploading ? 'Enviando...' : 'Enviar Nota Fiscal' }}
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Dicas de Uso -->
        <div class="card mt-4">
          <div class="card-header bg-light">
            <h6 class="card-title mb-0">
              <i class="bi bi-lightbulb me-2"></i>
              Dicas para um upload bem-sucedido
            </h6>
          </div>
          <div class="card-body">
            <ul class="mb-0">
              <li class="mb-2">
                <strong>Formatos aceitos:</strong> Arquivos XML ou PDF apenas
              </li>
              <li class="mb-2">
                <strong>Tamanho máximo:</strong> 10MB por arquivo
              </li>
              <li class="mb-2">
                <strong>CNPJ:</strong> Deve ser um CNPJ válido e ativo
              </li>
              <li class="mb-0">
                <strong>Data de vencimento:</strong> Não pode ser anterior à data atual
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useNotasFiscaisStore } from '@/stores/notasFiscais'
import { useToast } from 'vue-toastification'
import { apiUtils } from '@/services/api'

const router = useRouter()
const store = useNotasFiscaisStore()
const toast = useToast()

// Estado reativo
const form = ref({
  cnpj: '',
  nome_empresa: '',
  data_vencimento: '',
  arquivo: null
})

const errors = ref({})
const uploading = ref(false)
const uploadProgress = ref(0)
const isDragOver = ref(false)

// Computed properties
const isFormValid = computed(() => {
  return form.value.cnpj &&
         form.value.nome_empresa &&
         form.value.data_vencimento &&
         form.value.arquivo &&
         Object.keys(errors.value).length === 0
})

// Methods
const formatarCNPJ = (event) => {
  let value = event.target.value.replace(/\D/g, '')
  value = value.replace(/^(\d{2})(\d)/, '$1.$2')
  value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
  value = value.replace(/\.(\d{3})(\d)/, '.$1/$2')
  value = value.replace(/(\d{4})(\d)/, '$1-$2')
  form.value.cnpj = value
}

const onDragOver = (event) => {
  event.preventDefault()
  isDragOver.value = true
}

const onDragLeave = (event) => {
  event.preventDefault()
  isDragOver.value = false
}

const onDrop = (event) => {
  event.preventDefault()
  isDragOver.value = false
  
  const files = event.dataTransfer.files
  if (files.length > 0) {
    handleFile(files[0])
  }
}

const onFileSelect = (event) => {
  const file = event.target.files[0]
  if (file) {
    handleFile(file)
  }
}

const handleFile = (file) => {
  // Validar tipo de arquivo
  const allowedTypes = ['application/xml', 'text/xml', 'application/pdf']
  const allowedExtensions = ['.xml', '.pdf']
  
  const isValidType = allowedTypes.includes(file.type) || 
                      allowedExtensions.some(ext => file.name.toLowerCase().endsWith(ext))
  
  if (!isValidType) {
    toast.error('Formato de arquivo não suportado. Use apenas XML ou PDF.')
    return
  }

  // Validar tamanho (10MB)
  if (file.size > 10 * 1024 * 1024) {
    toast.error('Arquivo muito grande. O tamanho máximo é 10MB.')
    return
  }

  form.value.arquivo = file
  delete errors.value.arquivo
}

const removerArquivo = () => {
  form.value.arquivo = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const getFileIcon = (filename) => {
  const extension = filename.split('.').pop().toLowerCase()
  return extension === 'pdf' ? 'bi bi-file-pdf' : 'bi bi-file-code'
}

const getFileColor = (filename) => {
  const extension = filename.split('.').pop().toLowerCase()
  return extension === 'pdf' ? '#dc3545' : '#28a745'
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const limparErros = () => {
  errors.value = {}
}

const enviarNota = async () => {
  try {
    limparErros()
    uploading.value = true
    uploadProgress.value = 0

    // Preparar FormData
    const formData = apiUtils.formatFormData(form.value)

    // Fazer upload com progress
    const response = await apiUtils.uploadFile(
      '/notas-fiscais',
      formData,
      (progress) => {
        uploadProgress.value = progress
      }
    )

    // Sucesso
    toast.success('Nota fiscal enviada com sucesso!')
    
    // Redirecionar para detalhes ou lista
    router.push({
      name: 'DetalhesNota',
      params: { id: response.data.data.id }
    })

  } catch (error) {
    console.error('Erro no upload:', error)
    
    // Tratar erros de validação
    if (error.validationErrors) {
      errors.value = error.validationErrors
      toast.error('Corrija os erros no formulário e tente novamente.')
    } else {
      toast.error(error.message || 'Erro ao enviar nota fiscal.')
    }
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

// Refs
const fileInput = ref(null)
</script>

<style scoped>
.upload-zone {
  border: 2px dashed #dee2e6;
  border-radius: 0.375rem;
  padding: 2rem;
  cursor: pointer;
  transition: all 0.3s ease;
  background-color: #fafafa;
}

.upload-zone:hover {
  border-color: #0d6efd;
  background-color: #f8f9ff;
}

.upload-zone.drag-over {
  border-color: #0d6efd;
  background-color: #e7f1ff;
  transform: scale(1.02);
}

.upload-zone.has-file {
  border-color: #28a745;
  background-color: #f8fff9;
}

.upload-zone.is-invalid {
  border-color: #dc3545;
  background-color: #fff5f5;
}
</style> 