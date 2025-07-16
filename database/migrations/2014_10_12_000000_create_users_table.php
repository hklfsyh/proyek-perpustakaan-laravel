<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // id bigint, auto-increment, primary key
            $table->string('name'); // name varchar
            $table->string('email')->unique();
            $table->string('phone')->nullable(); // phone varchar
            $table->string('address')->nullable(); // address varchar
            $table->enum('role', ['admin', 'librarian', 'member']); // role enum
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // Kita buat nullable karena tidak wajib auth
            $table->rememberToken();
            $table->timestamps(); // created_at dan updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
