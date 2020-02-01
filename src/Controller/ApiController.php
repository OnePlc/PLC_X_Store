<?php
/**
 * ApiController.php - Store Api Controller
 *
 * Main Controller for Translation Api
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
use OnePlace\Article\Model\ArticleTable;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Adapter\AdapterInterface;
use Zend\I18n\Translator\Translator;

class ApiController extends CoreController {
    /**
     * Translation Table Object
     *
     * @since 1.0.0
     */
    private $oTableGateway;

    /**
     * ApiController constructor.
     *
     * @param AdapterInterface $oDbAdapter
     * @param TranslationTable $oTableGateway
     * @since 1.0.0
     */
    public function __construct(AdapterInterface $oDbAdapter,ArticleTable $oTableGateway,$oServiceManager) {
        parent::__construct($oDbAdapter,$oTableGateway,$oServiceManager);
        $this->oTableGateway = $oTableGateway;
        $this->sSingleForm = 'store-single';
    }

    /**
     * API Home - Main Index
     *
     * @return bool - no View File
     * @since 1.0.0
     */
    public function indexAction() {
        $this->layout('layout/json');

        $aReturn = ['state'=>'success','message'=>'Welcome to onePlace Store API'];
        echo json_encode($aReturn);

        return false;
    }

    /**
     * List all Entities of Translations
     *
     * @return bool - no View File
     * @since 1.0.0
     */
    public function listAction() {
        $this->layout('layout/json');

        $sSystemKey = $_REQUEST['systemkey'];

        # get list label from query
        $sLang = 'en_US';
        if(isset($_REQUEST['lang'])) {
            $sLang = $_REQUEST['lang'];
        }

        // translating system
        $translator = new Translator();
        $aLangs = ['en_US','de_DE'];
        foreach($aLangs as $sLoadLang) {
            if(file_exists(__DIR__.'/../../../oneplace-translation/language/'.$sLoadLang.'.mo')) {
                $translator->addTranslationFile('gettext', __DIR__.'/../../../oneplace-translation/language/'.$sLang.'.mo', 'skeleton', $sLoadLang);
            }
        }

        $translator->setLocale($sLang);

        try {
            $oInstanceTbl = CoreController::$oServiceManager->get(\OnePlace\Instance\Model\InstanceTable::class);
        } catch(\RuntimeException $e) {
            echo 'could not load instances';
            return false;
        }

        try {
            $oInstance = $oInstanceTbl->getSingle($sSystemKey,'instance_apikey');
        } catch(\RuntimeException $e) {
            echo 'could not find your instance sorry';
            return false;
        }

        try {
            $oTag = CoreController::$aCoreTables['core-tag']->select(['tag_key'=>'category']);
            $oTag = $oTag->current();
        } catch(\RuntimeException $e) {
            echo 'could not load tag';
            return false;
        }

        try {
            $oEntityTag = CoreController::$aCoreTables['core-entity-tag']->select(['tag_idfs'=>$oTag->Tag_ID,'entity_form_idfs'=>'article-single','tag_value'=>'Store Item']);
            $oEntityTag = $oEntityTag->current();
        } catch(\RuntimeException $e) {
            echo 'could not load entity tag';
            return false;
        }

        try {
            $oArticleTbl = CoreController::$oServiceManager->get(\OnePlace\Article\Model\ArticleTable::class);
            $oItemsDB = $oArticleTbl->fetchAll(false,['multi_tag'=>$oEntityTag->Entitytag_ID]);
        } catch(\RuntimeException $e) {
            echo 'could not load article';
            return false;
        }

        try {
            $aFields = $this->getFormFields('article-single');
            $oItemsDB = $oArticleTbl->fetchAll(false,['multi_tag'=>$oEntityTag->Entitytag_ID]);
        } catch(\RuntimeException $e) {
            echo 'could not load article';
            return false;
        }

        $aItems = [];

        if(count($oItemsDB) > 0) {
            foreach($oItemsDB as $oItem) {
                $aPublicItem = ['id'=>$oItem->getID()];
                # add all fields to item
                foreach($aFields as $oField) {
                    switch($oField->type) {
                        case 'multiselect':
                            # get selected
                            $oTags = $oItem->getMultiSelectField($oField->fieldkey);
                            $aTags = [];
                            foreach($oTags as $oTag) {
                                $aTags[] = ['id'=>$oTag->id,'label'=>$translator->translate($oTag->text,'skeleton',$sLang)];
                            }
                            $aPublicItem[$oField->fieldkey] = $aTags;
                            break;
                        case 'select':
                            # get selected
                            $oTag = $oItem->getSelectField($oField->fieldkey);
                            if($oTag) {
                                if (property_exists($oTag, 'tag_value')) {
                                    $aPublicItem[$oField->fieldkey] = ['id' => $oTag->id, 'label' => $translator->translate($oTag->tag_value,'skeleton',$sLang)];
                                } else {
                                    $aPublicItem[$oField->fieldkey] = ['id' => $oTag->getID(), 'label' => $translator->translate($oTag->getLabel(),'skeleton',$sLang)];
                                }
                            }
                            break;
                        case 'text':
                        case 'date':
                        case 'textarea':
                        case 'currency':
                            $aPublicItem[$oField->fieldkey] = $translator->translate($oItem->getTextField($oField->fieldkey),'skeleton',$sLang);
                            break;
                        default:
                            break;
                    }
                }
                $aItems[] = $aPublicItem;
            }
        }

        $aReturn = ['state'=>'success','message'=>'welcome '.$oInstance->getLabel(),'category'=>$oEntityTag->tag_value,'items'=>$aItems];
        echo json_encode($aReturn);

        return false;
    }
}
