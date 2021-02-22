<?php

namespace App\Console\Laraplate;

use App\Console\Laraplate\Exceptions\FileAlreadyExistsException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/**
 * Class Generator
 * @package Prettus\Repository\Generators
 * @author Eldin Mujovic <eldinpc@live.com>
 */
abstract class Generator
{

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $filesystem;

    /**
     * The array of options.
     *
     * @var array
     */
    protected $options;

    /**
     * The shortname of stub.
     *
     * @var string
     */
    protected $stub;


    /**
     * Create new instance of this class.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->filesystem = new Filesystem;
        $this->options = $options;
    }

    public function getMainNamespace($nameAddition = null)
    {
        return $this->getRootNamespace() . '\\' . $this->getName() . $nameAddition;
    }


    /**
     * Get the filesystem instance.
     *
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }


    /**
     * Set the filesystem instance.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return $this
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }


    /**
     * Get stub template for generated file.
     *
     * @return string
     */
    public function getStub()
    {
        $path = config('nar.generator.stubsOverridePath', __DIR__);

        if (!file_exists($path . '/Stubs/' . $this->stub . '.stub')) {
            $path = __DIR__;
        }

        return (new Stub($path . '/Stubs/' . $this->stub . '.stub', $this->getReplacements()))->render();
    }


    /**
     * Get stub template for generated file.
     *
     * @return string
     */
    public function getEntitiesFolderName()
    {
        $path = config('nar.generator.entitiesFolderName', __DIR__);

        return $path;
    }


    /**
     * Get template replacements.
     *
     * @return array
     */
    public function getReplacements()
    {
        return [
            'class' => $this->getClass(),
            'namespace' => $this->getNamespace(),
            'root_namespace' => $this->getRootNamespace()
        ];
    }


    /**
     * Get base path of destination file.
     *
     * @return string
     */
    public function getBasePath()
    {
        return base_path();
    }


    /**
     * Get destination path for generated file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getBasePath() . '/' . $this->getName() . '.php';
    }


    /**
     * Get name input.
     *
     * @return string
     */
    public function getName()
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
     * Get application namespace
     *
     * @return string
     */
    public function getAppNamespace()
    {
        return \Illuminate\Container\Container::getInstance()->getNamespace();
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


    /**
     * Get paths of namespace.
     *
     * @return array
     */
    public function getSegments()
    {
        return explode('/', $this->getName());
    }


    /**
     * Get root namespace.
     *
     * @return string
     */
    public function getRootNamespace()
    {
        return config('repository.generator.rootNamespace', $this->getAppNamespace());
    }


    /**
     * Get class-specific output paths.
     *
     * @param $class
     *
     * @param bool $directoryPath
     * @return string
     */
    public function getConfigGeneratorClassPath($class, $directoryPath = false)
    {
        switch ($class) {
            case ('models' === $class):
                $path = config('nar.generator.paths.models', 'Model');
                break;
            case ('contracts' === $class):
                $path = config('nar.generator.paths.contracts', 'Contract');
                break;
            case ('repositories' === $class):
                $path = config('nar.generator.paths.repositories', 'Repository');
                break;
            case ('provider' === $class):
                $path = config('nar.generator.paths.provider', 'ServiceProvider');
                break;
            case ('transformers' === $class):
                $path = config('nar.generator.paths.transformers', 'Transformers');
                break;
            case ('requests' === $class):
                $path = config('nar.generator.paths.requests', 'Requests');
                break;
            case ('controllers' === $class):
                $path = config('nar.generator.paths.controllers', 'Controllers');
                break;
            case ('routes' === $class):
                $path = config('nar.generator.paths.routes', 'Routes');
                break;
            case ('services' === $class):
                $path = config('nar.generator.paths.services', 'Services');
                break;
            default:
                $path = '';
        }

        if ($directoryPath) {
            $path = str_replace('\\', '/', $path);
        } else {
            $path = str_replace('/', '\\', $path);
        }


        return $path;
    }


    abstract public function getPathConfigNode();


    /**
     * Get class namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        $segments = $this->getSegments();
        array_pop($segments);
        $rootNamespace = $this->getRootNamespace();
        if ($rootNamespace == false) {
            return null;
        }

        return 'namespace ' . rtrim($rootNamespace . '\\' . implode($segments, '\\'), '\\') . ';';
    }


    /**
     * Setup some hook.
     *
     * @return void
     */
    public function setUp()
    {
        //
    }


    /**
     * Run the generator.
     *
     * @return int
     * @throws FileAlreadyExistsException
     */
    public function run()
    {
        $this->setUp();
        if ($this->filesystem->exists($path = $this->getPath()) && !$this->force) {
            throw new FileAlreadyExistsException($path);
        }
        if (!$this->filesystem->isDirectory($dir = dirname($path))) {
            $this->filesystem->makeDirectory($dir, 0777, true, true);
        }

        return $this->filesystem->put($path, $this->getStub());
    }


    /**
     * Get options.
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Determinte whether the given key exist in options array.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasOption($key)
    {
        return array_key_exists($key, $this->options);
    }


    /**
     * Get value from options by given key.
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string
     */
    public function getOption($key, $default = null)
    {
        if (!$this->hasOption($key)) {
            return $default;
        }

        return $this->options[$key] ?: $default;
    }


    /**
     * Helper method for "getOption".
     *
     * @param string $key
     * @param string|null $default
     *
     * @return string
     */
    public function option($key, $default = null)
    {
        return $this->getOption($key, $default);
    }


    /**
     * Handle call to __get method.
     *
     * @param string $key
     *
     * @return string|mixed
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }

        return $this->option($key);
    }
}
