<?php

namespace App\Console\Commands\Make;

class FactoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yggdrasil:make:factory
                            {name : Name of the entity this factory is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Factory';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/factory.stub';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        parent::handle();
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $name = str_replace($this->getDefaultNamespace($name).'\\', '', $name);
        $dummyFactoryNamspace = $this->rootNamespace().'\\Factories\\'.$name;
        $dummyFactoryNamspace = trim(implode('\\', array_slice(explode('\\', $dummyFactoryNamspace), 0, -1)), '\\');

        $stub = str_replace('DummyFactoryNamspace',
            $dummyFactoryNamspace,
            $stub
        );

        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace($this->getDefaultNamespace($name).'\\', '', $name);

        return $this->laravel['path'].'/Yggdrasil/Factories/'.str_replace('\\', '/', $name).'Factory.php';
    }
}