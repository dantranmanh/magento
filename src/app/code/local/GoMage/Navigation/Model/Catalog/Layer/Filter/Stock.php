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
class GoMage_Navigation_Model_Catalog_Layer_Filter_Stock extends Mage_Catalog_Model_Layer_Filter_Abstract
{
	protected $_resource;
   
    const IN_STOCK     = 1;
    const OUT_OF_STOCK = 2;

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
		
        $this->_requestVar = 'stock_status';
    }
	
	/**
     * Initialize filter items
     *
     * @return  Mage_Catalog_Model_Layer_Filter_Abstract
     */
    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        
		foreach ($data as $itemData) {
            $items[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active']
            );
        }
       
	    $this->_items = $items;
        
		return $this;
    }

    /**
     * Create filter item object
     *
     * @param string $label
     * @param mixed $value
     * @param int $count
     * @param bool $status
     * @param string $image
     * @param int $level
     * @param string $haschild
     * @param string $from_to
     * @return Mage_Catalog_Model_Layer_Filter_Item
     */
    protected function _createItem($label, $value, $count = 0, $status = false)
    {
        return Mage::getModel('gomage_navigation/catalog_layer_filter_item')
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count)
            ->setActive($status);
    }
    
    /**
     * Get filter value for reset current filter state
     *
     * @param null $value_to_remove
     * @return mixed|null|string
     */
    public function getResetValue($value_to_remove = null)
    {

        if ($value_to_remove && ($current_value = Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar))) {
            $current_value = explode(',', $current_value);

            if (false !== ($position = array_search($value_to_remove, $current_value))) {
                unset($current_value[$position]);

                if (!empty($current_value)) {
                    return implode(',', $current_value);
                }
            }
        }

        return null;
    }

    /**
     * Apply category filter to layer
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Mage_Core_Block_Abstract $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Category
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter		= $request->getParam($this->getRequestVarValue());
        $filters	= explode(',', $filter);

        $this->_getResource()->applyFilterToCollection($this, $filters);

        $collection = $this->getLayer()->getProductCollection();
		
        if ($filter == self::IN_STOCK) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
            $this->getLayer()->getState()->addFilter(
                $this->_createItem(Mage::helper('gomage_navigation')->__("In Stock"), array("stock_status" => $filter))
            );
        } elseif ($filter == self::OUT_OF_STOCK) {
            $manageStock = Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
            $cond        = array(
                '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=1 AND {{table}}.is_in_stock=0',
                '{{table}}.use_config_manage_stock = 0 AND {{table}}.manage_stock=0',
            );

            if ($manageStock) {
                $cond[] = '{{table}}.use_config_manage_stock = 1 AND {{table}}.is_in_stock=0';
            } else {
                $cond[] = '{{table}}.use_config_manage_stock = 1';
            }

            $collection->joinField(
                'inventory_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '(' . join(') OR (', $cond) . ')'
            );
            
			$this->getLayer()->getState()->addFilter(
                $this->_createItem(Mage::helper('gomage_navigation')->__("Out of Stock"), array("stock_status" => $filter))
            );
        }

        return $this;
    }
	
    /**
     * Get filter name
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('gomage_navigation')->__('Stock');
    }


    protected function _getItemsData()
    {
        $optionsCount	= $this->_getResource()->getCount($this);
        $value			= Mage::helper('gomage_navigation')->getRequest()->getParam($this->_requestVar);

        $data[] = array(
            'label'  => Mage::helper('gomage_navigation')->__("In Stock"),
            'value'  => (string) self::IN_STOCK,
            'count'  => isset($optionsCount['instock']) ? $optionsCount['instock'] : 0,
            'active' => ($value == self::IN_STOCK) ? true : false,
        );
		
        $data[] = array(
            'label'  => Mage::helper('gomage_navigation')->__("Out of Stock"),
            'value'  => (string) self::OUT_OF_STOCK,
            'count'  => isset($optionsCount['outofstock']) ? $optionsCount['outofstock'] : 0,
            'active' => ($value == self::OUT_OF_STOCK) ? true : false,
        );
		
        return $data;
    }

	/*****/
	
	public function hasAttributeModel()
    {   
		return $this->hasData('attribute_model');
    }	
	
	public function getRequestVarValue()
    {
        return $this->_requestVar;
    }
	
    /**
     * Retrieve resource instance
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Stock
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('gomage_navigation/resource_eav_mysql4_layer_filter_stock');
        }
		
        return $this->_resource;
    }
}
