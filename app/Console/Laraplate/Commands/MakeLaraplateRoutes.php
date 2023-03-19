<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateContract;
use App\Console\Laraplate\Generators\GenerateLaraplateController;
use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use App\Console\Laraplate\Generators\GenerateLaraplateRepository;
use App\Console\Laraplate\Generators\GenerateLaraplateRoutes;
use App\Console\Laraplate\Generators\GenerateLaraplateServiceProvider;
use App\Console\Laraplate\Generators\GenerateLaraplateTestFile;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-routes {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a routes file';

    protected $type = 'Routes';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the command.
     *
     * @return void
     * @see fire()
     */
    public function handle()
    {
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    public function fire()
    {

        try {
            (new GenerateLaraplateRoutes([
                'name' => $this->argument('name')
            ]))->run();

            $this->info("Laraplate routes file successfully created.");
            $this->info("Please do not forget to register new routes file in Route Service Provider");

        } catch (\Exception $e) {
            $this->error($this->type . ' already exists!');
            $this->error($e->getMessage());

            return false;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the entity.'],
        ];
    }
}
