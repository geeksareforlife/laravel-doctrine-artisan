<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Root Namespace
    |--------------------------------------------------------------------------
    |
    | This allows you to have a domain specific root namespace for your
    | entities, repositories, etc
    | This could either be at the normal "app" directory, or one below
    |
    */
    'rootNamespace' => 'App',

    /*
    |--------------------------------------------------------------------------
    | Root Namespace level
    |--------------------------------------------------------------------------
    |
    | If the Root Namespace above points to the "app" folder in your
    | Laravel app, then this should be true.
    | Otherwise, if the Root Namespace points to a directory inside the
    | "app" folder then this should be false
    |
    */
    'atAppLevel' => true,

    /*
    |--------------------------------------------------------------------------
    | Import sort
    |--------------------------------------------------------------------------
    |
    | Decide how you want your class imports sorted.
    | 'alpha' - alphabetcially, Laravel's default
    | 'length' - by length
    |
    */
    'importSort' => 'alpha',

    /*
    |--------------------------------------------------------------------------
    | Interface Parent Classes
    |--------------------------------------------------------------------------
    |
    | The repository interface will extend all the classes included here.
    | This allows you to define classes that provide the interface to
    | Doctrine's standard find and save functions, if you intend to use them.
    |
    */
    'interfaceParents' => [], 

    /*
    |--------------------------------------------------------------------------
    | Folder Names
    |--------------------------------------------------------------------------
    |
    | The folder names of each of the class types
    |
    */
    'folderNames' => [
        'entity'            =>  'Entities',
        'mapping'           =>  'Mappings',
        'factory'           =>  'Factories',
        'repository'        =>  'Repositories',
        'implementation'    =>  'Repositories/Doctrine',
    ],    
];