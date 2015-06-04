<?php

/**
 * Algolia search observer model
 */
class Algolia_Algoliasearch_Model_Observer
{
    /**
     * On Algolia admin panel saved
     */
    public function configSaved(Varien_Event_Observer $observer)
    {
        foreach (Mage::app()->getStores() as $store) /** @var $store Mage_Core_Model_Store */
            if ($store->getIsActive())
                Mage::helper('algoliasearch')->setIndexSettings($store->getId());
    }

    public function useAlgoliaSearchPopup(Varien_Event_Observer $observer)
    {
        if (Mage::helper('algoliasearch')->isPopupEnabled() || Mage::helper('algoliasearch')->isInstantEnabled()) {
            $observer->getLayout()->getUpdate()->addHandle('algolia_search_handle');
        }
        return $this;
    }


    /** Index all category product from cron job */
    public function rebuildCategoryIndex(Varien_Object $event)
    {
        $storeId = $event->getStoreId();

        if ($storeId == null)
            return;

        Mage::helper('algoliasearch')->rebuildStoreCategoryIndex($storeId, null);

        return $this;
    }

    /** Index all store product from cron job */
    public function rebuildProductIndex(Varien_Object $event)
    {
        $storeId = $event->getStoreId();

        if ($storeId == null)
            return;

        Mage::helper('algoliasearch')->rebuildStoreProductIndex($storeId, null);

        return $this;
    }


    /**
     * Inject Js
     */
    public function prepareLayoutBefore(Varien_Event_Observer $observer)
    {
        /* @var $block Mage_Page_Block_Html_Head */
        $block = $observer->getEvent()->getBlock();

        if ("head" == $block->getNameInLayout() && Mage::getDesign()->getArea() != 'adminhtml') {
            $block->addJs('../skin/frontend/base/default/algoliasearch/jquery.min.js');
            $block->addJs('../skin/frontend/base/default/algoliasearch/jquery-ui.js');
            $block->addJs('../skin/frontend/base/default/algoliasearch/typeahead.min.js');
            $block->addJs('../skin/frontend/base/default/algoliasearch/jquery.noconflict.js');
            $block->addCss('algoliasearch/jquery-ui.min.css');
        }

        return $this;
    }

    /**
     * Intercept search page and redirect to Instant search if enabled
     */
    public function controllerFrontInitBefore(Varien_Event_Observer $observer)
    {
        if (Mage::helper('algoliasearch')->replaceCategories() == false)
            return;
        if (Mage::helper('algoliasearch')->isInstantEnabled() == false)
            return;

        if (Mage::app()->getRequest()->getControllerName() == 'category' && Mage::app()->getRequest()->getParam('category') == null)
        {
            $category = Mage::registry('current_category');

            $category->getUrlInstance()->setStore(Mage::app()->getStore()->getStoreId());

            $path = '';

            foreach ($category->getPathIds() as $treeCategoryId) {
                if ($path != '') {
                    $path .= ' /// ';
                }

                $path .= Mage::helper('algoliasearch')->getCategoryName($treeCategoryId, Mage::app()->getStore()->getStoreId());
            }

            $indexName = Mage::helper('algoliasearch')->getIndexName(Mage::app()->getStore()->getStoreId()).'_products';

            $url = Mage::app()->getRequest()->getOriginalPathInfo().'?category=1#q=&page=0&refinements=%5B%7B%22categories%22%3A%22'.$path.'%22%7D%5D&numerics_refinements=%7B%7D&index_name=%22'.$indexName.'%22';

            header('Location: '.$url);

            die();
        }
    }
}
