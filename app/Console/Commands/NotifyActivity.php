<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyActivity extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users who subscribed on activity of a fest';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get tomorrow fests
        $tomorrowFests = DB::connection('mongodb')->collection('fests')->where('date', (new \DateTime('tomorrow'))->format('Y-m-d'))->get();
        
        foreach ($tomorrowFests as $tomorrowFest) {
            $offset = 0;
            $limit = 10;
            $activities = $this->usersActivities($tomorrowFest['id'], $offset, $limit);
            // will handle by parts
            while (count($activities)) {

                foreach ($activities as $activity) {
                    if (count($activity['activities'])) {
                        $message = "Завтра фестиваль в городе - " . mb_strtoupper($tomorrowFest['name']) . ". Ты записан на следующие активности:\n";
                        foreach ($activity['activities'] as $activityName) {
                            $message .= " - " . $activityName . ";\n";
                        }
                        vkApi_messagesSend($activity['vk_id'], $message);
                        sleep(1);
                    }
                }
                $offset += 10;
                $activities = $this->usersActivities($tomorrowFest['id'], $offset, $limit);
            }
        }
    }

    /**
     * Get subscribed users
     *
     * @param $cityId
     * @param $limit
     * @param $offset
     * @return mixed
     */
    private function subscribedUsers($cityId, $limit, $offset)
    {
        return DB::connection('mongodb')
            ->collection('subscribers')
            ->where('city_id', (int) $cityId)
            ->skip($offset)
            ->take($limit)
            ->get();
    }

    /**
     * Get user's activities
     *
     * @param $userId
     * @return mixed
     */
    private function usersActivities($cityId, $offset, $limit)
    {
        return DB::connection('mongodb')
            ->collection('activities')
            ->where('city_id', (int) $cityId)
            ->skip($offset)
            ->take($limit)
            ->get();
    }
}
