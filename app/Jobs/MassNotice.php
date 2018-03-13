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
    public $city;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationId, $city)
    {
        Log::useFiles(storage_path().'/logs/notification.log');
        Log::info('Notification ID - ' . $notificationId);
        $this->notificationId = $notificationId;
        $this->city = $city;
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
            ->where('city', $this->city)
//            ->distinct('vk_id')
            ->skip($offset)
            ->take($limit)
            ->get();

        $notice = null;
        while (count($recipients)) {
            $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
            foreach ($recipients as $recipient) {
                vkApi_messagesSend($recipient['vk_id'], $notice['text']);
            }
            sleep(1);
            $offset += 5;
            DB::connection('mongodb')
                ->collection('moment_notifications')
                ->where(['_id' => $this->notificationId])
                ->update([
                    'successRecipients' =>  $notice['successRecipients'] + count($recipients),
                    'is_working' => 1
                ]);

            $recipients = DB
                ::connection('mongodb')
                ->collection('subscribers')
                ->where('city', $this->city)
//                ->distinct('vk_id')
                ->skip($offset)
                ->take($limit)
                ->get();
        }

        DB::connection('mongodb')
            ->collection('moment_notifications')
            ->where(['_id' => $this->notificationId])
            ->update([
                'successRecipients' =>  $notice['successRecipients'] ?? 0 + count($recipients),
                'is_working' => 1,
                'sent' => 1,
                'queued' => 0,
            ]);
    }
}
