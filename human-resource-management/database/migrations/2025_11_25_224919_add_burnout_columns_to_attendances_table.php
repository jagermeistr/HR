<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_burnout_risk')->default(false)->after('is_late');
            $table->decimal('weekly_hours', 8, 2)->default(0)->after('overtime_hours');
            $table->string('burnout_level')->nullable()->after('weekly_hours');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['is_burnout_risk', 'weekly_hours', 'burnout_level']);
        });
    }
};