<?php

use App\Models\Spot;
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
        Schema::create('categories', function (Blueprint $table) {
            // Primary key untuk tabel categories.
            $table->id();

            // Foreign key 'spot_id' yang merujuk ke tabel 'spots'.
            // Ini menandakan bahwa kategori ini 'milik' atau terkait dengan satu spot spesifik.
            $table->foreignIdFor(Spot::class);

            // Nama kategori, contoh: 'Kafe', 'Restoran', 'Taman Bermain'.
            $table->string('category');

            // Kolom 'created_at' dan 'updated_at' otomatis.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
