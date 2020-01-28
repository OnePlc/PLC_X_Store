<?php
/**
 * StoreController.php - Store Main Controller
 *
 * Main Controller for Store
 *
 * @category Controller
 * @package Store
 * @author Verein onePlace
 * @copyright (C) 2020  Verein onePlace <admin@1plc.ch>
 * @license https://opensource.org/licenses/BSD-3-Clause
 * @version 1.0.0
 * @since 1.0.0
 */

declare(strict_types=1);

namespace OnePlace\Store\Controller;

use Application\Controller\CoreController;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\AdapterInterface;

class StoreController extends CoreController {
    /**
     * Skeletonrequest Table Object
     *
     * @since 1.0.0
     */
    private $oTableGateway;

    /**
     * StoreController constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
    }

    /**
     * Store - Index
     *
     * @return ViewModel
     * @since 1.0.0
     */
    public function indexAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('store');

        return new ViewModel([]);
    }
}
