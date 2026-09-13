<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This application serves a JSON API through Dingo; see routes/api.php and the
| accompanying users.php, roles.php and permissions.php files.
|
| NOTE: this file previously declared an "/image/{sizeX}/{sizeY}/{file}" route
| pointing at an ImageController that has never existed in this repository. It
| resolved lazily through the old RouteServiceProvider's controller namespace,
| so the breakage only surfaced when the route was actually hit. Laravel no
| longer applies a controller namespace, so the dead route has been removed.
|
*/

Route::get('/', fn () => view('welcome'));
