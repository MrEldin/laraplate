<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-model {name} {--fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model';

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
        try {
            (new GenerateLaraplateModel([
                'name'  => $this->argument('name'),
                'fillable' => $this->option('fillable')
            ]))->run();

            $this->info("Model created successfully.");

        } catch (\Exception $e) {
            $this->error($this->type . ' already exists!' . $e->getMessage());

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
