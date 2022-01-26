<?php

namespace App\Console\Laraplate\Generators;

use App\Console\Laraplate\Generator;

class GenerateLaraplateContract extends Generator
{
    /**
     * Get stub name.
     *
     * @var string
     */
    protected $stub = 'contracts';

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
        return 'contracts';
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
            . $this->getName() . 'Repository.php';
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
        return array_merge(parent::getReplacements(), [
            'fillable' => $this->getFillable()
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
}
