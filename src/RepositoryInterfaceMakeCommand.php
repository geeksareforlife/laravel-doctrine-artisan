<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

class RepositoryInterfaceMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine:repository:interface
                            {name : Name of the entity this interface is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Interface (Doctrine)';

    /**
     * The class type
     *
     * @var string
     */
    protected $type = "repository";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository-interface.stub';
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
     * Replace the repository interface spefific parts
     * 
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceSpecific($stub, $name)
    {
        $classes = config('doctrine-make.interfaceParents');
        $dummyUseStatements = '';
        $dummyExtends = '';

        if (count($classes) > 0) {
            $extendClasses = [];
            $useClasses = [];

            foreach ($classes as $class) {
                $extendClasses[] = str_replace($this->getNamespace($class) . '\\', '', $class);
                $useClasses[] = 'use ' . $class . ';';
            }

            $dummyUseStatements = "\n" . implode("\n", $useClasses);
            $dummyExtends = "extends " . implode(', ', $extendClasses);
        }

        $stub = str_replace(
            ['DummyUseStatements', 'DummyExtends'],
            [$dummyUseStatements, $dummyExtends],
            $stub
        );

        return $stub;
    }
}