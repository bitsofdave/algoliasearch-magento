<?php

class Algolia_Algoliasearch_Model_Resource_Fulltext_Collection extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{

    /**
     * Intercept query in case instant search is disabled
     */
    public function addSearchFilter($query)
    {
        $data = Mage::helper('algoliasearch')->getSearchResult($query, Mage::app()->getStore()->getId());

        $sortedIds = array_reverse(array_keys($data));
        $this->getSelect()->columns(array('relevance' => new Zend_Db_Expr("FIND_IN_SET(e.entity_id, '".implode(',',$sortedIds)."')")));
        $this->getSelect()->where('e.entity_id IN (?)', $sortedIds);

        return $this;
    }

}
