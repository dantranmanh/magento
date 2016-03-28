<?php
class Citybeach_Omnivore_Model_Product_Api extends Mage_Catalog_Model_Product_Api
{
    /**
     * Retrieve product info and all associated simple products/variants
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($productId, $store = null, $attributes = null, $identifierType = null)
    {
    	//mage::log(__FILE__);
        //error_log('Citybeach_Omnivore_Model_Product_Api->info() productId = ' . $productId);
    	
        $result = parent::info ( $productId, $store, $attributes, $identifierType );
        
        if ($result ['type'] == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
        
        	$product = Mage::getModel ( 'catalog/product' )->load ( $result ['product_id'] );
        
        	if ($product->isConfigurable ()) {
        		$children = $product->getTypeInstance ( true )->getUsedProductIds ( $product );
        		$result['associated_ids'] = $children;
        	}
        }
        
        
        return $result;
    }
}
?> 
