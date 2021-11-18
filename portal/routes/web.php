<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\LogoutController;
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

/**
 * ログイン画面
 */
Route::get('/', [LoginController::class, 'login']);
Route::post('/', [LoginController::class, 'argoAuth']);

/**
 * プロジェクト画面
 */
Route::get('project', [ProjectController::class, 'project']);
Route::post('project/select', [ProjectController::class, 'projectSelect']);

/**
 * プロジェクト画面
 * クエリ実行、csvファイル作成
 */
Route::post('project/query', [QueryController::class, 'query']);
Route::post('project/export', [QueryController::class, 'export']);

/**
 * 視聴ログ取得画面
 * クエリ実行
 */
Route::get('querytest', [QueryController::class, 'queryTest']);
Route::post('execute', [QueryController::class, 'queryExecute']);

/**
 * ログアウト
 */
Route::get('logout', [LogoutController::class, 'logout']);
