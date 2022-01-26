<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use App\Console\Laraplate\Generators\GenerateLaraplateRequest;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-request {name} {sub_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a request';

    protected $type = 'Request';

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
        $model = (new GenerateLaraplateModel(['name' => $this->argument('name')]))->getMainNamespace();

        try {
            (new GenerateLaraplateRequest([
                'name' => $this->argument('name'),
                'sub_name' => $this->argument('sub_name'),
                'model' => $model
            ]))->run();

            $this->info("Laraplate request successfully created.");

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
