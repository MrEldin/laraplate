<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateContract;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-contract {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new contract';

    protected $type = 'Contract';

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
     * @see fire()
     * @return void
     */
    public function handle(){
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    public function fire()
    {
        try {
            (new GenerateLaraplateContract([
                'name'  => $this->argument('name'),
            ]))->run();

            $this->info("Laraplate contract successfully created.");

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
