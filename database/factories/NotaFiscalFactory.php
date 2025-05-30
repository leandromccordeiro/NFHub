<?php

namespace Database\Factories;

use App\Models\NotaFiscal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NotaFiscal>
 */
class NotaFiscalFactory extends Factory
{
    protected $model = NotaFiscal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tiposArquivo = ['xml', 'pdf'];
        $tipoArquivo = $this->faker->randomElement($tiposArquivo);
        $dataVencimento = $this->faker->dateTimeBetween('now', '+60 days');
        $dataUpload = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'cnpj' => $this->gerarCnpj(),
            'data_vencimento' => $dataVencimento->format('Y-m-d'),
            'nome_empresa' => $this->faker->company(),
            'nome_arquivo_original' => 'nota_fiscal_' . $this->faker->numberBetween(1000, 9999) . '.' . $tipoArquivo,
            'caminho_arquivo' => 'notas-fiscais/' . $dataUpload->format('Y/m/d') . '/NF_' . $this->gerarCnpj() . '_' . $dataVencimento->format('Ymd') . '_' . $dataUpload->format('YmdHis') . '.' . $tipoArquivo,
            'tipo_arquivo' => $tipoArquivo,
            'tamanho_arquivo' => $this->faker->numberBetween(50000, 5000000), // 50KB a 5MB
            'hash_arquivo' => $this->faker->sha256(),
            'data_upload' => $dataUpload,
            'status' => $this->faker->randomElement(['pendente', 'processado', 'erro']),
            'observacoes' => $this->faker->optional(0.3)->text(200),
        ];
    }

    /**
     * Gerar CNPJ válido
     */
    private function gerarCnpj(): string
    {
        $cnpj = '';
        
        // Gerar os primeiros 12 dígitos
        for ($i = 0; $i < 12; $i++) {
            $cnpj .= $this->faker->numberBetween(0, 9);
        }
        
        // Calcular os dígitos verificadores
        $cnpj .= $this->calcularDigitoVerificadorCnpj($cnpj, 1);
        $cnpj .= $this->calcularDigitoVerificadorCnpj($cnpj, 2);
        
        return $cnpj;
    }

    /**
     * Calcular dígito verificador do CNPJ
     */
    private function calcularDigitoVerificadorCnpj(string $cnpj, int $posicao): int
    {
        $sequencia = $posicao === 1 ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2] : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        
        for ($i = 0; $i < count($sequencia); $i++) {
            $soma += intval($cnpj[$i]) * $sequencia[$i];
        }
        
        $resto = $soma % 11;
        return $resto < 2 ? 0 : 11 - $resto;
    }

    /**
     * Estado pendente
     */
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pendente',
        ]);
    }

    /**
     * Estado processado
     */
    public function processado(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'processado',
        ]);
    }

    /**
     * Estado erro
     */
    public function erro(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'erro',
            'observacoes' => 'Erro ao processar o arquivo: ' . $this->faker->sentence(),
        ]);
    }

    /**
     * Tipo XML
     */
    public function xml(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_arquivo' => 'xml',
            'nome_arquivo_original' => 'nota_fiscal_' . $this->faker->numberBetween(1000, 9999) . '.xml',
        ]);
    }

    /**
     * Tipo PDF
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_arquivo' => 'pdf',
            'nome_arquivo_original' => 'nota_fiscal_' . $this->faker->numberBetween(1000, 9999) . '.pdf',
        ]);
    }
}
