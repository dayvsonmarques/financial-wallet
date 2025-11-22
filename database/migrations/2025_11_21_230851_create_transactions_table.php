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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('type', ['transfer', 'deposit', 'reversal'])->index();
            $table->decimal('amount', 15, 2);
            $table->string('status')->default('completed')->index(); // completed, reversed
            $table->text('description')->nullable();
            $table->foreignId('reversed_by_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['from_user_id', 'created_at']);
            $table->index(['to_user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
