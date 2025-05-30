# 📁 API de Notas Fiscais - NFHub

Sistema para recepção e gerenciamento de uploads de notas fiscais (XML/PDF) via API REST com **API Resources** modularizados.

## 🚀 Configuração Inicial

### 1. Configurar Banco de Dados
Edite o arquivo `.env` com suas credenciais do banco:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nfhub
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

### 2. Executar Migrations
```bash
php artisan migrate
```

### 3. (Opcional) Popular com Dados de Teste
```bash
php artisan db:seed --class=NotaFiscalSeeder
```

### 4. Iniciar Servidor
```bash
php artisan serve
```

A API estará disponível em: `http://localhost:8000/api`

---

## 🏗️ **Arquitetura - API Resources**

O sistema utiliza **API Resources** para modularizar e padronizar as respostas, separando a responsabilidade de formatação:

### Resources Implementados:
- **`NotaFiscalResource`** - Resposta completa de uma nota fiscal
- **`NotaFiscalCollection`** - Coleção paginada de notas fiscais  
- **`NotaFiscalUploadResource`** - Resposta simplificada após upload
- **`EstatisticasResource`** - Estatísticas formatadas do sistema

### Benefícios:
✅ **Respostas Padronizadas** - Estrutura consistente em toda a API  
✅ **Versionamento Simples** - Fácil evolução das respostas  
✅ **Reutilização** - Resources podem ser reutilizados em diferentes endpoints  
✅ **Transformação** - Dados formatados automaticamente (datas, tamanhos, etc.)  

---

## 📋 Endpoints da API

### Base URL
```
http://localhost:8000/api/notas-fiscais
```

### 1. 📤 **Upload de Nota Fiscal**
**POST** `/api/notas-fiscais`

**Content-Type:** `multipart/form-data`

#### Parâmetros:
| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `cnpj` | string | ✅ | CNPJ (formatado ou apenas números) |
| `data_vencimento` | date | ✅ | Data no formato YYYY-MM-DD |
| `nome_empresa` | string | ✅ | Nome da empresa (2-255 caracteres) |
| `arquivo` | file | ✅ | Arquivo XML ou PDF (máx 10MB) |

#### Exemplo de Requisição:
```bash
curl -X POST http://localhost:8000/api/notas-fiscais \
  -F "cnpj=12.345.678/0001-90" \
  -F "data_vencimento=2025-06-15" \
  -F "nome_empresa=Empresa Exemplo LTDA" \
  -F "arquivo=@/caminho/para/nota_fiscal.xml"
```

#### Resposta de Sucesso (201) - `NotaFiscalUploadResource`:
```json
{
  "data": {
    "id": 32,
    "cnpj": "98.765.432/0001-10",
    "data_vencimento": "01/07/2025",
    "nome_empresa": "Nova Empresa LTDA",
    "arquivo": {
      "nome_original": "nota_fiscal_teste2.xml",
      "tipo": "XML",
      "tamanho": "1.16 KB"
    },
    "status": {
      "codigo": "pendente",
      "descricao": "Aguardando Processamento"
    },
    "data_upload": "30/05/2025 16:17:51",
    "hash_arquivo": "32412500cdbd021d0051cd2d6de8f66a19155e3dc390604126891e7ae204a94c",
    "links": {
      "visualizar": "http://localhost:8002/api/notas-fiscais/32",
      "download": "http://localhost:8002/api/notas-fiscais/32/download"
    }
  },
  "success": true,
  "message": "Nota fiscal enviada e armazenada com sucesso.",
  "timestamp": "2025-05-30T16:17:51.449871Z"
}
```

### 2. 📋 **Listar Notas Fiscais**
**GET** `/api/notas-fiscais`

#### Parâmetros de Query (opcionais):
| Parâmetro | Descrição | Exemplo |
|-----------|-----------|---------|
| `cnpj` | Filtrar por CNPJ | `?cnpj=12345678000190` |
| `data_vencimento` | Filtrar por data | `?data_vencimento=2025-06-15` |
| `status` | Filtrar por status | `?status=pendente` |
| `sort_by` | Campo para ordenação | `?sort_by=data_upload` |
| `sort_order` | Ordem (asc/desc) | `?sort_order=desc` |
| `per_page` | Itens por página | `?per_page=20` |

#### Exemplo:
```bash
curl "http://localhost:8000/api/notas-fiscais?status=pendente&per_page=10"
```

#### Resposta - `NotaFiscalCollection`:
```json
{
  "data": [
    {
      "id": 31,
      "cnpj": "12.345.678/0001-90",
      "cnpj_numerico": "12345678000190",
      "data_vencimento": {
        "original": "2025-06-15",
        "formatada": "15/06/2025",
        "timestamp": 1749945600
      },
      "nome_empresa": "Empresa Teste LTDA",
      "arquivo": {
        "nome_original": "nota_fiscal_teste.xml",
        "tipo": "XML",
        "tamanho_bytes": 1162,
        "tamanho_formatado": "1.13 KB",
        "hash": "2ba91cd7256e0f6d5e85e849c1671b2e82b84a81a87bd709ecc1b0f1fe935e2e"
      },
      "status": {
        "codigo": "processado",
        "descricao": "Processado com Sucesso",
        "cor": "success"
      },
      "observacoes": "Nota processada com sucesso via API",
      "datas": {
        "upload": {
          "original": "2025-05-30 14:55:05",
          "formatada": "30/05/2025 14:55:05",
          "timestamp": 1748616905,
          "humana": "2 horas atrás"
        },
        "criacao": { },
        "atualizacao": { }
      },
      "links": {
        "self": "http://localhost:8002/api/notas-fiscais/31",
        "download": "http://localhost:8002/api/notas-fiscais/31/download",
        "update": "http://localhost:8002/api/notas-fiscais/31",
        "delete": "http://localhost:8002/api/notas-fiscais/31"
      }
    }
  ],
  "meta": {
    "total": 32,
    "count": 1,
    "per_page": 1,
    "current_page": 1,
    "last_page": 32,
    "from": 1,
    "to": 1
  },
  "links": {
    "first": "http://localhost:8002/api/notas-fiscais?page=1",
    "last": "http://localhost:8002/api/notas-fiscais?page=32",
    "prev": null,
    "next": "http://localhost:8002/api/notas-fiscais?page=2",
    "self": "http://localhost:8002/api/notas-fiscais"
  },
  "filtros_aplicados": {},
  "success": true,
  "message": "Notas fiscais recuperadas com sucesso.",
  "timestamp": "2025-05-30T16:18:45.123456Z",
  "request_id": "unique_id_here"
}
```

### 3. 🔍 **Visualizar Nota Específica**
**GET** `/api/notas-fiscais/{id}`

#### Resposta - `NotaFiscalResource`:
Retorna estrutura completa com todos os campos formatados, incluindo informações sobre existência física do arquivo.

### 4. ✏️ **Atualizar Status/Observações**
**PUT** `/api/notas-fiscais/{id}`

**Content-Type:** `application/json`

#### Parâmetros:
| Campo | Tipo | Descrição |
|-------|------|-----------|
| `status` | string | `pendente`, `processado` ou `erro` |
| `observacoes` | string | Observações (máx 1000 caracteres) |

#### Resposta Atualizada:
```json
{
  "success": true,
  "message": "Nota fiscal atualizada com sucesso.",
  "data": {
    "id": 31,
    "status": {
      "codigo": "processado",
      "descricao": "Processado com Sucesso"
    },
    "observacoes": "Nota processada com sucesso",
    "atualizado_em": "30/05/2025 16:20:15"
  },
  "timestamp": "2025-05-30T16:20:15.123456Z"
}
```

### 5. 📥 **Download do Arquivo**
**GET** `/api/notas-fiscais/{id}/download`

Retorna o arquivo original ou erro JSON estruturado.

### 6. 🗑️ **Remover Nota Fiscal**
**DELETE** `/api/notas-fiscais/{id}`

#### Resposta de Sucesso:
```json
{
  "success": true,
  "message": "Nota fiscal removida com sucesso.",
  "data": {
    "id": 32,
    "nome_arquivo": "nota_fiscal_teste2.xml",
    "empresa": "Nova Empresa LTDA"
  },
  "timestamp": "2025-05-30T16:21:00.123456Z"
}
```

### 7. 📊 **Estatísticas do Sistema**
**GET** `/api/notas-fiscais/estatisticas`

#### Resposta - `EstatisticasResource`:
```json
{
  "data": {
    "resumo_geral": {
      "total_notas": 31,
      "tamanho_total": {
        "bytes": "80168710",
        "formatado": "76.45 MB",
        "em_mb": 76.45,
        "em_gb": 0.07
      }
    },
    "distribuicao_por_tipo": [
      {
        "tipo": "XML",
        "quantidade": 12,
        "percentual": 38.7,
        "cor": "#28a745"
      },
      {
        "tipo": "PDF",
        "quantidade": 19,
        "percentual": 61.3,
        "cor": "#dc3545"
      }
    ],
    "distribuicao_por_status": [
      {
        "status": "pendente",
        "descricao": "Aguardando Processamento",
        "quantidade": 0,
        "percentual": 0,
        "cor": "warning"
      }
    ],
    "metricas_temporais": {
      "uploads_hoje": 0,
      "uploads_semana": 0,
      "uploads_mes": 0,
      "media_diaria": 0
    },
    "alertas": [
      {
        "tipo": "warning",
        "titulo": "Alto Uso de Armazenamento",
        "mensagem": "O sistema está usando mais de 1GB de armazenamento.",
        "acao_sugerida": "Considere implementar arquivamento ou limpeza de arquivos antigos."
      }
    ],
    "meta": {
      "atualizado_em": "30/05/2025 16:22:30",
      "periodo_analise": "Últimos 30 dias",
      "fonte_dados": "Sistema NFHub"
    }
  },
  "success": true,
  "message": "Estatísticas recuperadas com sucesso.",
  "timestamp": "2025-05-30T16:22:30.123456Z"
}
```

---

## 🏗️ **Estrutura dos API Resources**

### NotaFiscalResource (Individual)
- **Dados completos** da nota fiscal
- **Múltiplos formatos** de data (original, formatada, timestamp, humana)
- **Status estruturado** com código, descrição e cor
- **Links HATEOAS** para ações relacionadas
- **Informações do arquivo** incluindo verificação de existência

### NotaFiscalCollection (Listagem)
- **Metadados de paginação** completos
- **Filtros aplicados** na requisição atual
- **Links de navegação** (primeira, última, anterior, próxima)
- **Informações de requisição** (ID, timestamp)

### NotaFiscalUploadResource (Upload)
- **Resposta simplificada** após upload
- **Links diretos** para visualização e download
- **Hash do arquivo** para verificação de integridade

### EstatisticasResource (Estatísticas)
- **Resumo geral** do sistema
- **Distribuições** por tipo e status com percentuais
- **Alertas inteligentes** baseados nos dados
- **Metadados** da análise

---

## 📁 Estrutura do Projeto Atualizada

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── NotaFiscalController.php
│   ├── Requests/
│   │   └── StoreNotaFiscalRequest.php
│   └── Resources/               # 🆕 API Resources
│       ├── EstatisticasResource.php
│       ├── NotaFiscalCollection.php
│       ├── NotaFiscalResource.php
│       └── NotaFiscalUploadResource.php
├── Models/
│   └── NotaFiscal.php
└── Services/
    └── NotaFiscalStorageService.php

routes/
└── api.php

database/
├── factories/
│   └── NotaFiscalFactory.php
├── migrations/
│   └── 2025_05_30_144426_create_notas_fiscais_table.php
└── seeders/
    └── NotaFiscalSeeder.php
```

---

## 🎯 Próximos Passos

1. **Integração com AWS S3** (produção)
2. **Sistema de processamento em background** (Queue)
3. **Webhook para notificações**
4. **Dashboard web para visualização**
5. **API de relatórios**
6. **Versionamento da API** (`/api/v1/`, `/api/v2/`)
7. **Cache de estatísticas** para melhor performance 