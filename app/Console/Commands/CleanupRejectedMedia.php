<?php

namespace App\Console\Commands;

use App\Models\Advert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupRejectedMedia extends Command
{
    protected $signature = 'media:cleanup';
    protected $description = 'Delete files for rejected and expired adverts older than 30 days';

    public function handle(): void
    {
        $adverts = Advert::whereIn('status', ['rejected'])
            ->where('updated_at', '<', now()->subDays(30))
            ->whereNotNull('file_path')
            ->get();

        $count = 0;
        foreach ($adverts as $advert) {
            if (Storage::disk('public')->exists($advert->file_path)) {
                Storage::disk('public')->delete($advert->file_path);
            }
            $advert->update(['file_path' => null]);
            $count++;
        }

        $this->info("Cleaned up {$count} media file(s).");
    }
}
