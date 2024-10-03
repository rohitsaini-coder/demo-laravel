
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;


/*
|--------------------------------------------------------------------------
|  Get User Records API Routes
|--------------------------------------------------------------------------
|
| Route         : http://localhost:8000/api/users
| Header        : Content-Type:application/json
| Parameters    : search (optional): string (e.g., ?search=term)
| Method        : GET
|
*/
Route::get('/users', [UserController::class, 'index']);


/*
|--------------------------------------------------------------------------
|  Add User Record API Route
|--------------------------------------------------------------------------
|
| Route         : http://localhost:8000/api/user/create
| Header        : Content-Type:application/json
| Method        : POST
*/
Route::post('/users/create', [UserController::class, 'store']);
