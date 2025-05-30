<?php

namespace Database\Seeders;

use App\Models\NotaFiscal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotaFiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar 20 notas fiscais com diferentes status
        NotaFiscal::factory()
            ->count(10)
            ->pendente()
            ->create();

        NotaFiscal::factory()
            ->count(8)
            ->processado()
            ->create();

        NotaFiscal::factory()
            ->count(2)
            ->erro()
            ->create();

        // Criar algumas notas específicas por tipo
        NotaFiscal::factory()
            ->count(5)
            ->xml()
            ->pendente()
            ->create();

        NotaFiscal::factory()
            ->count(5)
            ->pdf()
            ->processado()
            ->create();

        $this->command->info('Notas fiscais de teste criadas com sucesso!');
    }
}
