<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('workspace_id', 26);
            $table->string('name')->nullable();
            $table->string('url', 2048);
            $table->string('signing_secret', 128);
            $table->string('previous_signing_secret', 128)->nullable();
            $table->timestamp('secret_rotated_at')->nullable();
            $table->string('event_filter', 128)->default('*');
            $table->enum('state', ['active', 'paused', 'disabled'])->default('active');
            $table->unsignedInteger('consecutive_failures')->default(0);
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->index(['workspace_id', 'state']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
