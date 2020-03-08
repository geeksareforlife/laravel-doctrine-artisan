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

    protected function getNameInput()
    {
        $name = trim($this->argument('name'));

        // insert the subnamespace, if we have one
        if (isset($this->subspace) and $this->subspace != '') {
            $name = $this->subspace . '\\' . $name;
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
}