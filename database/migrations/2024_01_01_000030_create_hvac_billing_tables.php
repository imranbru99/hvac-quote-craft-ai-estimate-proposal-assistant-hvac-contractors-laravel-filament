<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('license_no')->nullable();
            $table->string('specialty')->nullable();
            $table->string('status')->default('available');
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable()->index();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('priority')->default('normal');
            $table->string('status')->default('intake');
            $table->string('equipment')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('job_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable()->index();
            $table->foreignId('service_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('scheduled');
            $table->string('priority')->default('normal');
            $table->decimal('labor_hours', 8, 2)->default(0);
            $table->decimal('parts_total', 14, 2)->default(0);
            $table->decimal('labor_total', 14, 2)->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('job_ticket_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->decimal('subtotal', 14, 2)->default(0)->after('total');
            $table->decimal('tax', 14, 2)->default(0)->after('subtotal');
            $table->decimal('amount_paid', 14, 2)->default(0)->after('tax');
            $table->decimal('amount_due', 14, 2)->default(0)->after('amount_paid');
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->timestamp('due_at')->nullable()->after('issued_at');
            $table->timestamp('approved_at')->nullable()->after('due_at');
            $table->string('approved_by')->nullable()->after('approved_at');
            $table->longText('ai_draft')->nullable()->after('notes');
            $table->boolean('reminder_consent')->default(false)->after('ai_draft');
        });

        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable()->index();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel')->default('email');
            $table->string('status')->default('draft');
            $table->longText('body')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->boolean('consent_ok')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('code')->nullable()->index();
            $table->string('entity_type')->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('action')->nullable();
            $table->string('actor')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
        Schema::dropIfExists('payment_reminders');
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('job_ticket_id');
            $table->dropColumn([
                'subtotal', 'tax', 'amount_paid', 'amount_due', 'payment_status',
                'due_at', 'approved_at', 'approved_by', 'ai_draft', 'reminder_consent',
            ]);
        });
        Schema::dropIfExists('job_tickets');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('technicians');
    }
};
