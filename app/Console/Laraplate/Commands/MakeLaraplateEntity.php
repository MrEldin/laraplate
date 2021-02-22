<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-entity {name} {--fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new entity';

    protected $type = 'Entity';

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
        $name = $this->argument('name');
        try {
            $this->call('make:l-contract', ['name' => $name]);
            $this->call('make:l-repository', ['name' => $name]);
            $this->call('make:l-service-provider', ['name' => $name]);
            $this->call('make:l-transformer', ['name' => $name]);
            $this->call('make:l-controller', ['name' => $name, '--fillable' => $this->option('fillable')]);
            system('composer dump-autoload');
        } catch (\Exception $e) {
            $this->error($this->type . ' already exists!' . ' - ' . $e->getMessage());

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
