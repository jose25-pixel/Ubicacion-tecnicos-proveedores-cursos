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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('youtube_playlist_url')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('youtube_url');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->boolean('is_preview')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['course_id', 'slug']);
            $table->index(['course_id', 'sort_order']);
        });

        Schema::create('course_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('pending');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->char('currency', 3)->default('USD');

            $table->string('provider', 30)->default('paypal');
            $table->string('provider_order_id')->nullable()->unique();
            $table->string('provider_capture_id')->nullable()->unique();
            $table->string('payer_email')->nullable();
            $table->string('payer_id')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['provider', 'provider_order_id']);
        });

        Schema::create('course_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_order_id')->constrained('course_orders')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->decimal('unit_price', 10, 2);
            $table->unsignedTinyInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['course_order_id', 'course_id']);
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_order_id')->nullable()->constrained('course_orders')->nullOnDelete();
            $table->foreignId('last_watched_lesson_id')->nullable()->constrained('course_lessons')->nullOnDelete();
            $table->string('status', 30)->default('active');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']);
            $table->index(['status', 'expires_at']);
        });

        Schema::create('paypal_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->unique();
            $table->string('event_type', 120);
            $table->string('resource_type', 120)->nullable();
            $table->foreignId('course_order_id')->nullable()->constrained('course_orders')->nullOnDelete();
            $table->string('transmission_id')->nullable();
            $table->string('transmission_time')->nullable();
            $table->boolean('is_signature_valid')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->json('payload');
            $table->timestamps();

            $table->index(['event_type', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paypal_webhook_events');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_order_items');
        Schema::dropIfExists('course_orders');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('courses');
    }
};
