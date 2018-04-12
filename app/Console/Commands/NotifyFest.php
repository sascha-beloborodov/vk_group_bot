<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyFest extends Command
{
    const MAX_DAYS_INTERVAL = 3;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:fest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users who subscribed on a fest in a city';

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
        $today = new \DateTime();
        for ($nextDays = 1; $nextDays <= self::MAX_DAYS_INTERVAL; $nextDays++) {
            $clonedToday = clone $today;
            $formattedNextDay = $clonedToday->add(new \DateInterval("P{$nextDays}D"))->format('Y-m-d');
            $fests = DB::connection('mongodb')->collection('fests')->where('date', $formattedNextDay)->get();

            foreach ($fests as $fest) {
                $usersOffset = 0;
                $usersLimit = 10;
                $subscribedUsers = DB::connection('mongodb')
                    ->collection('subscribers')
                    ->where('city_id', $fest['city_id'])
                    ->skip($usersOffset)
                    ->take($usersLimit)
                    ->get();
                while(count($subscribedUsers)) {
                    foreach ($subscribedUsers as $subscribedUser) {
                        vkApi_messagesSend($subscribedUser['vk_id'], 'Напоминаем, что до фестиваля в городе - ' . mb_strtoupper($fest['name']) . ' осталось ' . $nextDays . ' дня.');
                        sleep(2);
                    }
                    $usersOffset += 10;
                    $subscribedUsers = DB::connection('mongodb')
                        ->collection('subscribers')
                        ->where('city_id', $fest['city_id'])
                        ->skip($usersOffset)
                        ->take($usersLimit)
                        ->get();
                }
            }

        }

    }
}
