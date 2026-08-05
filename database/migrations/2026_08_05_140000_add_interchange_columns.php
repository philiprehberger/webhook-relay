<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Plan 7.2 — Interchange adoption columns.
 *
 * All additive and nullable/defaulted, so the rollback procedure can leave them
 * in place: v0 code ignores what it does not read (plan R-1/R-2).
 *
 * ONLINE DDL: `events` and `deliveries` are the highest-volume tables in this
 * fleet, so the statements below are written for `ALGORITHM=INPLACE, LOCK=NONE`.
 * Measured on production 2026-08-05 they hold six rows apiece — the care is
 * cheap here and the habit is what matters when it is applied to a table that
 * actually has traffic.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // The missing primitive: without this there is no way to ask
            // "which delivery came from this submission?".
            $table->char('correlation_id', 64)->nullable()->after('idempotency_key');
            $table->char('causation_id', 64)->nullable()->after('correlation_id');
            $table->char('trace_id', 32)->nullable()->after('causation_id');
            $table->string('trace_class', 16)->nullable()->after('trace_id');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->char('trace_id', 32)->nullable()->after('workspace_id');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Defaults to the native scheme permanently: Standard Webhooks
            // needs a newly issued whsec_ secret and a subscriber handshake,
            // so it can never be flipped in bulk (SPEC §4.5.1).
            $table->string('signature_scheme', 32)->default('webhook-relay-v0')->after('signing_secret');
        });

        // D-8: scheme-usage evidence in the database, not inferred from logs.
        Schema::create('signature_scheme_usage', function (Blueprint $table) {
            $table->id();
            $table->char('subscription_id', 26);
            $table->string('scheme', 32);
            $table->unsignedBigInteger('requests')->default(0);
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'scheme']);
            $table->index('last_seen_at');
        });

        // Indexes added separately so the algorithm can be stated explicitly.
        // Named to stay inside MySQL's 64-character identifier limit — the
        // generated name for the events index would be 52, which is fine, but
        // naming both keeps them greppable and predictable.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE events ADD INDEX events_workspace_correlation_idx (workspace_id, correlation_id), ALGORITHM=INPLACE, LOCK=NONE');
            DB::statement('ALTER TABLE events ADD INDEX events_trace_idx (trace_id), ALGORITHM=INPLACE, LOCK=NONE');
            DB::statement('ALTER TABLE deliveries ADD INDEX deliveries_trace_idx (trace_id), ALGORITHM=INPLACE, LOCK=NONE');
        } else {
            Schema::table('events', function (Blueprint $table) {
                $table->index(['workspace_id', 'correlation_id'], 'events_workspace_correlation_idx');
                $table->index('trace_id', 'events_trace_idx');
            });
            Schema::table('deliveries', function (Blueprint $table) {
                $table->index('trace_id', 'deliveries_trace_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_scheme_usage');

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('signature_scheme');
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex('deliveries_trace_idx');
            $table->dropColumn('trace_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_workspace_correlation_idx');
            $table->dropIndex('events_trace_idx');
            $table->dropColumn(['correlation_id', 'causation_id', 'trace_id', 'trace_class']);
        });
    }
};
