<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaFiscalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cnpj' => $this->cnpj_formatado,
            'cnpj_numerico' => $this->cnpj,
            'data_vencimento' => [
                'original' => $this->data_vencimento->format('Y-m-d'),
                'formatada' => $this->data_vencimento->format('d/m/Y'),
                'timestamp' => $this->data_vencimento->timestamp,
            ],
            'nome_empresa' => $this->nome_empresa,
            'arquivo' => [
                'nome_original' => $this->nome_arquivo_original,
                'tipo' => strtoupper($this->tipo_arquivo),
                'tamanho_bytes' => $this->tamanho_arquivo,
                'tamanho_formatado' => $this->tamanho_formatado,
                'hash' => $this->hash_arquivo,
                'existe' => $this->whenAppended('arquivo_existe'),
            ],
            'status' => [
                'codigo' => $this->status,
                'descricao' => $this->getStatusDescricao(),
                'cor' => $this->getStatusCor(),
            ],
            'observacoes' => $this->observacoes,
            'datas' => [
                'upload' => [
                    'original' => $this->data_upload->format('Y-m-d H:i:s'),
                    'formatada' => $this->data_upload->format('d/m/Y H:i:s'),
                    'timestamp' => $this->data_upload->timestamp,
                    'humana' => $this->data_upload->diffForHumans(),
                ],
                'criacao' => [
                    'original' => $this->created_at->format('Y-m-d H:i:s'),
                    'formatada' => $this->created_at->format('d/m/Y H:i:s'),
                    'timestamp' => $this->created_at->timestamp,
                    'humana' => $this->created_at->diffForHumans(),
                ],
                'atualizacao' => [
                    'original' => $this->updated_at->format('Y-m-d H:i:s'),
                    'formatada' => $this->updated_at->format('d/m/Y H:i:s'),
                    'timestamp' => $this->updated_at->timestamp,
                    'humana' => $this->updated_at->diffForHumans(),
                ],
            ],
            'links' => [
                'self' => route('notas-fiscais.show', $this->id),
                'download' => route('notas-fiscais.download', $this->id),
                'update' => route('notas-fiscais.update', $this->id),
                'delete' => route('notas-fiscais.destroy', $this->id),
            ],
        ];
    }

    /**
     * Obter descrição do status
     */
    private function getStatusDescricao(): string
    {
        return match($this->status) {
            'pendente' => 'Aguardando Processamento',
            'processado' => 'Processado com Sucesso',
            'erro' => 'Erro no Processamento',
            default => 'Status Desconhecido',
        };
    }

    /**
     * Obter cor do status para frontend
     */
    private function getStatusCor(): string
    {
        return match($this->status) {
            'pendente' => 'warning',
            'processado' => 'success',
            'erro' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Resource simplificado para listagens
     */
    public function toSimpleArray(): array
    {
        return [
            'id' => $this->id,
            'cnpj' => $this->cnpj_formatado,
            'nome_empresa' => $this->nome_empresa,
            'arquivo' => [
                'nome' => $this->nome_arquivo_original,
                'tipo' => strtoupper($this->tipo_arquivo),
                'tamanho' => $this->tamanho_formatado,
            ],
            'status' => [
                'codigo' => $this->status,
                'descricao' => $this->getStatusDescricao(),
                'cor' => $this->getStatusCor(),
            ],
            'data_upload' => $this->data_upload->format('d/m/Y H:i'),
            'data_vencimento' => $this->data_vencimento->format('d/m/Y'),
        ];
    }
}
