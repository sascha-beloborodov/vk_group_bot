<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class MassNotice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notificationId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationId)
    {
        Log::useFiles(storage_path().'/logs/notification.log');
        Log::info('Notification ID - ' . $notificationId);
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path().'/logs/notification.log');
        Log::info('Handle. Notification ID - ' . $this->notificationId);
    }
}
