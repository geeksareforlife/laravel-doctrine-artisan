# Laravel Doctrine Artisan Make Commands

This package adds functionality for Doctrine to the standard Laravel `make` Artisan commands.

It is capable of creating basic files for:

- Entities
- Mappings (Yaml)
- Factories
- Repository interfaces
- Repository implementations

As well as a single command that can create all of the above related files for an entity.

## Installation

Installation is via [Composer](https://getcomposer.org/):

```
composer require geeksareforlife/laravel-doctrine-artisan --dev 
```

### Compatability

Version | Supported Laravel Version
------- | -------------------------
1.0 | 6.x

## Getting Started

Each of the created classes are related to an Entity - they are either the entity itself, its mapping, a factory to
create the entities or a repository to access them.

For that reason, each of the commands accepts the Entity name as the input and creates an appropriate class in a folder
in your app.  The location of those folders are configurable - see the below section.

By default, the following folders are used:

```
|- App
| |- Entities
| |- Factories
| |- Mappings
| |- Repositories
| | |- Doctrine 
```

So, to create a `Student` Entity class in the `Entities` folder, you would use:

```
php artisan make:doctrine:entity Student
```

This would create a file called `Student.php` in the `Entities` folder.

If you need to create multi-level entities, that is also possible.  Use a forward or back slash (remember to escape any 
back slashes).  Each of these is equivalent:

```
php artisan make:doctrine:entity Staff/Teacher
php artisan make:doctrine:entity Staff\\Teacher
php artisan make:doctrine:entity 'Staff\Teacher'
```

and would create a `Teacher.php` file in `Entities/Staff`.

Other available individual commands are:

- `make:doctrine:factory`
- `make:doctrine:mapping`
- `make:doctrine:repository:interface`
- `make:doctrine:repository:implementation`

Note that for Laravel's dependency injection to work properly you will need to link the repository interface with the 
implementation. There is some example code provided in the **Configuration** section below.

## Combined Command

In addition to the indivudal commands above, there is also a command that will create all classes at once.  For example, 
for our `Student` entity:

```bash
php artisan make:doctrine Student
```

This will act the same as running all five individual commands individually.

You can also exclude certain classes.  For example:

```bash
php artisan --exclude=factory make:doctrine Student
```

would make everything except a factory for the entity.

If you want to exclude more than one class, pass multiple options:

```bash
php artisan --exclude=factory --exclude=mapping make:doctrine Student
```

## Configuration

There are a number of items that are configurable. To change the configuration, you will first need to publish the default
config file. Run the following Artisan command:

```bash
php artisan vendor:publish
```

and choose the `GeeksAreForLife\Laravel\Artisan\Make\DoctrineMakeProvider` option.  This will create a `doctrine-make.php`
file in your `config` folder.

### Root Namespace

By default, the locations of your files will be underneath the Laravel root namespace, which is normally `App`.

If you have changed the name of your Laravel root namespace, you can update the `rootNamespace` item in the config file.

If you have decided to use a different root namespace for your app, you can update the `rootNamespace` item to your
namespace and the `atAppLevel` item to `false`. This assumes the folder for the top level of your root namespace is located within the Laravel `app` directory.

### Import Sort

You can set `importSort` to either `alpha` or `length`, depending on how you would like the import (use) statements in
the generated classes to be sorted.

### Interface Parents

As our repository implementations inherit funtionality from the Doctrine `EntityRepository`, you will want to define the 
signatures of these standard functions in your interfaces.

The easiest eay to do this is to have your interface extend one or more parent interfaces.

Once you have created these, you can list them (with full namespaces) in the `interfaceParents` item and they will be built into your interface.

### Folders

The `folderNames` array contains the names of each of the folders for the various class types. You will notice that 
repositories is shared by both the `repository` (interface) and `implementation` items. The location of the implementation
file is affected by the next configuration item.

### Implementation Location

Implementations will be stored in a `Doctrine` folder underneath your repositories folder. However, you have two choices 
about how this happens.

**Collected**

This option stores all tof the implementations in a top level `Doctrine` folder. The folder structure of the repository 
interfaces is replicated inside the `Doctrine` folder.

```
|- Repositories
| |- Doctrine
| | |- Staff
| | | |- DoctrineTeacherRepository.php
| | |- DoctrineStudentRepository.php
| |- Staff
| | |- TeacherRepository.php
| |- StudentRepository.php
```

**Nested**

This option places a `Doctrine` folder at each level of the repositories structure, with the appropriate implementations 
in each level.

```
|- Repositories
| |- Doctrine
| | |- DoctrineStudentRepository.php
| |- Staff
| | |- Doctrine
| | | |- DoctrineTeacherRepository.php
| | |- TeacherRepository.php
| |- StudentRepository.php
```

### Repository Dependency Injection

For Laravel's dependency injection to work, it needs to know which repository implementation belongs to which interface.

If, for example, we had a `Student` entity:

```php
App\Entities\Student
```

which had the follow interface and implementation:

```php
App\Repositories\StudentRepository
App\Repositories\Doctrine\StudentRepository
```

Then you would need the follow code in a service provider's `register` function to link them:

```php
$this->app->singleton(
	App\Entities\StudentRepository::class, function ($app) {
        return new App\Repositories\Doctrine\StudentRepository($app['em'], $app['em']->getClassMetaData(App\Entities\Student::class));
    });
```