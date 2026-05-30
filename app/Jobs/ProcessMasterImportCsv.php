<?php

namespace App\Jobs;

use App\Services\OrderImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMasterImportCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public string $originalFilename
    ) {}

    public function handle(OrderImportService $orderImportService): void
    {
        $orderImportService->import($this->filePath, $this->originalFilename);
    }
}
