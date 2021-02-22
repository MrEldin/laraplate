<?php

namespace App\Console;

use App\Console\Laraplate\Commands\MakeLaraplateContract;
use App\Console\Laraplate\Commands\MakeLaraplateController;
use App\Console\Laraplate\Commands\MakeLaraplateEntity;
use App\Console\Laraplate\Commands\MakeLaraplateModel;
use App\Console\Laraplate\Commands\MakeLaraplateRepository;
use App\Console\Laraplate\Commands\MakeLaraplateRequest;
use App\Console\Laraplate\Commands\MakeLaraplateRoutes;
use App\Console\Laraplate\Commands\MakeLaraplateServiceProvider;
use App\Console\Laraplate\Commands\MakeLaraplateTransformer;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        MakeLaraplateEntity::class,
        MakeLaraplateModel::class,
        MakeLaraplateContract::class,
        MakeLaraplateRepository::class,
        MakeLaraplateServiceProvider::class,
        MakeLaraplateController::class,
        MakeLaraplateTransformer::class,
        MakeLaraplateRequest::class,
        MakeLaraplateRoutes::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

//        require base_path('routes/console.php');
    }
}
