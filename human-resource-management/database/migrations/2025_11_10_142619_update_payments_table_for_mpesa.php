<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('payroll_id');
    });
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('payroll_id')->nullable()->after('employee_id');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('payroll_id');
    });
    Schema::table('payments', function (Blueprint $table) {
        $table->unsignedBigInteger('payroll_id')->nullable(false)->after('employee_id');
    });
}
};
