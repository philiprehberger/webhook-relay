<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Separate table for idempotency dedup because MySQL has no partial unique
// indexes — keeping nullable idempotency_key on events would force ambiguous
// semantics on the events row. This table is the source of truth for
// "have we seen this key before?" and stores the cached response.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('idempotency_records', function (Blueprint $table) {
            $table->char('id', 26)->primary();
            $table->char('workspace_id', 26);
            $table->string('key', 255);
            $table->string('request_fingerprint', 64);
            $table->json('response_body');
            $table->unsignedSmallInteger('response_status');
            $table->char('event_id', 26)->nullable();
            $table->timestamp('expires_at')->index();
            $table->timestamps();

            $table->foreign('workspace_id')->references('id')->on('workspaces')->cascadeOnDelete();
            $table->unique(['workspace_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('idempotency_records');
    }
};
