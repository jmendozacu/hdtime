<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 7/10/14
 * Time: 10:25 AM
 */ 
class Komplizierte_ManaCategory_Model_Filters_Filter_Category extends Mana_Filters_Model_Filter_Category {
    protected function _getItemsData()
    {
        $key = $this->getLayer()->getStateKey().'_SUBCATEGORIES';
        $data = $this->getLayer()->getAggregator()->getCacheData($key);

        if ($data === null) {
            /* @var $query Mana_Filters_Model_Query */
            $query = $this->getQuery();
            $counts = $query->getFilterCounts($this->getFilterOptions()->getCode());
            if (count($counts)<1) $counts=$query->getLayer()->getCurrentCategory()->getParentCategory()->getChildrenCategories();
            $data = $this->_getCategoryItemsData($counts);
            $tags = $this->getLayer()->getStateTags();
            $this->getLayer()->getAggregator()->saveCacheData($data, $key, $tags);
        }
        return $data;
    }

}