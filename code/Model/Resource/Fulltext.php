<?php

class Algolia_Algoliasearch_Model_Resource_Fulltext extends Mage_CatalogSearch_Model_Resource_Fulltext
{
    protected function _saveProductIndexes($storeId, $productIndexes)
    {
        Mage::helper('algoliasearch')->rebuildStoreProductIndex($storeId, array_keys($productIndexes), $productIndexes);

        return $this;
    }

    public function cleanEntityIndex($entity, $storeId = NULL, $entityId = NULL)
    {
        $this->_engine->cleanEntityIndex($entity, $storeId, $entityId);

        return $this;
    }

    public function rebuildCategoryIndex($storeId = NULL, $categoryIds = NULL)
    {
        $this->_engine->rebuildCategoryIndex($storeId, $categoryIds);

        return $this;
    }

    public function rebuildProductIndex($storeId = NULL, $productIds = NULL)
    {
        $this->_engine->rebuildProductIndex($storeId, $productIds);
    }

    public function getSearchableAttributes($backendType = NULL)
    {
        return $this->_getSearchableAttributes($backendType);
    }
}