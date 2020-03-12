<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

use Illuminate\Support\Str;

class FactoryMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine:factory
                            {name : Name of the entity this factory is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Factory (Doctrine)';

    /**
     * The class type
     *
     * @var string
     */
    protected $classType = "factory";

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
     * Replace the factory spefific parts
     * 
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceSpecific($stub, $name)
    {
        $cleanName = $this->getCleanName();

        $subs = $this->getNamespace($cleanName);
        $dummyEntity = str_replace($subs . '\\', '', $cleanName);
        $dummyEntityNamespace = $this->rootNamespace() . '\\' . config('doctrine-make.folderNames.entity');
        if ($subs != '') {
            $dummyEntityNamespace = $dummyEntityNamespace . '\\' . $subs;
        }

        $stub = str_replace(
            ['DummyEntityNamespace', 'DummyEntity', 'dummyEntity'],
            [$dummyEntityNamespace, $dummyEntity, Str::camel($dummyEntity)],
            $stub
        );


        return $stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    /*protected function buildClass($name)
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
    }*/
}