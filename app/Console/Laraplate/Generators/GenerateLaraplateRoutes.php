<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;
use Illuminate\Support\Str;
use Prettus\Repository\Generators\Migrations\SchemaParser;
use Prettus\Repository\Generators\ValidatorGenerator;

class GenerateLaraplateRoutes extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'routes';

    public function __construct(array $options = [])
    {
        parent::__construct($options);
    }

    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return parent::getRootNamespace(). 'Api\\'
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
        return 'routes';
    }

    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/'
            . $this->getName() . '/'
            . 'routes.php';
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
            . 'Routes';
    }

    /**
     * Get array replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        $controllerNamespace = (new GenerateLaraplateController(['name' => $this->name]))
            ->getMainNamespace('Controller');

        return array_merge(parent::getReplacements(), [
            'small_class' => Str::plural(strtolower($this->name)),
            'controller_namespace' => $controllerNamespace,
        ]);
    }

    /**
     * Get class name.
     *
     * @return string
     */
    public function getClass()
    {
        return Str::studly(class_basename($this->getName()));
    }
}
