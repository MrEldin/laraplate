<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;
use Illuminate\Support\Str;
use Prettus\Repository\Generators\Migrations\SchemaParser;
use Prettus\Repository\Generators\ValidatorGenerator;

class GenerateLaraplateRequest extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'request';

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace() . 'Api\\'
            . strtoupper(env('API_VERSION')) . '\\'
            . parent::getConfigGeneratorClassPath($this->getPathConfigNode()) . '\\'
            . $this->getOnlyName();
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'requests';
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
            . $this->getOnlyName() . '/'
            . $this->getName() . 'Request.php';
    }

    public function getName()
    {
        $name = !is_null($this->sub_name)
            ? $this->sub_name . parent::getName()
            : parent::getName();

        return $name;
    }




    /**
     * Get name input.
     *
     * @return string
     */
    public function getOnlyName()
    {
        $name = $this->name;

        if (str_contains($this->name, '\\')) {
            $name = str_replace('\\', '/', $this->name);
        }
        if (str_contains($this->name, '/')) {
            $name = str_replace('/', '/', $this->name);
        }

        return Str::studly(str_replace(' ', '/', ucwords(str_replace('/', ' ', $name))));
    }

    /**
     * Get base path of destination file.
     *
     * @return string
     */

    public function getBasePath()
    {
        return config('nar.generator.basePath', app()->path()) . '/Api/'
            . strtoupper(env('API_VERSION')) . '/';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable(),
            'class_small' => strtolower($this->name),
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
