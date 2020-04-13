<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

use Illuminate\Support\Str;

class MappingMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine:mapping
                            {name : Name of the entity this mapping is for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Mapping (Doctrine)';

    /**
     * The class type.
     *
     * @var string
     */
    protected $type = 'mapping';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/mapping.stub';
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
     * Make some mapping-specific changes to the path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $entityFolder = config('doctrine-make.folderNames.entity');
        $mappingFolder = config('doctrine-make.folderNames.mapping');

        // first we need to change the name - we need to embed the namespace in it
        // App\Entities\Testy\Test -> App.Entities.Testy.Test
        $className = str_replace('\\', '.', $name);

        // now replace the actual className
        // App\Entities\Testy\Test -> App\Entities\App.Entities.Testy.Test
        $name = Str::replaceLast(Str::afterLast($name, '\\' . $entityFolder . '\\'), $className, $name);

        // now get the path from the parent
        $path = parent::getPath($name);

        // we need to make some changes to the path to accomodate the mapping!
        $path = Str::replaceLast('.php', '.dcm.yml', $path);
        $path = Str::replaceFirst('/' . $entityFolder . '/', '/' . $mappingFolder . '/', $path);

        return $path;
    }

    /**
     * Replace the mapping spefific parts.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceSpecific($stub, $name)
    {
        $namspacedDummmyMapping = str_replace('\\', '.', $name);

        $dummyTable = str_replace('\\', '', Str::snake(str_replace($this->getNamespace($name) . '\\', '', $name)));

        $stub = str_replace(
            ['NamspacedDummmyMapping', 'dummyTable'],
            [$namspacedDummmyMapping, $dummyTable],
            $stub
        );

        if (strpos($this->getCleanName(), '\\') !== false) {
            $this->warn('You may want to change the table name in the mapping!');
            $this->warn('The generated table name is <fg=black;bg=white;>' . $dummyTable . '</>');
        }

        return $stub;
    }
}
