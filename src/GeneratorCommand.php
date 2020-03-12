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

    protected function getNameInput()
    {
        $name = $this->getCleanName();

        // insert the subnamespace and name changes, for the class type, if we have one
        if (isset($this->type)) {
            // our name might have multiple components, such as Testy\Tester\Test
            // split these into the main name and sub components
            $subs = $this->getNamespace($name);
            if ($subs != '') {
                $subs = $subs . '\\';
            }

            $name = str_replace($subs, '', $name);

            $type = $this->type == "mapping" ? "entity" : $this->type;

            $prefix = '';
            $suffix = '';
            if ($type == "implementation") {
                $prefix = "Doctrine";
            }
            if ($type == "implementation") {
                $suffix = "Repository";
            } elseif ($type != "entity" and $type != "mapping") {
                $suffix = Str::studly($type);
            }

            $name = $subs . $prefix . $name . $suffix;

            $subspace = $this->getNamespaceOfType($type);

            if ($subspace != '') {
                $name = $subspace . '\\' . $name;
            }
        }

        return $name;
    }

    protected function getCleanName()
    {
        return trim($this->argument('name'));
    }

    protected function getPath($name)
    {
        if (config('doctrine-make.atAppLevel')) {
            $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        }

        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
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

        $stub = $this->replaceRepositories($stub);
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
    protected function replaceRepositories($stub)
    {
        $cleanName = $this->getCleanName();

        $stub = str_replace(
            ['DummyRepositoryInterfaceNamespace',
            'DummyRepositoryImplementationNamespace',
            'NamespacedDummyRepositoryInterface',
            'DummyRepository',
            'dummyRepository',
            'DummyRepositoryImplementation'
            ],
            [$this->getRepositoryInterfaceNamespace($cleanName),
            $this->getRepositoryImplementationNamespace($cleanName),
            $this->getNamespacedRepositoryInterface($cleanName),
            $this->getRepository($cleanName),
            Str::camel($this->getRepository($cleanName)),
            $this->getImplemention($cleanName),
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

        $name = Str::afterLast($name, '\\') . 'Respository';

        return $interface . '\\' . $name;
    }

    /**
     * Get the name of the repository for the entity.
     *
     * @return string
     */
    protected function getRepository($name)
    {
        // our name might have multiple components, such as Testy\Tester\Test
        // split these into the main name and sub components
        $subs = $this->getNamespace($name);
        if ($subs != '') {
            $subs = $subs . '\\';
        }

        $name = str_replace($subs, '', $name);

        return $name . 'Repository';
    }

    /**
     * Get the name of the repository implementation for the entity.
     *
     * @return string
     */
    protected function getImplemention($name)
    {
        // our name might have multiple components, such as Testy\Tester\Test
        // split these into the main name and sub components
        $subs = $this->getNamespace($name);
        if ($subs != '') {
            $subs = $subs . '\\';
        }

        $name = str_replace($subs, '', $name);

        return 'Doctrine' . $name . 'Repository';
    }
}