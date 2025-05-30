<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaFiscalUploadResource extends JsonResource
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
            'data_vencimento' => $this->data_vencimento->format('d/m/Y'),
            'nome_empresa' => $this->nome_empresa,
            'arquivo' => [
                'nome_original' => $this->nome_arquivo_original,
                'tipo' => strtoupper($this->tipo_arquivo),
                'tamanho' => $this->tamanho_formatado,
            ],
            'status' => [
                'codigo' => $this->status,
                'descricao' => $this->getStatusDescricao(),
            ],
            'data_upload' => $this->data_upload->format('d/m/Y H:i:s'),
            'hash_arquivo' => $this->hash_arquivo,
            'links' => [
                'visualizar' => route('notas-fiscais.show', $this->id),
                'download' => route('notas-fiscais.download', $this->id),
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
     * Adicionar informações de contexto
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Nota fiscal enviada e armazenada com sucesso.',
            'timestamp' => now()->toISOString(),
        ];
    }
}
