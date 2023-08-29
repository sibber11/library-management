<?php

use App\Models\Book;
use App\Models\Member;
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
        Schema::create('check_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Book::class)->constrained();
            $table->foreignIdFor(Member::class)->constrained();
            $table->date('check_out_date');
            $table->date('due_date');
            $table->date('check_in_date')->nullable();
            $table->boolean('is_checked_in')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_outs');
    }
};
