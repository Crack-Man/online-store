<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol')->unique();
            $table->timestamps();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('slug')->constrained('units')->nullOnDelete();
        });

        $measures = DB::table('properties')
            ->whereNotNull('measure')
            ->where('measure', '<>', '')
            ->distinct()
            ->pluck('measure');

        foreach ($measures as $measure) {
            $unitId = DB::table('units')->insertGetId([
                'name' => $measure,
                'symbol' => $measure,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('properties')
                ->where('measure', $measure)
                ->update(['unit_id' => $unitId]);
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('measure');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('measure')->nullable()->after('slug');
        });

        DB::table('properties')
            ->join('units', 'units.id', '=', 'properties.unit_id')
            ->update(['properties.measure' => DB::raw('units.symbol')]);

        Schema::table('properties', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });

        Schema::dropIfExists('units');
    }
};
