<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('testimonials')) {
            return;
        }

        Schema::create('testimonials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('booking_id')->nullable()->unique()->constrained('bookings')->nullOnDelete();
            $table->foreignUuid('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('package_name')->nullable();
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_whatsapp', 30)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('message');
            $table->string('source', 24)->default('home');
            $table->string('status', 24)->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_featured']);
            $table->index(['rating', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
