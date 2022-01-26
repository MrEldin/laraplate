<?php

namespace App\Console\Laraplate\Commands;

use App\Console\Laraplate\Generators\GenerateLaraplateController;
use App\Console\Laraplate\Generators\GenerateLaraplateModel;
use App\Console\Laraplate\Generators\GenerateLaraplateService;
use App\Console\Laraplate\Generators\GenerateLaraplateTestFile;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class MakeLaraplateController extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:l-controller {name} {--fillable=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller';

    protected $type = 'Controller';

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

            (new GenerateLaraplateModel([
                'name'     => $this->argument('name'),
                'fillable' => $this->option('fillable')
            ]))->run();

            $model = (new GenerateLaraplateModel(['name' => $this->argument('name')]))->getMainNamespace();

            $this->call('make:l-request', [
                'name'     => $this->argument('name'),
                'sub_name' => 'Create',
            ]);

            $this->call('make:l-request', [
                'name'     => $this->argument('name'),
                'sub_name' => 'Update',
            ]);

            $this->call('make:l-request', [
                'name'     => $this->argument('name'),
                'sub_name' => 'Get',
            ]);

            $this->call('make:l-request', [
                'name'     => $this->argument('name'),
                'sub_name' => 'Delete',
            ]);

            list($createService,
                $updateService,
                $getService,
                $indexService,
                $deleteService) = $this->generateServices($model);

            $createService->run();
            $updateService->run();
            $deleteService->run();
            $getService->run();
            $indexService->run();

            (new GenerateLaraplateController([
                'name'           => $this->argument('name'),
                'create_service' => $createService->getMainNamespace('Create'),
                'update_service' => $updateService->getMainNamespace('Update'),
                'get_service'    => $getService->getMainNamespace('Get'),
                'index_service'  => $indexService->getMainNamespace('Index'),
                'delete_service' => $deleteService->getMainNamespace('Delete'),
            ]))->run();

            $this->call('make:l-routes', [
                'name' => $this->argument('name')
            ]);

            $this->generateTests($model);

            $this->info("Laraplate controller successfully created.");
            $this->info("Please register new service provider created in app.php config file.");

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

    /**
     * @param string $model
     * @throws \App\Console\Laraplate\Exceptions\FileAlreadyExistsException
     */
    private function generateTests(string $model): void
    {
        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'Create',
            'model'    => $model,
            'method'   => 'post',
            'url'      => '/api/' . strtolower($this->argument('name')),
        ]))->run();

        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'Update',
            'model'    => $model,
            'method'   => 'put',
            'url'      => '/api/' . strtolower($this->argument('name')) . '/1',
        ]))->run();


        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'CreateX',
            'model'    => $model,
            'method'   => 'post',
            'url'      => '/api/' . strtolower($this->argument('name')),
        ]))->run();

        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'UpdateX',
            'model'    => $model,
            'method'   => 'put',
            'url'      => '/api/' . strtolower($this->argument('name')) . '/1',
        ]))->run();

        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'Get',
            'model'    => $model,
            'method'   => 'get',
            'url'      => '/api/' . strtolower($this->argument('name')),
        ]))->run();

        (new GenerateLaraplateTestFile([
            'name'     => $this->argument('name'),
            'sub_name' => 'Delete',
            'model'    => $model,
            'method'   => 'delete',
            'url'      => '/api/' . strtolower($this->argument('name')) . '/1',
        ]))->run();
    }

    /**
     * @param string $model
     * @return array
     */
    private function generateServices(string $model): array
    {
        $createService = (new GenerateLaraplateService([
            'name'     => $this->argument('name'),
            'sub_name' => 'Create',
            'model'    => $model,
            'method'   => 'create',
            'fillable' => $this->option('fillable')
        ]));

        $updateService = (new GenerateLaraplateService([
            'name'     => $this->argument('name'),
            'sub_name' => 'Update',
            'model'    => $model,
            'method'   => 'update',
            'sub_data' => '$id, ',
            'fillable' => $this->option('fillable')
        ]));

        $deleteService = (new GenerateLaraplateService([
            'name'     => $this->argument('name'),
            'sub_name' => 'Delete',
            'model'    => $model,
            'method'   => 'delete',
            'sub_data' => '$id',
            'fillable' => $this->option('fillable')
        ]));

        $getService = (new GenerateLaraplateService([
            'name'     => $this->argument('name'),
            'sub_name' => 'Get',
            'model'    => $model,
            'sub_data' => '$id',
            'method'   => 'find',
            'fillable' => $this->option('fillable')
        ]));

        $indexService = (new GenerateLaraplateService([
            'name'     => $this->argument('name'),
            'sub_name' => 'Index',
            'model'    => $model,
            'method'   => 'get',
            'fillable' => $this->option('fillable')
        ]));

        return array($createService, $updateService, $getService, $indexService, $deleteService);
    }
}
