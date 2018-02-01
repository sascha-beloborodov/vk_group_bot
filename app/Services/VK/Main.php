<?php

namespace App\Services\VK;

use Illuminate\Support\Facades\DB;

class Main {

    const CALLBACK_API_EVENT_CONFIRMATION = 'confirmation';
    const CALLBACK_API_EVENT_MESSAGE_NEW = 'message_new';

    function callbackHandleEvent() {
        $event = $this->callbackGetEvent();

        try {
            switch ($event['type']) {
                //Подтверждение сервера
                case self::CALLBACK_API_EVENT_CONFIRMATION:
                    $this->callbackHandleConfirmation();
                    break;

                //Получение нового сообщения
                case self::CALLBACK_API_EVENT_MESSAGE_NEW:
                    laravelLog($event);
                    $this->callbackHandleMessageNew($event['object']);
                    break;

                default:
//        _callback_response('Unsupported event');
                    break;
            }
        } catch (Exception $e) {
            log_error($e);
        }

        $this->callbackOkResponse();
    }

    function callbackGetEvent() {
        return json_decode(file_get_contents('php://input'), true);
    }

    function callbackHandleConfirmation() {
        $this->callbackResponse(env('CALLBACK_API_CONFIRMATION_TOKEN'));
    }

    function callbackHandleMessageNew($data) {

        $user_id = $data['user_id'];

        $msg = 'Нет вариантов';

        // die;

        // $logs = DB::connection('mongodb')->collection('logs')->insert($answer);
        (new Bot)->sendMessage($user_id, $msg);
        $this->callbackOkResponse();
    }

    function callbackOkResponse() {
        $this->callbackResponse('ok');
    }

    function callbackResponse($data) {
        echo $data;
        exit();
    }

}