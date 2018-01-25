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
    $fname = $file->getFilename();
    $fpath = $file->getPath();
    $t = $file->getRealPath();
//    $file = new \SplFileObject($fpath . "\\" . $fname);
    $finfo = $file->openFile();
    while (!$file->eof()) {
        $string = $file->fgets();
        list($q, $a) = @explode("\\", $string);
        $logs = DB::connection('mongodb')->collection('qa')->insert([
            'question' => @$q,
            'answer' => @$a
        ]);
    }
    echo $content;
});