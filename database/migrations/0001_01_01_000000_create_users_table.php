<?php

use App\Enums\UserType;
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
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        /**
         * migration tables
         */

        Schema::create('accounts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->unique();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->ulid('admin_user_id')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('type')->default(UserType::ADMIN());
            $table->timestamp('onboarded_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->ulid('created_by')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('agent_assignments', function (Blueprint $table) {
            $table->ulid('user_id')->index();
            $table->ulid('account_id')->index();
            $table->ulid('company_id')->nullable();
            $table->ulid('department_id')->nullable();
            $table->string('position')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('user_permissions', function (Blueprint $table) {
            $table->ulid('user_id')->index();
            $table->string('permission')->index();
        });

        Schema::create('companies', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('kpis', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->index();
            $table->string('title');
            $table->ulid('category_id')->index();
            $table->text('description')->nullable();
            $table->string('measure_unit')->nullable();
            $table->boolean('is_active')->default(true);
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('forecasts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('account_id')->index();
            $table->ulid('kpi_id')->index();
            $table->ulid('company_id')->index();
            $table->ulid('department_id')->index();
            $table->integer('year');
            $table->integer('month');
            $table->string('target');
            $table->boolean('is_submitted')->default(false);
            $table->string('value')->nullable();
            $table->text('remarks')->nullable();
            $table->string('evidence_filepath')->nullable();
            $table->ulid('submitted_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
