<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notas_fiscais', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj', 14)->index();
            $table->date('data_vencimento');
            $table->string('nome_empresa');
            $table->string('nome_arquivo_original');
            $table->string('caminho_arquivo');
            $table->string('tipo_arquivo')->comment('xml ou pdf');
            $table->bigInteger('tamanho_arquivo')->comment('em bytes');
            $table->string('hash_arquivo', 64)->unique()->comment('hash SHA256 do arquivo');
            $table->timestamp('data_upload');
            $table->enum('status', ['pendente', 'processado', 'erro'])->default('pendente');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_fiscais');
    }
};
