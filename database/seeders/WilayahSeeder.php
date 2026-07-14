<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $sqlPath = database_path('wilayah.sql');

        if (! file_exists($sqlPath)) {
            $this->command->error("File wilayah.sql tidak ditemukan di: {$sqlPath}");

            return;
        }

        $sql = file_get_contents($sqlPath);

        // Split by INSERT statements
        $inserts = preg_split('/^INSERT INTO/m', $sql, -1, PREG_SPLIT_NO_EMPTY);

        $count = 0;

        foreach ($inserts as $chunk) {
            $statement = 'INSERT INTO'.$chunk;

            // Only execute INSERT statements
            if (str_starts_with(trim($statement), 'INSERT INTO wilayah')) {
                try {
                    DB::unprepared($statement);
                } catch (\Exception $e) {
                    $this->command->warn('Gagal menjalankan: '.mb_substr($statement, 0, 80)."... — {$e->getMessage()}");

                    continue;
                }

                // Count rows in this batch by matching value tuples
                preg_match_all('/\(/', $statement, $matches);
                $count += count($matches[0]);
            }
        }

        $this->command->info("Wilayah: {$count} data berhasil diimpor.");
    }
}
