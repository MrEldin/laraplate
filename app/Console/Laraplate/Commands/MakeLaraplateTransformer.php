<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use App\Console\Laraplate\Generators\GenerateLaraplateTransformer;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateTransformer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-transformer {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a transformer';

    protected $type = 'Transformer';

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
            (new GenerateLaraplateTransformer([
                'name' => $this->argument('name'),
                'model' => $model
            ]))->run();

            $this->info("Laraplate transformer successfully created.");

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
