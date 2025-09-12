<?php

namespace twa\smsautils\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use twa\apiutils\Traits\APITrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MigrationsController extends Controller
{
    public function run()
    {

        if (!Schema::hasTable('hubs')) {
            Schema::create('hubs', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->string('reference')->unique();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('routes')) {
            Schema::create('routes', function (Blueprint $table) {
                $table->id();

                $table->string('label');
                $table->bigInteger('capacity');
                $table->foreignId('hub_id')->constrained('hubs');
                $table->enum('mode', ['city', 'range', 'pattern']);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('couriers')) {
            Schema::create('couriers', function (Blueprint $table) {
                $table->id();
                $table->string('first_name');
                $table->string('last_name');
                $table->text('phone_number');
                $table->text('email');
                $table->string('national_id');
                $table->text('address');
                $table->text('driving_license_number');
                $table->date('license_expiry_date');
                $table->string('license_type');
                $table->date('hire_date');
                $table->foreignId('status_id')->nullable()->constrained('courier_statuses');
                $table->bigInteger('assigned_vehicle');
                $table->bigInteger('type');
                $table->text('contact_person_name');
                $table->text('contact_phone_number');
                $table->text('relationship');
                $table->bigInteger('driving_license_scan');
                $table->bigInteger('national_id_scan');
                $table->string('status')->nullable();
                ;
                $table->text('notes')->nullable();
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable('courier_statuses')) {
            Schema::create('courier_statuses', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('operators')) {
            Schema::create('operators', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->softDeletes();
                $table->bigInteger('hub_id');
                $table->string('name');
                $table->string('email');
                $table->string('password');
                $table->string('phone_number');
                $table->boolean('superadmin')->default(false);
                $table->text('roles_ids')->nullable();
            });
        }

        if (!Schema::hasTable('access_tokens')) {
            Schema::create('access_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('token');
                $table->morphs('tokenable');
                $table->timestamp('expires_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('verify_tokens')) {
            Schema::create('verify_tokens', function (Blueprint $table) {
                $table->id();
                $table->string('token');
                $table->morphs('tokenable');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes()->nullable();
            });
        }
        if (!Schema::hasTable('verify_token_attempts')) {
            Schema::create('verify_token_attempts', function (Blueprint $table) {
                $table->id();
                $table->string('token');
                $table->timestamp('attempt_at');
                $table->timestamp('used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('driver');
                $table->string('otp');
                $table->softDeletes()->nullable();
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->text('permissions');
                $table->timestamps();
            });
        }
        if (!Schema::hasTable('route_assignments')) {
            Schema::create('route_assignments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('route_id');
                $table->unsignedBigInteger('courier_id');
                $table->timestamp('assigned_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
        if (!Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->timestamps();
                $table->softDeletes();
            });
        }


        if (!Schema::hasTable('files')) {
            Schema::create('files', function (Blueprint $table) {
                $table->id();
                $table->string('original_name');
                $table->string('file_path');
                $table->string('mime_type');
                $table->unsignedBigInteger('size');
                $table->timestamps();
            });
        }
          if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}