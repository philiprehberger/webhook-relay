<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('workspace_id', 26);
            $table->string('type', 128);
            $table->json('payload');
            $table->string('idempotency_key', 255)->nullable();
            $table->string('source_ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->index(['workspace_id', 'created_at']);
            $table->index(['workspace_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
