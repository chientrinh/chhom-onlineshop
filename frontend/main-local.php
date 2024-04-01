<?php
/**
 * $URL: https://tarax.toyouke.com/svn/MALL/frontend/config/main-local.php.example $
 * $Id: main-local.php.example 801 2015-03-14 07:10:44Z mori $
 */

$config = [
    'components' => [
        'db' => [
            'password' => '9QEkmT82'
        ],
        'ysd' => [
            'password' => '9QEkmT82'
        ],
        'request' => [
            'cookieValidationKey' => 'ioIDQe829sm6GfZthMjufWd3ah9zEfPZiN3QLxYnT',
        ],
    ],
    'modules' => [
        'profile' => [
            'components' => [
                'ysdConnection' => [
                    'password' => '6ffdb60c',
                ],
            ],
        ],
    ],
];

return $config;
