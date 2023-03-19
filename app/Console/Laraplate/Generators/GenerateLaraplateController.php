<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;
use Prettus\Repository\Generators\Migrations\SchemaParser;
use Prettus\Repository\Generators\ValidatorGenerator;

class GenerateLaraplateController extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'controller';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . 'Api\\'
            . strtoupper(env('API_VERSION')) . '\\'
            . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'controllers';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/'
            . $this->getName() . 'Controller.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */

    public function getBasePath()
    {
        return config('nar.generator.basePath', app()->path()) . '/Api/'
            . strtoupper(env('API_VERSION')) . '/'
            . 'Controllers';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $contract = (new GenerateLaraplateContract(['name' => $this->name]))->getMainNamespace('Repository');
        $transformer = (new GenerateLaraplateTransformer(['name' => $this->name]))->getMainNamespace('Transformer');

        $createRequest = (new GenerateLaraplateRequest([
            'name' => $this->name,
            'sub_name' => 'Create',
        ]))->getMainNamespace('Request');

        $updateRequest = (new GenerateLaraplateRequest([
            'name' => $this->name,
            'sub_name' => 'Update',
        ]))->getMainNamespace('Request');

        $getRequest = (new GenerateLaraplateRequest([
            'name' => $this->name,
            'sub_name' => 'Get',
        ]))->getMainNamespace('Request');

        $deleteRequest = (new GenerateLaraplateRequest([
            'name' => $this->name,
            'sub_name' => 'Delete',
        ]))->getMainNamespace('Request');

        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'repository' => $contract,
            'class_small' => strtolower($this->name),
            'transformer' => $transformer,
            'create_request' => $createRequest,
            'update_request' => $updateRequest,
            'get_request' => $getRequest,
            'delete_request' => $deleteRequest,
            'create_service_namespace' => $this->create_service,
            'update_service_namespace' => $this->update_service,
            'get_service_namespace' => $this->get_service,
            'index_service_namespace' => $this->index_service,
            'delete_service_namespace' => $this->delete_service,
        ]);
    }

    /**
     * Get the fillable attributes.
     *
     * @return string
     */
    public function getFillable()
    {
        if (!$this->fillable) {
            return '[]';
        }
        $results = '[' . PHP_EOL;

        foreach ($this->getSchemaParser()->toArray() as $column => $value) {
            $results .= "\t\t'{$column}'," . PHP_EOL;
        }

        return $results . "\t" . ']';
    }

    /**
     * Get schema parser.
     *
     * @return SchemaParser
     */
    public function getSchemaParser()
    {
        return new SchemaParser($this->fillable);
    }

    public function getValidatorUse()
    {
        $validator = $this->getValidator();

        return "use {$validator};";
    }


    public function getValidator()
    {
        $validatorGenerator = new ValidatorGenerator([
            'name' => $this->name,
            'rules' => $this->rules,
            'force' => $this->force,
        ]);

        $validator = $validatorGenerator->getRootNamespace() . '\\' . $validatorGenerator->getName();

        return str_replace([
                "\\",
                '/'
            ], '\\', $validator) . 'Validator';

    }


    public function getValidatorMethod()
    {
        if ($this->validator != 'yes') {
            return '';
        }

        $class = $this->getClass();

        return '/**' . PHP_EOL . '    * Specify Validator class name' . PHP_EOL . '    *' . PHP_EOL . '    * @return mixed' . PHP_EOL . '    */' . PHP_EOL . '    public function validator()' . PHP_EOL . '    {' . PHP_EOL . PHP_EOL . '        return ' . $class . 'Validator::class;' . PHP_EOL . '    }' . PHP_EOL;

    }
}
