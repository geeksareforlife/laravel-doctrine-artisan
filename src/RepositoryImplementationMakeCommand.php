<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

class RepositoryImplementationMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine:repository:implementation
                            {name : Name of the entity this implementation if for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Implementation (Doctrine)';

    /**
     * The class type
     *
     * @var string
     */
    protected $type = "implementation";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/doctrine-repository.stub';
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
}