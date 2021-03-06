<?php

define('VK_API_VERSION', '5.67'); //Используемая версия API
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');

function vkApi_messagesSend($peer_id, $message, $attachments = array()) {
  return _vkApi_call('messages.send', array(
    'peer_id'    => $peer_id,
    'message'    => $message,
    'attachment' => implode(',', $attachments)
  ));
}

function vkApi_usersGet($user_id) {
  return _vkApi_call('users.get', array(
    'user_id' => $user_id,
  ));
}

function vkApi_photosGetMessagesUploadServer($peer_id) {
  return _vkApi_call('photos.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
  ));
}

function vkApi_photosSaveMessagesPhoto($photo, $server, $hash) {
  return _vkApi_call('photos.saveMessagesPhoto', array(
    'photo'  => $photo,
    'server' => $server,
    'hash'   => $hash,
  ));
}

function vkApi_docsGetMessagesUploadServer($peer_id, $type) {
  return _vkApi_call('docs.getMessagesUploadServer', array(
    'peer_id' => $peer_id,
    'type'    => $type,
  ));
}

function vkApi_docsSave($file, $title) {
  return _vkApi_call('docs.save', array(
    'file'  => $file,
    'title' => $title,
  ));
}

function _vkApi_call($method, $params = array(), $token = null) {
  $params['access_token'] = $token ?? env('VK_API_ACCESS_TOKEN');
  $params['v'] = VK_API_VERSION;

  $query = http_build_query($params);
  $url = VK_API_ENDPOINT.$method.'?'.$query;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$method} request");
  }

  curl_close($curl);

  $response = json_decode($json, true);
  if (!$response || !isset($response['response'])) {
    log_error($json);
    throw new Exception("Invalid response for {$method} request");
  }

  return $response['response'];
}

function vkApi_upload($url, $file_name) {
  if (!file_exists($file_name)) {
    throw new Exception('File not found: '.$file_name);
  }

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, array('file' => new CURLfile($file_name)));
  $json = curl_exec($curl);
  $error = curl_error($curl);
  if ($error) {
    log_error($error);
    throw new Exception("Failed {$url} request");
  }

  curl_close($curl);

  $response = json_decode($json, true);
  if (!$response) {
    throw new Exception("Invalid response for {$url} request");
  }

  return $response;
}


function log_msg($message) {
    if (is_array($message)) {
        $message = json_encode($message);
    }

    _log_write('[INFO] ' . $message);
}

function log_error($message) {
    if (is_array($message)) {
        $message = json_encode($message);
    }

    _log_write('[ERROR] ' . $message);
}

function _log_write($message) {
    $trace = debug_backtrace();
    $function_name = isset($trace[2]) ? $trace[2]['function'] : '-';
    $mark = date("H:i:s") . ' [' . $function_name . ']';
    $log_name = storage_path() .'/logs/log_' . date("j.n.Y") . '.txt';
    file_put_contents($log_name, $mark . " : " . $message . "\n", FILE_APPEND);
}


function laravelLog($variable) {
    ob_start();
    var_dump($variable);
    $out = ob_get_contents();
    \Illuminate\Support\Facades\Log::useFiles(storage_path() . '/logs/debug.log');
    \Illuminate\Support\Facades\Log::info($out);
}