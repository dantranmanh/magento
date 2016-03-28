<?php
 /**
 * GoMage Advanced Navigation Extension
 *
 * @category     Extension
 * @copyright    Copyright (c) 2010-2015 GoMage (http://www.gomage.com)
 * @author       GoMage
 * @license      http://www.gomage.com/license-agreement/  Single domain license
 * @terms of use http://www.gomage.com/terms-of-use
 * @version      Release: 4.9
 * @since        Class available since Release 3.2
 */

class GoMage_Navigation_Model_Resource_Eav_Mysql4_Layer_Filter_Stock extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
{
	
	public function prepareSelect($filter, $value, $select){

		$val = (int)$value[0];
		
        $table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');

        $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);  
        if($val == GoMage_Navigation_Model_Catalog_Layer_Filter_Stock::IN_STOCK)
        {
            $cond = array( 
                "{$table}.use_config_manage_stock = 0 AND {$table}.manage_stock=1 AND {$table}.is_in_stock=1",
                "{$table}.use_config_manage_stock = 0 AND {$table}.manage_stock=0",
            );

            if ($manageStock) {
                $cond[] = "{$table}.use_config_manage_stock = 1 AND {$table}.is_in_stock=1";
            } else {
                $cond[] = "{$table}.use_config_manage_stock = 1";
            }
            $select->where("{$table}.product_id=e.entity_id");
            $select->join(  
                array($table => Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item')),
                '(' . join(') OR (', $cond) . ')',
                array("inventory_in_stock_qty"=>"qty")
            );
                
        }
        elseif($val == GoMage_Navigation_Model_Catalog_Layer_Filter_Stock::OUT_OF_STOCK)
        {
            $cond = array(
                "{$table}.use_config_manage_stock = 0 AND {$table}.manage_stock=1 AND {$table}.is_in_stock=0",
                "{$table}.use_config_manage_stock = 0 AND {$table}.manage_stock=0",
            );

            if ($manageStock) {
                $cond[] = "{$table}.use_config_manage_stock = 1 AND {$table}.is_in_stock=0";
            } else {
                $cond[] = "{$table}.use_config_manage_stock = 1";
            }

            $select->where("{$table}.product_id=e.entity_id");
            $select->join(  
                array($table => Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item')),
                '(' . join(') OR (', $cond) . ')',
                array("inventory_in_stock_qty"=>"qty")
                
            ); 
        } 
        
        return $this;     
	}

     
     /**
     * Apply attribute filter to product collection
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @param int $value
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Attribute
     */
     
     
    public function applyFilterToCollection($filter, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();
        
        $this->prepareSelect($filter, $value, $collection->getSelect());
        
        $base_select = $filter->getLayer()->getBaseSelect();
        
        foreach($base_select as $code=>$select){
        	
        	if('stock_status' != $code){
        	
        		$this->prepareSelect($filter, $value, $select);
        	
        	}
        }
        
        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param Mage_Catalog_Model_Layer_Filter_Attribute $filter
     * @return array
     */
    public function getCount($filter)
    {
    	$connection = $this->_getReadAdapter();

        $table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_status');
        $tableAlias = 'gan_stock_status';

		$base_select = $filter->getLayer()->getBaseSelect();
		        
        if(isset($base_select['stock_status'])){
        	
        	$select = $base_select['stock_status'];        	
        
        }else{
        	$select = clone $filter->getLayer()->getProductCollection()->getSelect();
        	
        }

        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::GROUP);

        $conditions = array(
            "{$tableAlias}.product_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.website_id = ?",  Mage::app()->getWebsite()->getId()),
        );

        $select
            ->join(
                array($tableAlias => $table),
                join(' AND ', $conditions),
                array("{$tableAlias}.stock_status", 'count' => "COUNT({$tableAlias}.stock_status)"))
            ->group("{$tableAlias}.stock_status");

        $result = $connection->fetchPairs($select);

        $stockCount = array('instock' => 0, 'outofstock' => 0);

        if (isset($result[Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK])){
            $stockCount['instock'] = $result[Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK];
        }

        if (isset($result[Mage_CatalogInventory_Model_Stock_Status::STATUS_OUT_OF_STOCK])){
            $stockCount['outofstock'] = $result[Mage_CatalogInventory_Model_Stock_Status::STATUS_OUT_OF_STOCK];
        }

        return $stockCount;
    }
}
