<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

class EntityMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine:entity
                            {name : Name of the entity to create}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Entity (Doctrine)';

    /**
     * Tha sub namespace for this type of class
     *
     * @var string
     */
    protected $subspace = "Entities";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/entity.stub';
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