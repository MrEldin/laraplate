<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;
use Illuminate\Support\Str;

class GenerateLaraplateService extends Generator
{
    const GET_STUB_METHODS = [
        'find',
        'get',
        'delete'
    ];
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'service';

    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (in_array($this->method, self::GET_STUB_METHODS)) {
            $this->stub = 'index-service';
        }
    }

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace()
            . $this->getEntitiesFolderName() . '\\'
            . $this->getOnlyName() . '\\'
            . parent::getConfigGeneratorClassPath($this->getPathConfigNode());
    }

    /**
     * Get generator path config node.
     *
     * @return string
     */
    public function getPathConfigNode()
    {
        return 'services';
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
            . $this->getName() . 'Service.php';
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
            . $this->getOnlyName();
    }


    /**
     * Get original name.
     *
     * @return string
     */
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
     * @param null $nameAddition
     * @return string
     */
    public function getMainNamespace($nameAddition = null)
    {
        return $this->getRootNamespace() . '\\' . $nameAddition . $this->getOnlyName() . 'Service';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $contract = (new GenerateLaraplateContract(['name' => $this->name]))->getMainNamespace('Repository');

        return array_merge(parent::getReplacements(), [
            'small_class'          => Str::plural(strtolower($this->name)),
            'class'                => $this->getOnlyName(),
            'sub_class'            => $this->sub_name,
            'method'               => $this->method,
            'sub_data'             => $this->sub_data,
            'repository_namespace' => $contract,
            'fillable_data'        => $this->getFillable(),
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
            $results .= "\t\t'{$value}'," . PHP_EOL;
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
}
