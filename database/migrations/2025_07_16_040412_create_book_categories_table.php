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
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id(); // id bigint
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); // book_id bigint
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // category_id bigint
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_categories');
    }
};
