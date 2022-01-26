<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;
use Prettus\Repository\Generators\Migrations\SchemaParser;
use Prettus\Repository\Generators\ValidatorGenerator;

class GenerateLaraplateServiceProvider extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'service-provider';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace()
            . $this->getEntitiesFolderName() . '\\'
            . $this->getName() . '\\'
            . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'provider';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/'
            . parent::getConfigGeneratorClassPath($this->getPathConfigNode(), true) . '/'
            . $this->getName() . 'ServiceProvider.php';
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */

    public function getBasePath()
    {
        return config('nar.generator.basePath', app()->path()) . '/'
            . $this->getEntitiesFolderName() . '/'
            . $this->getName();
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $contract = (new GenerateLaraplateContract(['name' => $this->name]))->getMainNamespace('Repository');
        $repo = (new GenerateLaraplateRepository(['name' => $this->name]))->getMainNamespace('RepositoryEloquent');

        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'repository' => $contract,
            'repository_eloquent' => $repo,
            'model' => isset($this->options['model']) ? $this->options['model'] : ''
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
