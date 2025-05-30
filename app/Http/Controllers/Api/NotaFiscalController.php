<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotaFiscalRequest;
use App\Http\Resources\NotaFiscalCollection;
use App\Http\Resources\NotaFiscalResource;
use App\Http\Resources\NotaFiscalUploadResource;
use App\Http\Resources\EstatisticasResource;
use App\Models\NotaFiscal;
use App\Services\NotaFiscalStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class NotaFiscalController extends Controller
{
    public function __construct(
        private NotaFiscalStorageService $storageService
    ) {}

    /**
     * Listar todas as notas fiscais
     */
    public function index(Request $request): NotaFiscalCollection
    {
        $query = NotaFiscal::query();

        // Filtros opcionais
        if ($request->has('cnpj')) {
            $query->byCnpj($request->cnpj);
        }

        if ($request->has('data_vencimento')) {
            $query->byDataVencimento($request->data_vencimento);
        }

        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $perPage = $request->get('per_page', 15);
        $notas = $query->paginate($perPage);

        return new NotaFiscalCollection($notas);
    }

    /**
     * Armazenar uma nova nota fiscal
     */
    public function store(StoreNotaFiscalRequest $request): NotaFiscalUploadResource|JsonResponse
    {
        try {
            $arquivo = $request->file('arquivo');
            $cnpj = $request->validated('cnpj');
            $dataVencimento = $request->validated('data_vencimento');
            $nomeEmpresa = $request->validated('nome_empresa');

            $notaFiscal = $this->storageService->armazenarArquivo(
                $arquivo,
                $cnpj,
                $dataVencimento,
                $nomeEmpresa
            );

            return (new NotaFiscalUploadResource($notaFiscal))
                ->response()
                ->setStatusCode(201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao armazenar a nota fiscal.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 422);
        }
    }

    /**
     * Exibir uma nota fiscal específica
     */
    public function show(NotaFiscal $notaFiscal): NotaFiscalResource|JsonResponse
    {
        try {
            // Adicionar informação se o arquivo existe fisicamente
            $notaFiscal->append('arquivo_existe');
            $notaFiscal->arquivo_existe = $this->storageService->arquivoExiste($notaFiscal);

            return new NotaFiscalResource($notaFiscal);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar a nota fiscal.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Atualizar o status ou observações de uma nota fiscal
     */
    public function update(Request $request, NotaFiscal $notaFiscal): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'sometimes|in:pendente,processado,erro',
                'observacoes' => 'sometimes|string|max:1000',
            ]);

            $notaFiscal->update($request->only(['status', 'observacoes']));

            return response()->json([
                'success' => true,
                'message' => 'Nota fiscal atualizada com sucesso.',
                'data' => [
                    'id' => $notaFiscal->id,
                    'status' => [
                        'codigo' => $notaFiscal->status,
                        'descricao' => $this->getStatusDescricao($notaFiscal->status),
                    ],
                    'observacoes' => $notaFiscal->observacoes,
                    'atualizado_em' => $notaFiscal->updated_at->format('d/m/Y H:i:s'),
                ],
                'timestamp' => now()->toISOString(),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar a nota fiscal.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 422);
        }
    }

    /**
     * Remover uma nota fiscal
     */
    public function destroy(NotaFiscal $notaFiscal): JsonResponse
    {
        try {
            $dadosNota = [
                'id' => $notaFiscal->id,
                'nome_arquivo' => $notaFiscal->nome_arquivo_original,
                'empresa' => $notaFiscal->nome_empresa,
            ];

            $removido = $this->storageService->removerArquivo($notaFiscal);

            if ($removido) {
                return response()->json([
                    'success' => true,
                    'message' => 'Nota fiscal removida com sucesso.',
                    'data' => $dadosNota,
                    'timestamp' => now()->toISOString(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover a nota fiscal.',
                'timestamp' => now()->toISOString(),
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover a nota fiscal.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Obter estatísticas do sistema
     */
    public function estatisticas(): EstatisticasResource|JsonResponse
    {
        try {
            $estatisticas = $this->storageService->obterEstatisticas();
            return new EstatisticasResource($estatisticas);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao recuperar as estatísticas.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Download do arquivo da nota fiscal
     */
    public function download(NotaFiscal $notaFiscal): mixed
    {
        try {
            if (!$this->storageService->arquivoExiste($notaFiscal)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo não encontrado.',
                    'data' => [
                        'id' => $notaFiscal->id,
                        'nome_arquivo' => $notaFiscal->nome_arquivo_original,
                    ],
                    'timestamp' => now()->toISOString(),
                ], 404);
            }

            $caminhoCompleto = $this->storageService->obterCaminhoCompleto($notaFiscal);
            
            return response()->download(
                $caminhoCompleto,
                $notaFiscal->nome_arquivo_original
            );

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer download do arquivo.',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString(),
            ], 500);
        }
    }

    /**
     * Método auxiliar para descrição do status
     */
    private function getStatusDescricao(string $status): string
    {
        return match($status) {
            'pendente' => 'Aguardando Processamento',
            'processado' => 'Processado com Sucesso',
            'erro' => 'Erro no Processamento',
            default => 'Status Desconhecido',
        };
    }
}
