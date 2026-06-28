<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('storage:migrate-to-supabase {--dry-run} {--public-disk=supabase_public} {--private-disk=supabase_private}', function () {
    $dryRun = (bool) $this->option('dry-run');

    $jobs = [
        [
            'label' => 'public uploads',
            'source' => 'public',
            'target' => $this->option('public-disk'),
            'directories' => ['gallery', 'packages'],
        ],
        [
            'label' => 'private invoices',
            'source' => 'local',
            'target' => $this->option('private-disk'),
            'directories' => ['invoices'],
        ],
    ];

    foreach ($jobs as $job) {
        $source = Storage::disk($job['source']);
        $target = null;
        $copied = 0;

        $this->info("Migrating {$job['label']} to {$job['target']}...");

        foreach ($job['directories'] as $directory) {
            if (! $source->exists($directory)) {
                $this->line("  Skipped {$directory}: local directory not found.");
                continue;
            }

            foreach ($source->allFiles($directory) as $file) {
                if ($dryRun) {
                    $this->line("  Would copy {$file}");
                    continue;
                }

                $target ??= Storage::disk($job['target']);
                $stream = $source->readStream($file);

                try {
                    $target->put($file, $stream);
                    $copied++;
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
            }
        }

        $dryRun
            ? $this->comment("Dry run finished for {$job['label']}.")
            : $this->info("Copied {$copied} file(s) for {$job['label']}.");
    }
})->purpose('Copy local uploads and invoices to Supabase Storage');
