<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dynamic_forms', function (Blueprint $table) {
            $table->json('workflow_definition')->nullable()->after('success_message');
            $table->json('notification_settings')->nullable()->after('workflow_definition');
            $table->json('automation_settings')->nullable()->after('notification_settings');
        });

        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('id');
            $table->string('status')->default('submitted')->after('data');
            $table->unsignedInteger('current_step')->nullable()->after('status');
            $table->string('current_step_name')->nullable()->after('current_step');
            $table->text('decision_notes')->nullable()->after('current_step_name');
            $table->timestamp('reviewed_at')->nullable()->after('decision_notes');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');
            $table->timestamp('completed_at')->nullable()->after('rejected_at');
            $table->timestamp('last_activity_at')->nullable()->after('completed_at');
            $table->json('meta')->nullable()->after('last_activity_at');
            $table->unique('reference');
            $table->index(['form_id', 'status']);
        });

        Schema::create('dynamic_form_submission_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('dynamic_form_submissions')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('trigger')->nullable();
            $table->string('actor_name')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_form_submission_activities');

        Schema::table('dynamic_form_submissions', function (Blueprint $table) {
            $table->dropIndex(['form_id', 'status']);
            $table->dropUnique(['reference']);
            $table->dropColumn([
                'reference',
                'status',
                'current_step',
                'current_step_name',
                'decision_notes',
                'reviewed_at',
                'approved_at',
                'rejected_at',
                'completed_at',
                'last_activity_at',
                'meta',
            ]);
        });

        Schema::table('dynamic_forms', function (Blueprint $table) {
            $table->dropColumn([
                'workflow_definition',
                'notification_settings',
                'automation_settings',
            ]);
        });
    }
};
