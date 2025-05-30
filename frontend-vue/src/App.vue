<template>
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <div class="container">
        <RouterLink class="navbar-brand fw-bold" to="/">
          <i class="bi bi-receipt me-2"></i>
          NFHub
        </RouterLink>
        
        <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <RouterLink class="nav-link" to="/">
                <i class="bi bi-house me-1"></i>
                Dashboard
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/upload">
                <i class="bi bi-cloud-upload me-1"></i>
                Upload
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/notas">
                <i class="bi bi-list-ul me-1"></i>
                Notas Fiscais
              </RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="nav-link" to="/estatisticas">
                <i class="bi bi-bar-chart me-1"></i>
                Estatísticas
              </RouterLink>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="py-4">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center py-3 mt-auto">
      <div class="container">
        <small class="text-muted">
          © 2025 NFHub - Sistema de Gerenciamento de Notas Fiscais
        </small>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style>
/* Garantir que o footer fique no final da página */
#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

/* Customizações para os ícones Bootstrap */
@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css');

/* Estilo customizado para links ativos */
.router-link-active {
  font-weight: bold !important;
}

/* Animações suaves */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Loading spinner customizado */
.loading-spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #0d6efd;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Estilo para tabelas responsivas */
.table-container {
  overflow-x: auto;
}

/* Cards com hover effect */
.card-hover {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card-hover:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style> 