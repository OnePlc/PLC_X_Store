<?php
/**
 * module.config.php - Store Config
 *
 * Main Config File for Store Module
 *
 * @category Config
 * @package Store
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

namespace OnePlace\Store;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    # Store Module - Routes
    'router' => [
        'routes' => [
            # Module Basic Route
            'store' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/store[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[a-zA-Z0-9-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\StoreController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'store-api' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/store/api[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    # View Settings
    'view_manager' => [
        'template_path_stack' => [
            'store' => __DIR__ . '/../view',
        ],
    ],
];
