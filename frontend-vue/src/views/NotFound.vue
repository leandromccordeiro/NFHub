<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 text-center">
        <div class="py-5">
          <!-- Ícone 404 -->
          <div class="mb-4">
            <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
          </div>
          
          <!-- Título -->
          <h1 class="display-4 fw-bold text-primary mb-3">404</h1>
          <h2 class="h4 mb-4 text-muted">Página não encontrada</h2>
          
          <!-- Mensagem -->
          <p class="lead mb-4">
            Ops! A página que você está procurando não existe ou foi movida.
          </p>
          
          <!-- Ações -->
          <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
            <RouterLink 
              to="/" 
              class="btn btn-primary btn-lg"
            >
              <i class="bi bi-house me-2"></i>
              Voltar ao Dashboard
            </RouterLink>
            
            <RouterLink 
              to="/notas" 
              class="btn btn-outline-secondary btn-lg"
            >
              <i class="bi bi-list-ul me-2"></i>
              Ver Notas Fiscais
            </RouterLink>
          </div>
          
          <!-- Links úteis -->
          <div class="mt-5">
            <h6 class="text-muted mb-3">Links úteis:</h6>
            <div class="d-flex flex-wrap gap-3 justify-content-center">
              <RouterLink to="/upload" class="text-decoration-none">
                <i class="bi bi-cloud-upload me-1"></i>
                Upload de Nota
              </RouterLink>
              <RouterLink to="/estatisticas" class="text-decoration-none">
                <i class="bi bi-bar-chart me-1"></i>
                Estatísticas
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Página estática - sem lógica necessária
</script>

<style scoped>
.display-1 {
  font-size: 6rem;
}

.display-4 {
  font-size: 3rem;
}

@media (max-width: 576px) {
  .display-1 {
    font-size: 4rem;
  }
  
  .display-4 {
    font-size: 2rem;
  }
}
</style> 