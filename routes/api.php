<?php

use App\Http\Controllers\GooglemapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarkdownController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// routes/api.php

Route::get('/article/detail/{file}', [MarkdownController::class, 'convert']);
Route::get('/articles/all', [MarkdownController::class, 'getMarkdownFilesInDirectory']);
Route::get('/articles/list', [MarkdownController::class, 'getArticleList']);
Route::get('/googlemap/detail', [GooglemapController::class, 'getMapDetail']);
