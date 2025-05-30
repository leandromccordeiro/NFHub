<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstatisticasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'resumo_geral' => [
                'total_notas' => $this->resource['total_notas'],
                'tamanho_total' => [
                    'bytes' => $this->resource['total_tamanho_bytes'],
                    'formatado' => $this->resource['total_tamanho_formatado'],
                    'em_mb' => round($this->resource['total_tamanho_bytes'] / 1024 / 1024, 2),
                    'em_gb' => round($this->resource['total_tamanho_bytes'] / 1024 / 1024 / 1024, 2),
                ],
            ],
            'distribuicao_por_tipo' => $this->formatarDistribuicaoTipos(),
            'distribuicao_por_status' => $this->obterDistribuicaoStatus(),
            'metricas_temporais' => $this->obterMetricasTemporais(),
            'top_empresas' => $this->obterTopEmpresas(),
            'alertas' => $this->gerarAlertas(),
            'meta' => [
                'atualizado_em' => now()->format('d/m/Y H:i:s'),
                'periodo_analise' => 'Últimos 30 dias',
                'fonte_dados' => 'Sistema NFHub',
            ],
        ];
    }

    /**
     * Formatar distribuição por tipos de arquivo
     */
    private function formatarDistribuicaoTipos(): array
    {
        $tipos = $this->resource['notas_por_tipo'];
        $total = array_sum($tipos);

        $distribuicao = [];
        foreach ($tipos as $tipo => $quantidade) {
            $percentual = $total > 0 ? round(($quantidade / $total) * 100, 1) : 0;
            $distribuicao[] = [
                'tipo' => strtoupper($tipo),
                'quantidade' => $quantidade,
                'percentual' => $percentual,
                'cor' => $this->getCorTipo($tipo),
            ];
        }

        return $distribuicao;
    }

    /**
     * Obter distribuição por status (simulado - seria bom ter no service)
     */
    private function obterDistribuicaoStatus(): array
    {
        // Aqui seria ideal ter esses dados vindo do service
        // Por enquanto, vou simular baseado na estrutura existente
        return [
            [
                'status' => 'pendente',
                'descricao' => 'Aguardando Processamento',
                'quantidade' => 0, // Seria obtido do banco
                'percentual' => 0,
                'cor' => 'warning',
            ],
            [
                'status' => 'processado',
                'descricao' => 'Processado com Sucesso',
                'quantidade' => 0,
                'percentual' => 0,
                'cor' => 'success',
            ],
            [
                'status' => 'erro',
                'descricao' => 'Erro no Processamento',
                'quantidade' => 0,
                'percentual' => 0,
                'cor' => 'danger',
            ],
        ];
    }

    /**
     * Obter métricas temporais
     */
    private function obterMetricasTemporais(): array
    {
        return [
            'uploads_hoje' => 0, // Seria calculado
            'uploads_semana' => 0,
            'uploads_mes' => 0,
            'media_diaria' => 0,
            'pico_uploads' => [
                'data' => null,
                'quantidade' => 0,
            ],
        ];
    }

    /**
     * Obter top empresas por volume de uploads
     */
    private function obterTopEmpresas(): array
    {
        // Seria calculado no service
        return [];
    }

    /**
     * Gerar alertas baseados nas estatísticas
     */
    private function gerarAlertas(): array
    {
        $alertas = [];
        $tamanhoTotal = $this->resource['total_tamanho_bytes'];
        $totalNotas = $this->resource['total_notas'];

        // Alerta de espaço
        if ($tamanhoTotal > 1024 * 1024 * 1024) { // > 1GB
            $alertas[] = [
                'tipo' => 'warning',
                'titulo' => 'Alto Uso de Armazenamento',
                'mensagem' => 'O sistema está usando mais de 1GB de armazenamento.',
                'acao_sugerida' => 'Considere implementar arquivamento ou limpeza de arquivos antigos.',
            ];
        }

        // Alerta de volume
        if ($totalNotas > 1000) {
            $alertas[] = [
                'tipo' => 'info',
                'titulo' => 'Alto Volume de Notas',
                'mensagem' => 'O sistema possui mais de 1000 notas fiscais.',
                'acao_sugerida' => 'Considere implementar indexação para melhor performance.',
            ];
        }

        return $alertas;
    }

    /**
     * Obter cor para tipo de arquivo
     */
    private function getCorTipo(string $tipo): string
    {
        return match(strtolower($tipo)) {
            'xml' => '#28a745', // Verde
            'pdf' => '#dc3545', // Vermelho
            default => '#6c757d', // Cinza
        };
    }

    /**
     * Adicionar metadados da resposta
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Estatísticas recuperadas com sucesso.',
            'timestamp' => now()->toISOString(),
        ];
    }
}
