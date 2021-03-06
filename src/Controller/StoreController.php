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
    public function __construct($oDbAdapter) {
        parent::__construct($oDbAdapter,false,CoreController::$oServiceManager);
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

        # Set Links for Breadcrumb
        $this->layout()->aNavLinks = [
            (object)['label'=>'Store'],
        ];

        if(!isset(CoreController::$aGlobalSettings['store-server-url'])) {
            return new ViewModel([
                'sError'=>'Store not connected',
            ]);
        }

        $sStoreUrl = CoreController::$aGlobalSettings['store-server-url'];
        $sStoreApiKey = CoreController::$aGlobalSettings['store-server-apikey'];

        try {
            $sApiURL = CoreController::$aGlobalSettings['store-server-url'] . '/store/api/list/0';
            $sApiURL .= '?authkey='.CoreController::$aGlobalSettings['store-server-apikey'];
            $sApiURL .= '&authtoken='.CoreController::$aGlobalSettings['store-server-apitoken'];
            $sApiURL .= '&systemkey=' . CoreController::$aGlobalSettings['store-server-apikey'];
            $sAnswer = file_get_contents($sApiURL);
        } catch(\RuntimeException $e) {
            return new ViewModel([
                'sError'=>'Could not connect to store',
            ]);
        }
        if(!$sAnswer) {
            return new ViewModel([
                'sError'=>'Could not connect to store',
            ]);
        }

        $oResponse = json_decode($sAnswer);

        $aItems = [];

        if(is_object($oResponse)) {
            if($oResponse->state != 'success') {
                return new ViewModel([
                    'sError'=>'could not load list',
                ]);
            }
        }

        $aItems = $oResponse->items;

        $iInstanceID = CoreController::$oSession->aLicences['info-instance-id'];
        $sLicApiURL = CoreController::$aGlobalSettings['license-server-url'].'/license/api/list/'.$iInstanceID.'?authkey='.CoreController::$aGlobalSettings['license-server-apikey'].'&authtoken='.CoreController::$aGlobalSettings['license-server-apitoken'].'&modulename=all&systemkey='.CoreController::$aGlobalSettings['license-server-apikey'];
        $sLicAnswer = file_get_contents($sLicApiURL);

        $aArticlesIHave = [];

        $oLicResponse = json_decode($sLicAnswer);
        if(count($oLicResponse->items) > 0) {
            foreach($oLicResponse->items as $oLic) {
                $aArticlesIHave[$oLic->article_idfs] = true;
            }
        }

        return new ViewModel([
            'sStoreUrl'=>$sStoreUrl,
            'aItems'=>$aItems,
            'aArticlesIHave' => $aArticlesIHave,
        ]);
    }

    /**
     * Store - Buy
     *
     * @return ViewModel
     * @since 1.0.0
     */
    public function buyAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('store');

        $oRequest = $this->getRequest();

        if(!$oRequest->isPost()) {
            $iStoreItemID = $this->params()->fromRoute('id', 0);

            try {
                $sApiURL = CoreController::$aGlobalSettings['store-server-url'] . '/article/api/get/'.$iStoreItemID;
                $sApiURL .= '?authkey='.CoreController::$aGlobalSettings['store-server-apikey'];
                $sApiURL .= '&authtoken='.CoreController::$aGlobalSettings['store-server-apitoken'];
                $sApiURL .= '&systemkey=' . CoreController::$aGlobalSettings['store-server-apikey'];
                $sAnswer = file_get_contents($sApiURL);
            } catch(\RuntimeException $e) {
                return new ViewModel([
                    'sError'=>'Could not connect to store',
                ]);
            }

            $oResponse = json_decode($sAnswer);
            if(!is_object($oResponse)) {
                return new ViewModel([
                    'sError'=>'error loading store data',
                ]);
            }

            return new ViewModel([
                'oStoreItem'=>$oResponse->oItem,
            ]);
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

    /**
     * List articles of a certain category
     *
     * @return ViewModel
     * @since 1.0.0
     */
    public function listAction() {
        # Set Layout based on users theme
        $this->setThemeBasedLayout('store');

        $sStoreCategory = $this->params()->fromRoute('id','all');

        return new ViewModel([
            'sStoreCategory'=>$sStoreCategory,
        ]);
    }
}
