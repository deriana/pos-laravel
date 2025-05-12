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
        Schema::create('accounts_receivable', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2);
            $table->date('due_date');
            $table->enum('payment_methode', ['cash', 'credit']);
            $table->enum('status', ['unpaid', 'partial', 'paid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_receivable');
    }
};
