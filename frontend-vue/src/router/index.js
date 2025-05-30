import { createRouter, createWebHistory } from 'vue-router'

// Importar as views/páginas
import Dashboard from '@/views/Dashboard.vue'
import Upload from '@/views/Upload.vue'
import NotasFiscais from '@/views/NotasFiscais.vue'
import DetalhesNota from '@/views/DetalhesNota.vue'
import Estatisticas from '@/views/Estatisticas.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: {
      title: 'Dashboard - NFHub'
    }
  },
  {
    path: '/upload',
    name: 'Upload',
    component: Upload,
    meta: {
      title: 'Upload de Nota Fiscal - NFHub'
    }
  },
  {
    path: '/notas',
    name: 'NotasFiscais',
    component: NotasFiscais,
    meta: {
      title: 'Notas Fiscais - NFHub'
    }
  },
  {
    path: '/notas/:id',
    name: 'DetalhesNota',
    component: DetalhesNota,
    props: true,
    meta: {
      title: 'Detalhes da Nota Fiscal - NFHub'
    }
  },
  {
    path: '/estatisticas',
    name: 'Estatisticas',
    component: Estatisticas,
    meta: {
      title: 'Estatísticas - NFHub'
    }
  },
  {
    // Rota de fallback para 404
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
    meta: {
      title: 'Página não encontrada - NFHub'
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Sempre volta para o topo ao navegar
    if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

// Guard para atualizar o título da página
router.beforeEach((to, from, next) => {
  document.title = to.meta.title || 'NFHub - Sistema de Notas Fiscais'
  next()
})

export default router 