<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersNeedsWhatsappNumber = ! Schema::hasColumn('users', 'whatsapp_number');
        $usersNeedsRole = ! Schema::hasColumn('users', 'role');

        if ($usersNeedsWhatsappNumber || $usersNeedsRole) {
            Schema::table('users', function (Blueprint $table) use ($usersNeedsWhatsappNumber, $usersNeedsRole) {
                if ($usersNeedsWhatsappNumber) {
                    $table->string('whatsapp_number')->nullable();
                }

                if ($usersNeedsRole) {
                    $table->enum('role', ['user', 'admin'])->default('user');
                }
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('package_name');
                $table->text('description');
                $table->decimal('price', 12, 2);
                $table->string('image')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('schedules')) {
            Schema::create('schedules', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->date('booking_date');
                $table->time('booking_time');
                $table->string('status', 32)->default('available');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['booking_date', 'booking_time']);
            });
        }

        if (! Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignUuid('package_id')->constrained('packages');
                $table->foreignUuid('schedule_id')->constrained('schedules');
                $table->string('event_type');
                $table->string('location');
                $table->text('customization_notes')->nullable();
                $table->enum('status', ['pending', 'confirmed', 'rejected', 'done'])->default('pending');
                $table->boolean('wa_sent')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('booking_id')->constrained('bookings')->cascadeOnDelete();
                $table->string('invoice_number')->unique();
                $table->date('invoice_date');
                $table->decimal('total_price', 12, 2);
                $table->string('pdf_path')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('galleries')) {
            Schema::create('galleries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('image_path');
                $table->string('caption')->nullable();
                $table->string('category')->default('general');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_contents')) {
            Schema::create('site_contents', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('text');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_contents');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('packages');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
