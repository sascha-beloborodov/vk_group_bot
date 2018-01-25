<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('vk-bot', function () {
    (new \App\Services\VK\Main())->callbackHandleEvent();
});

Route::post('file', function(\Illuminate\Http\Request $request) {
    $file = $request->file('file');
    if (!$file) {
        return redirect('/');
    }
    if ($file->getError()) {
        return response($file->getErrorMessage());
    }
    $fileObject = $file->openFile();
    while (!$fileObject->eof()) {
        $string = $fileObject->fgets();
        $qaArray = @explode("\\", $string);
        if (empty($qaArray[0]) || empty($qaArray[1])) {
            continue;
        }
        list($q, $a) = $qaArray;
        $logs = DB::connection('mongodb')->collection('qa')->insert([
            'question' => @$q,
            'answer' => @$a
        ]);
    }
    echo 'success';
});