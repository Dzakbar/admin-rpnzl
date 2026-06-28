<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (! Schema::hasTable('schedules')) {
            return;
        }

        if (
            Schema::hasColumn('schedules', 'booking_time')
            && $this->uniqueIndexExists('schedules_booking_date_booking_time_unique')
        ) {
            $this->widenStatusColumn();

            return;
        }

        if (! Schema::hasColumn('schedules', 'booking_time')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->time('booking_time')->nullable();
            });
        }

        DB::table('schedules')
            ->whereNull('booking_time')
            ->update(['booking_time' => '09:00:00']);

        $this->dropUniqueIndexIfExists('schedules_booking_date_unique');
        $this->dropUniqueIndexIfExists('schedules_booking_date_booking_time_unique');
        $this->widenStatusColumn();

        if (! $this->uniqueIndexExists('schedules_booking_date_booking_time_unique')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->unique(['booking_date', 'booking_time'], 'schedules_booking_date_booking_time_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('schedules')) {
            return;
        }

        $this->dropUniqueIndexIfExists('schedules_booking_date_booking_time_unique');

        DB::table('schedules')
            ->where('status', 'limited')
            ->update(['status' => 'available']);

        $this->restoreStatusColumn();

        if (Schema::hasColumn('schedules', 'booking_time')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->dropColumn('booking_time');
            });
        }
    }

    private function dropUniqueIndexIfExists(string $index): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE schedules DROP CONSTRAINT IF EXISTS {$index}");
            DB::statement("DROP INDEX IF EXISTS {$index}");

            return;
        }

        try {
            Schema::table('schedules', function (Blueprint $table) use ($index) {
                $table->dropUnique($index);
            });
        } catch (Throwable) {
            // The index name differs by database driver or has already been removed.
        }
    }

    private function uniqueIndexExists(string $index): bool
    {
        if (DB::getDriverName() === 'pgsql') {
            return DB::selectOne(
                'SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ? LIMIT 1',
                ['schedules', $index]
            ) !== null;
        }

        return false;
    }

    private function widenStatusColumn(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE schedules MODIFY status VARCHAR(32) NOT NULL DEFAULT 'available'");
        } elseif (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE schedules ALTER COLUMN status TYPE VARCHAR(32)');
        }
    }

    private function restoreStatusColumn(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE schedules MODIFY status ENUM('available', 'blocked', 'fully_booked') NOT NULL DEFAULT 'available'");
        }
    }
};
