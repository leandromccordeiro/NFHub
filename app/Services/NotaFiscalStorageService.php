<?php

namespace App\Services;

use App\Models\NotaFiscal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class NotaFiscalStorageService
{
    /**
     * Armazenar arquivo de nota fiscal
     */
    public function armazenarArquivo(
        UploadedFile $arquivo,
        string $cnpj,
        string $dataVencimento,
        string $nomeEmpresa
    ): NotaFiscal {
        try {
            // Validar se o arquivo existe
            if (!$arquivo->isValid()) {
                throw new Exception('Arquivo inválido ou corrompido.');
            }

            // Gerar hash do arquivo
            $hashArquivo = hash_file('sha256', $arquivo->getRealPath());

            // Verificar se já existe um arquivo com o mesmo hash
            $notaExistente = NotaFiscal::where('hash_arquivo', $hashArquivo)->first();
            if ($notaExistente) {
                throw new Exception('Este arquivo já foi enviado anteriormente.');
            }

            // Determinar o tipo do arquivo
            $tipoArquivo = strtolower($arquivo->getClientOriginalExtension());
            if (!in_array($tipoArquivo, ['xml', 'pdf'])) {
                throw new Exception('Tipo de arquivo não permitido. Apenas XML e PDF são aceitos.');
            }

            // Criar estrutura de diretórios baseada na data atual
            $dataAtual = Carbon::now();
            $diretorioBase = 'notas-fiscais/' . $dataAtual->format('Y/m/d');

            // Gerar nome único para o arquivo
            $nomeArquivoUnico = $this->gerarNomeArquivoUnico($arquivo, $cnpj, $dataVencimento);

            // Caminho completo do arquivo
            $caminhoArquivo = $diretorioBase . '/' . $nomeArquivoUnico;

            // Armazenar o arquivo
            $caminhoCompleto = Storage::disk('local')->putFileAs(
                $diretorioBase,
                $arquivo,
                $nomeArquivoUnico
            );

            if (!$caminhoCompleto) {
                throw new Exception('Erro ao armazenar o arquivo.');
            }

            // Criar registro na base de dados
            $notaFiscal = NotaFiscal::create([
                'cnpj' => $cnpj,
                'data_vencimento' => $dataVencimento,
                'nome_empresa' => $nomeEmpresa,
                'nome_arquivo_original' => $arquivo->getClientOriginalName(),
                'caminho_arquivo' => $caminhoCompleto,
                'tipo_arquivo' => $tipoArquivo,
                'tamanho_arquivo' => $arquivo->getSize(),
                'hash_arquivo' => $hashArquivo,
                'data_upload' => $dataAtual,
                'status' => 'pendente',
            ]);

            return $notaFiscal;

        } catch (Exception $e) {
            // Se houve erro e o arquivo foi armazenado, remover
            if (isset($caminhoCompleto) && Storage::disk('local')->exists($caminhoCompleto)) {
                Storage::disk('local')->delete($caminhoCompleto);
            }
            
            throw $e;
        }
    }

    /**
     * Gerar nome único para o arquivo
     */
    private function gerarNomeArquivoUnico(
        UploadedFile $arquivo,
        string $cnpj,
        string $dataVencimento
    ): string {
        $timestamp = Carbon::now()->format('YmdHis');
        $extensao = $arquivo->getClientOriginalExtension();
        $dataVencimentoFormatada = Carbon::parse($dataVencimento)->format('Ymd');
        
        return "NF_{$cnpj}_{$dataVencimentoFormatada}_{$timestamp}.{$extensao}";
    }

    /**
     * Obter o caminho completo do arquivo
     */
    public function obterCaminhoCompleto(NotaFiscal $notaFiscal): string
    {
        return Storage::disk('local')->path($notaFiscal->caminho_arquivo);
    }

    /**
     * Verificar se o arquivo existe fisicamente
     */
    public function arquivoExiste(NotaFiscal $notaFiscal): bool
    {
        return Storage::disk('local')->exists($notaFiscal->caminho_arquivo);
    }

    /**
     * Remover arquivo físico e registro do banco
     */
    public function removerArquivo(NotaFiscal $notaFiscal): bool
    {
        try {
            // Remover arquivo físico se existir
            if ($this->arquivoExiste($notaFiscal)) {
                Storage::disk('local')->delete($notaFiscal->caminho_arquivo);
            }

            // Remover registro do banco
            $notaFiscal->delete();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obter estatísticas de armazenamento
     */
    public function obterEstatisticas(): array
    {
        $totalNotas = NotaFiscal::count();
        $totalTamanho = NotaFiscal::sum('tamanho_arquivo');
        $notasPorTipo = NotaFiscal::selectRaw('tipo_arquivo, COUNT(*) as quantidade')
            ->groupBy('tipo_arquivo')
            ->pluck('quantidade', 'tipo_arquivo')
            ->toArray();

        return [
            'total_notas' => $totalNotas,
            'total_tamanho_bytes' => $totalTamanho,
            'total_tamanho_formatado' => $this->formatarTamanho($totalTamanho),
            'notas_por_tipo' => $notasPorTipo,
        ];
    }

    /**
     * Formatar tamanho em bytes para exibição
     */
    private function formatarTamanho(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
} 