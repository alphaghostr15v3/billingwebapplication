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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('cgst_amount', 15, 2)->default(0)->after('tax_amount');
            $table->decimal('sgst_amount', 15, 2)->default(0)->after('cgst_amount');
            $table->decimal('igst_amount', 15, 2)->default(0)->after('sgst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['cgst_amount', 'sgst_amount', 'igst_amount']);
        });
    }
};
