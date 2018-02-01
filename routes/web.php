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
use Illuminate\Support\Facades\DB;

// $logs = DB::connection('mongodb')->collection('logs')->insert(['request' => 'vk-bot']);


Route::get('/', function () {

    return view('welcome');
});

Route::any('vk-bot/hole', function(\Illuminate\Http\Request $request) {
    (new \App\Services\VK\Main())->callbackHandleEvent();
    die;
});

Route::post('file', function(\Illuminate\Http\Request $request) {
    // echo 'aaa';
    $file = $request->file('file');
    // if (!$file) {
    //     return redirect('/');
    // }
    // echo 'xxx';die;
    if ($file->getError()) {
        echo $file->getErrorMessage();
        die;
    }
    $fileObject = $file->openFile();
    var_dump($fileObject);
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
    die;
});


// Route::group([], function () {
//     // All your routes
//     // $logs = DB::connection('mongodb')->collection('logs')->insert(['request' => 'vk-bot route']);
    
// });

