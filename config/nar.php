<?php
/*
|--------------------------------------------------------------------------
| Nar Global Config
|--------------------------------------------------------------------------
|
|
*/
return [
    /*
    |--------------------------------------------------------------------------
    | Generator Config
    |--------------------------------------------------------------------------
    |
    */
    'generator' => [
        'basePath'           => base_path('/src'),
        'rootNamespace'      => 'Laraplate\\',
        'entitiesFolderName' => 'Entities',
        'stubsOverridePath'  => app_path('Console/Laraplate'),
        'paths'              => [
            'models'       => 'Models',
            'repositories' => 'Repositories',
            'contracts'    => 'Contracts',
            'transformers' => 'Transformers',
            'presenters'   => 'Presenters',
            'validators'   => 'Validators',
            'controllers'  => 'Controllers',
            'provider'     => 'Providers',
            'criteria'     => 'Criteria',
            'routes'       => 'Routes',
            'services'     => 'Services',
        ]
    ]
];
