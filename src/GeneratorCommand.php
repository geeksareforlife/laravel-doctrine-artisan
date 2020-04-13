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

            // for the purposes of the name, a mapping will pretend to be an entity
            $type = $this->type == 'mapping' ? 'entity' : $this->type;

            // work out the prefix and suffix for the name itself
            $prefix = '';
            $suffix = '';
            if ($type == 'implementation') {
                $prefix = 'Doctrine';
                $suffix = 'Repository';
            } elseif ($type != 'entity') {
                $suffix = Str::studly($type);
            }

            $name = $prefix . $name . $suffix;

            $subspace = $this->getNamespaceOfType($type);
            if ($subspace != '') {
                $subspace = $subspace . '\\';
            }

            // implementation could have two different ways to setup the namespace
            if ($type == 'implementation') {
                $implementationLocation = config('doctrine-make.implementationLocation');
                if ($implementationLocation == 'collected') {
                    $name = $subspace . 'Doctrine\\' . $subs . $name;
                } else {
                    $name = $subspace . $subs . 'Doctrine\\' . $name;
                }
            } else {
                $name = $subspace . $subs . $name;
            }
        }

        return $name;
    }

    protected function getCleanName()
    {
        $name = trim($this->argument('name'));

        // replace all forward slashes with backslashes
        $name = str_replace('/', '\\', $name);

        return $name;
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
     * Injects some more replacements into the parent function.
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
     * Intended for the child class to override if needed.
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
     * A number of our classes will need to know details of the repositories.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceRepositories($stub)
    {
        $cleanName = $this->getCleanName();

        $stub = str_replace(
            [
                'DummyRepositoryInterfaceNamespace',
                'DummyRepositoryImplementationNamespace',
                'NamespacedDummyRepositoryInterface',
                'DummyRepository',
                'dummyRepository',
                'DummyRepositoryImplementation',
            ],
            [
                $this->getRepositoryInterfaceNamespace($cleanName),
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

    /**
     * Sorts the imports for the given stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function sortImports($stub)
    {
        if (preg_match('/(?P<imports>(?:use [^;]+;$\n?)+)/m', $stub, $match)) {
            $imports = explode("\n", trim($match['imports']));

            $sortType = config('doctrine-make.importSort');

            if ($sortType == 'length') {
                usort($imports, [$this, 'cmpLength']);
            } else {
                // default is alphe
                sort($imports);
            }

            return str_replace(trim($match['imports']), implode("\n", $imports), $stub);
        }

        return $stub;
    }

    /**
     * Length based sort for the import sort.
     */
    public static function cmpLength($a, $b)
    {
        $lenA = strlen($a);
        $lenB = strlen($b);

        if ($lenA == $lenB) {
            // back to alpha if same length!
            if ($a == $b) {
                return 0;
            }

            return ($a < $b) ? -1 : 1;
        }

        return ($lenA < $lenB) ? -1 : 1;
    }
}
