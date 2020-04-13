<?php

namespace GeeksAreForLife\Laravel\Artisan\Make;

use Illuminate\Console\Command;

class MakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:doctrine
                            {name : Name of the entity to create}
                            {--exclude=* : Types not to create (entity, mapping, repository, factory)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Entity/Mapping/Interface/Implementation/Factory all in one go.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = trim($this->argument('name'));

        $exclude = array_map('strtolower', $this->option('exclude'));

        if (! in_array('entity', $exclude)) {
            $this->info('Creating Entity');
            $this->call('make:doctrine:entity', [
                'name' => "$name",
            ]);
        }

        if (! in_array('mapping', $exclude)) {
            $this->info('Creating Mapping');
            $this->call('make:doctrine:mapping', [
                'name' => "$name",
            ]);
        }

        if (! in_array('repository', $exclude)) {
            $this->info('Creating Repository Interface');
            $this->call('make:doctrine:repository:interface', [
                'name' => "$name",
            ]);

            $this->info('Creating Repository Implementation');
            $this->call('make:doctrine:repository:implementation', [
                'name' => "$name",
            ]);
        }

        if (! in_array('factory', $exclude)) {
            $this->info('Creating Factory');
            $this->call('make:doctrine:factory', [
                'name' => "$name",
            ]);
        }
    }
}
