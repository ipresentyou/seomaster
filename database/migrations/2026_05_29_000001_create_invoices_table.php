<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->enum('status', ['draft', 'sent', 'error'])->default('draft');
            $table->decimal('net_amount', 10, 2);
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('gross_amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('description')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->string('xml_path', 500)->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
