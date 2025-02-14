<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::table('task_user', function (Blueprint $table) {
            if (!Schema::hasColumn('task_user', 'status')) {
                $table->string('status')->default('Pending');
            }
        });
    }
    
    public function down()
    {
        Schema::table('task_user', function (Blueprint $table) {
            if (Schema::hasColumn('task_user', 'status')) {
                $table->dropColumn('status'); 
            }
        });
    }
    
    
};
