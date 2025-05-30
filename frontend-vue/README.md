# 🚀 NFHub Frontend - Vue.js

Frontend moderno e responsivo para o sistema de gerenciamento de notas fiscais NFHub, desenvolvido com Vue.js 3 + Bootstrap 5.

## 📋 Índice

- [Visão Geral](#visão-geral)
- [Tecnologias Utilizadas](#tecnologias-utilizadas)
- [Pré-requisitos](#pré-requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Desenvolvimento](#desenvolvimento)
- [Build de Produção](#build-de-produção)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Funcionalidades](#funcionalidades)
- [API](#api)
- [Contribuição](#contribuição)

## 🎯 Visão Geral

O NFHub Frontend é uma Single Page Application (SPA) que oferece uma interface intuitiva e moderna para gerenciar notas fiscais. O sistema permite upload, visualização, edição e exclusão de notas fiscais, além de fornecer estatísticas e relatórios detalhados.

### ✨ Principais Características

- **Interface Responsiva**: Design adaptativo que funciona perfeitamente em desktop, tablet e mobile
- **Upload com Drag & Drop**: Interface intuitiva para upload de arquivos XML/PDF
- **Feedback Visual**: Notificações toast, loading states e animações suaves
- **Filtros Avançados**: Sistema completo de busca e filtros
- **Paginação**: Navegação eficiente por grandes volumes de dados
- **Estatísticas**: Dashboards com métricas e gráficos
- **Validação**: Validação de formulários em tempo real
- **Responsividade**: Bootstrap 5 para layouts modernos

## 🛠️ Tecnologias Utilizadas

### Core
- **Vue.js 3** - Framework JavaScript progressivo
- **Vite** - Build tool rápido e moderno
- **Vue Router 4** - Roteamento SPA
- **Pinia** - Gerenciamento de estado

### UI/UX
- **Bootstrap 5** - Framework CSS responsivo
- **Bootstrap Icons** - Biblioteca de ícones
- **Vue Toastification** - Notificações toast
- **SweetAlert2** - Modais elegantes

### HTTP & API
- **Axios** - Cliente HTTP para API REST
- **Interceptors** - Tratamento global de erros

### Desenvolvimento
- **ESLint** - Linting de código
- **Prettier** - Formatação de código

## 📋 Pré-requisitos

- **Node.js** >= 16.0.0
- **npm** >= 8.0.0 ou **yarn** >= 1.22.0
- **API NFHub** rodando em `http://localhost:8000`

## 📦 Instalação

### 1. Clone o repositório
```bash
git clone <url-do-repositorio>
cd NFHub/frontend-vue
```

### 2. Instale as dependências
```bash
npm install
# ou
yarn install
```

### 3. Configure as variáveis de ambiente
```bash
cp .env.example .env
```

Edite o arquivo `.env` conforme necessário:
```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_TITLE=NFHub - Sistema de Notas Fiscais
```

## ⚙️ Configuração

### API Base URL

Por padrão, o frontend está configurado para acessar a API em `http://localhost:8000/api`. Se sua API estiver rodando em outro endereço, ajuste em `src/services/api.js`:

```javascript
const apiClient = axios.create({
  baseURL: 'http://seu-servidor:porta/api',
  // ...
})
```

### CORS

Certifique-se de que a API Laravel tenha CORS configurado para aceitar requisições do frontend:

```php
// config/cors.php
'allowed_origins' => ['http://localhost:3000'],
```

## 🚀 Desenvolvimento

### Iniciar servidor de desenvolvimento
```bash
npm run dev
# ou
yarn dev
```

O aplicativo estará disponível em `http://localhost:3000`

### Linting
```bash
npm run lint
# ou
yarn lint
```

### Hot Reload

O Vite oferece hot reload automático durante o desenvolvimento. Alterações em arquivos `.vue`, `.js` ou `.css` são refletidas instantaneamente no navegador.

## 🏗️ Build de Produção

### Gerar build de produção
```bash
npm run build
# ou
yarn build
```

### Pré-visualizar build
```bash
npm run preview
# ou
yarn preview
```

Os arquivos de produção serão gerados na pasta `dist/`.

## 📁 Estrutura do Projeto

```
src/
├── components/          # Componentes reutilizáveis
├── router/             # Configuração de rotas
│   └── index.js
├── services/           # Serviços HTTP e utilitários
│   └── api.js
├── stores/             # Stores Pinia
│   └── notasFiscais.js
├── views/              # Páginas/Views
│   ├── Dashboard.vue
│   ├── Upload.vue
│   ├── NotasFiscais.vue
│   ├── DetalhesNota.vue
│   ├── Estatisticas.vue
│   └── NotFound.vue
├── App.vue             # Componente raiz
└── main.js             # Ponto de entrada
```

### Convenções de Nomenclatura

- **Componentes**: PascalCase (`MeuComponente.vue`)
- **Views**: PascalCase (`MinhaView.vue`)
- **Stores**: camelCase (`meuStore.js`)
- **Services**: camelCase (`meuService.js`)

## ⚡ Funcionalidades

### 📊 Dashboard
- Visão geral do sistema
- Estatísticas rápidas
- Últimas notas fiscais
- Ações rápidas

### 📤 Upload de Notas
- Interface drag & drop
- Validação de arquivo (XML/PDF, max 10MB)
- Progress bar de upload
- Validação de formulário em tempo real
- Formatação automática de CNPJ

### 📋 Listagem de Notas
- Tabela responsiva com todas as notas
- Filtros por CNPJ, empresa, status e data
- Ordenação por diferentes campos
- Paginação eficiente
- Ações rápidas (visualizar, download, excluir)

### 🔍 Detalhes da Nota
- Visualização completa dos dados
- Edição de status e observações
- Download do arquivo original
- Timeline de datas
- Exclusão com confirmação

### 📈 Estatísticas
- Resumo geral do sistema
- Distribuição por tipo de arquivo
- Distribuição por status
- Métricas temporais
- Alertas do sistema

### 🚨 Tratamento de Erros
- Páginas 404 personalizadas
- Notificações toast para feedback
- Loading states visuais
- Validação de formulários
- Interceptors para erros de API

## 🔌 API

### Endpoints Utilizados

```javascript
// Listar notas fiscais
GET /api/notas-fiscais?page=1&per_page=15&status=pendente

// Upload de nota fiscal
POST /api/notas-fiscais
Content-Type: multipart/form-data

// Detalhes de uma nota
GET /api/notas-fiscais/{id}

// Atualizar nota
PUT /api/notas-fiscais/{id}
Content-Type: application/json

// Download de arquivo
GET /api/notas-fiscais/{id}/download

// Excluir nota
DELETE /api/notas-fiscais/{id}

// Estatísticas
GET /api/notas-fiscais/estatisticas
```

### Tratamento de Respostas

O frontend está preparado para trabalhar com os API Resources do Laravel:

```javascript
// Resposta esperada da API
{
  "data": { /* dados */ },
  "meta": { /* metadados de paginação */ },
  "links": { /* links de navegação */ },
  "success": true,
  "message": "Operação realizada com sucesso",
  "timestamp": "2025-01-XX..."
}
```

## 🎨 Customização

### Temas e Cores

O sistema utiliza as cores padrão do Bootstrap 5. Para customizar:

```css
/* src/App.vue ou arquivo CSS global */
:root {
  --bs-primary: #0d6efd;    /* Azul principal */
  --bs-success: #198754;    /* Verde sucesso */
  --bs-warning: #ffc107;    /* Amarelo alerta */
  --bs-danger: #dc3545;     /* Vermelho erro */
}
```

### Componentes Bootstrap

Aproveite a flexibilidade do Bootstrap 5:

```vue
<template>
  <!-- Cards responsivos -->
  <div class="card card-hover">
    <div class="card-header bg-primary text-white">
      <h5>Título</h5>
    </div>
    <div class="card-body">
      Conteúdo
    </div>
  </div>

  <!-- Alertas contextuais -->
  <div class="alert alert-success" role="alert">
    <i class="bi bi-check-circle me-2"></i>
    Operação realizada com sucesso!
  </div>
</template>
```

## 🐛 Solução de Problemas

### Erro de CORS
```
Access-Control-Allow-Origin header is present on the requested resource
```

**Solução**: Configure CORS no Laravel ou use um proxy:

```javascript
// vite.config.js
export default defineConfig({
  server: {
    proxy: {
      '/api': 'http://localhost:8000'
    }
  }
})
```

### Erro de Conexão com API
```
Network Error
```

**Verificações**:
1. API Laravel está rodando?
2. URL da API está correta em `src/services/api.js`?
3. Firewall bloqueando conexões?

### Problemas de Build
```
Module not found
```

**Soluções**:
1. Limpe cache: `rm -rf node_modules && npm install`
2. Verifique imports relativos vs absolutos
3. Configure alias no `vite.config.js`

## 🤝 Contribuição

### Guia de Desenvolvimento

1. **Fork** do repositório
2. **Crie** uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. **Abra** um Pull Request

### Padrões de Código

- Use **ESLint** e **Prettier**
- Siga as convenções Vue.js 3 Composition API
- Componentes devem ser responsivos (Bootstrap)
- Adicione comentários em lógicas complexas
- Teste as funcionalidades antes do commit

### Estrutura de Commits

```
tipo(escopo): descrição

- feat: nova funcionalidade
- fix: correção de bug
- docs: documentação
- style: formatação
- refactor: refatoração
- test: testes
```

## 📄 Licença

Este projeto está sob a licença [MIT](LICENSE).

## 📞 Suporte

Para dúvidas ou problemas:

1. **Issues**: Abra uma issue no repositório
2. **Documentação**: Consulte este README
3. **API**: Veja `README_API.md` na pasta raiz

---

**Desenvolvido com ❤️ para o sistema NFHub** 