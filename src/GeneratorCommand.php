<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand as LaravelGeneratorCommand;

abstract class GeneratorCommand extends LaravelGeneratorCommand
{
    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return config('doctrine-make.rootNamespace');
    }

    protected function getFolderName($type)
    {
        return config('doctrine-make.folderNames.' . $type);
    }

    protected function getNamespaceOfType($type)
    {
        $folder = $this->getFolderName($type);

        return str_replace('/', '\\', $folder);
    }

    protected function getNameInput($clean = false)
    {
        $name = trim($this->argument('name'));

        // insert the subnamespace, for the class type, if we have one
        // if we want the "clean" name, skip this
        if (isset($this->classType) and !$clean) {
            $classType = $this->classType == "mapping" ? "entity" : $this->classType;
            $subspace = $this->getNamespaceOfType($classType);
            if ($subspace != '') {
                $name = $subspace . '\\' . $name;
            }
        }

        return $name;
    }

    protected function getPath($name)
    {
        if (config('doctrine-make.atAppLevel')) {
            $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        }

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Build the class with the given name.
     * Injects some more replacements into the parent function
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = $this->replaceRepositories($stub, $name);
        $stub = $this->replaceSpecific($stub, $name);

        return $stub;
    }

    /**
     * Intended for the child class to override if needed
     * 
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceSpecific($stub, $name)
    {
        return $stub;
    }

    /**
     * A number of our classes will need to know details of the repositories
     * 
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceRepositories($stub, $name)
    {
        // go back and get the clean name of the class
        $cleanName = $this->getNameInput(true);
        $stub = str_replace(
            ['DummyRepositoryInterfaceNamespace',
            'DummyRepositoryImplementationNamespace',
            'NamespacedDummyRepositoryInterface'
            ],
            [$this->getRepositoryInterfaceNamespace($name),
            $this->getRepositoryImplementationNamespace($name),
            $this->getNamespacedRepositoryInterface($name)
            ],
            $stub
        );

        return $stub;
    }

    /**
     * Get the repository interface namespace for the class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRepositoryInterfaceNamespace($name)
    {
        $namespace = $this->rootNamespace() . '\\' . $this->getNamespaceOfType('repository');

        // add on the additional levels, if any
        if (strpos($name, '\\') !== false) {
            $namespace = $namespace . '\\' . Str::beforeLast($name, '\\');
        }

        return $namespace;
    }

    /**
     * Get the repository implementation namespace for the class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getRepositoryImplementationNamespace($name)
    {
        $namespace = $this->rootNamespace() . '\\' . $this->getNamespaceOfType('implementation');

        // add on the additional levels, if any
        if (strpos($name, '\\') !== false) {
            $namespace = $namespace . '\\' . Str::beforeLast($name, '\\');
        }

        return $namespace;
    }

    /**
     * Get the namespaced repository interface for the class.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespacedRepositoryInterface($name)
    {
        $interface = $this->getRepositoryInterfaceNamespace($name);

        $name = Str::afterLast($name, '\\') . 'Repository';

        return $interface . '\\' . $name;
    }
}