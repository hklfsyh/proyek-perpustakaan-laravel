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
        Schema::create('loans', function (Blueprint $table) {
            $table->id(); // id bigint
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // book_id bigint
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade'); // member_id bigint
            $table->foreignId('librarian_id')->constrained('users')->onDelete('cascade'); // librarian_id bigint
            $table->dateTime('loan_at'); // loan_at datetime
            $table->dateTime('returned_at')->nullable(); // returned_at datetime, bisa null
            $table->string('note')->nullable(); // note varchar
            // created_at & updated_at tidak ada di ERD untuk loans, jadi kita tidak pakai ->timestamps()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
