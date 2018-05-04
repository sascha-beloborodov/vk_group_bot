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
    public $cityId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notificationId, $cityId)
    {
        Log::useFiles(storage_path().'/logs/notification.log');
        Log::info('Notification ID - ' . $notificationId);
        $this->notificationId = $notificationId;
        $this->cityId = $cityId;
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

        $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
        $recipients = !empty($notice['activity']) ? $this->getUsersWithActivities($limit, $offset) : $this->getUsersWithSubscribe($limit, $offset);

        $totalRecipients = 0;
        while ($recipients->count()) {
            $totalRecipients += $recipients->count();
            $notice = DB::connection('mongodb')->collection('moment_notifications')->where('_id', $this->notificationId)->first();
            foreach ($recipients as $recipient) {
                vkApi_messagesSend($recipient['vk_id'], $notice['text']);
                sleep(1);
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

            $recipients = !empty($notice['activity']) ? $this->getUsersWithActivities($limit, $offset) : $this->getUsersWithSubscribe($limit, $offset);
        }
        DB::connection('mongodb')
            ->collection('moment_notifications')
            ->where(['_id' => $this->notificationId])
            ->update([
                'successRecipients' => $totalRecipients,
                'is_working' => 1,
                'sent' => 1,
                'queued' => 0,
            ]);
    }

    private function getUsersWithActivities($limit, $offset)
    {
        return DB
            ::connection('mongodb')
            ->collection('activities') //subscribers
            ->where('city_id', (int) $this->cityId)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    private function getUsersWithSubscribe($limit, $offset)
    {
        return DB
            ::connection('mongodb')
            ->collection('subscribers')
            ->where('city_id', (int) $this->cityId)
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
