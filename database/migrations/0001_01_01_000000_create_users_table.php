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
        Schema::create('users', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->ulid('company_id')->nullable();
            $table->ulid('department_id')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_representative')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->timestamp('registered_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->ulid('created_by')->nullable()->index();
            $table->rememberToken();
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
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->ulid('created_by')->index();
            $table->timestamps();
        });

        Schema::create('kpis', function (Blueprint $table) {
            $table->ulid('id')->primary();
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
