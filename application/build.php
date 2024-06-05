<?php
/**
 * Created by PhpStorm.
 * User: Ganzi
 * Date: 2020-02-27
 * Time: 13:06
 */

// php think build

//return [
//    // 生成应用公共文件
//    '__file__' => ['common.php', 'config.php', 'database.php'],
//
//    // 定义demo模块的自动生成 （按照实际定义的文件名生成）
//    'demo'     => [
//        '__file__'   => ['common.php'],
//        '__dir__'    => ['behavior', 'controller', 'model', 'view'],
//        'controller' => ['Index', 'Test', 'UserType'],
//        'model'      => ['User', 'UserType'],
//        'view'       => ['index/index'],
//    ],
//    // 其他更多的模块定义
//];

return [
    '__file__' => [],
    'Home' => [
        '__file__' => [],
        '__dir__' => ['controller','model','view'],
        'controller' => ['index'],
        'model' => [],
        'view' => ['index/index'],
    ],
    'Manage' => [
        '__file__' => [],
        '__dir__' => ['controller','model','view'],
        'controller' => ['index'],
        'model' => [],
        'view' => ['index/index'],
    ],
];