<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['phone_number']);
            $table->dropUnique(['email']);

            $table->unique(['user_id', 'phone_number']);
            $table->unique(['user_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'phone_number']);
            $table->dropUnique(['user_id', 'email']);

            $table->unique('phone_number');
            $table->unique('email');
        });
    }
};
