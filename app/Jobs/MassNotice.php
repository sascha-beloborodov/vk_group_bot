<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
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
        $offset = 0;
        $limit = 5;
        $recipients = DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->distinct('vk_id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        while ($recipients) {
            $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
            Log::info($notice);
            Log::info($recipients);
            die;
            foreach ($recipients as $recipient) {
                vkApi_messagesSend($recipient['vk_id'], $notice['text']);
            }
            sleep(1);
            $offset += 5;
            DB::connection('mongodb')
                ->collection('moment_notifications')
                ->where(['_id' => $this->notificationId])
                ->update([
                    'successRecipients' =>  $notice['successRecipients'] + $recipients->count(),
                    'is_working' => 1
                ]);

            $recipients = DB
                ::connection('mongodb')
                ->collection('subscribers')
                ->distinct('vk_id')
                ->offset($offset)
                ->limit($limit)
                ->get();
        }

        DB::connection('mongodb')
            ->collection('moment_notifications')
            ->where(['_id' => $this->notificationId])
            ->update([
                'successRecipients' =>  $notice['successRecipients'] + $recipients->count(),
                'is_working' => 1,
                'sent' => 1,
                'queued' => 0,
            ]);
    }
}
