<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NotaFiscal extends Model
{
    use HasFactory;

    protected $table = 'notas_fiscais';

    protected $fillable = [
        'cnpj',
        'data_vencimento',
        'nome_empresa',
        'nome_arquivo_original',
        'caminho_arquivo',
        'tipo_arquivo',
        'tamanho_arquivo',
        'hash_arquivo',
        'data_upload',
        'status',
        'observacoes',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_upload' => 'datetime',
        'tamanho_arquivo' => 'integer',
    ];

    /**
     * Formatar CNPJ para exibição
     */
    public function getCnpjFormatadoAttribute(): string
    {
        $cnpj = $this->cnpj;
        return substr($cnpj, 0, 2) . '.' . 
               substr($cnpj, 2, 3) . '.' . 
               substr($cnpj, 5, 3) . '/' . 
               substr($cnpj, 8, 4) . '-' . 
               substr($cnpj, 12, 2);
    }

    /**
     * Formatar tamanho do arquivo para exibição
     */
    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho_arquivo;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope para filtrar por CNPJ
     */
    public function scopeByCnpj($query, string $cnpj)
    {
        return $query->where('cnpj', $cnpj);
    }

    /**
     * Scope para filtrar por data de vencimento
     */
    public function scopeByDataVencimento($query, string $dataVencimento)
    {
        return $query->where('data_vencimento', $dataVencimento);
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
