<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class NotaFiscalCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = NotaFiscalResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'from' => $this->firstItem(),
                'to' => $this->lastItem(),
            ],
            'links' => [
                'first' => $this->url(1),
                'last' => $this->url($this->lastPage()),
                'prev' => $this->previousPageUrl(),
                'next' => $this->nextPageUrl(),
                'self' => $request->url(),
            ],
            'filtros_aplicados' => $this->getFiltrosAplicados($request),
        ];
    }

    /**
     * Obter filtros aplicados na requisição
     */
    private function getFiltrosAplicados(Request $request): array
    {
        $filtros = [];

        if ($request->has('cnpj')) {
            $filtros['cnpj'] = $request->cnpj;
        }

        if ($request->has('data_vencimento')) {
            $filtros['data_vencimento'] = $request->data_vencimento;
        }

        if ($request->has('status')) {
            $filtros['status'] = $request->status;
        }

        if ($request->has('sort_by')) {
            $filtros['ordenacao'] = [
                'campo' => $request->sort_by,
                'direcao' => $request->get('sort_order', 'desc'),
            ];
        }

        return $filtros;
    }

    /**
     * Adicionar informações adicionais ao cabeçalho da resposta
     */
    public function with(Request $request): array
    {
        return [
            'success' => true,
            'message' => 'Notas fiscais recuperadas com sucesso.',
            'timestamp' => now()->toISOString(),
            'request_id' => $request->header('X-Request-ID', uniqid()),
        ];
    }
}
