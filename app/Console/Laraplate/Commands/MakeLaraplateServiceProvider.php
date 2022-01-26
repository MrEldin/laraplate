<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateContract;
use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use App\Console\Laraplate\Generators\GenerateLaraplateRepository;
use App\Console\Laraplate\Generators\GenerateLaraplateServiceProvider;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateServiceProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-service-provider {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider';

    protected $type = 'Service provider';

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
            (new GenerateLaraplateServiceProvider([
                'name' => $this->argument('name')
            ]))->run();

            $this->info("Laraplate service provider successfully created.");

        } catch (\Exception $e) {
            $this->error($this->type . ' already exists!');

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
