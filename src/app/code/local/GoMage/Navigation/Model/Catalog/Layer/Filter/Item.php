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
 * @since        Class available since Release 1.0
 */
class GoMage_Navigation_Model_Catalog_Layer_Filter_Item extends Mage_Catalog_Model_Layer_Filter_Item
{
	/**
     * Get filter item url
     *
     * @return string
     */
    public function getUrl($ajax = false, $stock = false)
    {
        if ($this->hasData('url') && !$stock) {
            return $this->getData('url');
        }

        $query = array(
            $this->getFilter()->getRequestVarValue()                     => $this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $query['ajax'] = null;
		
        if ($ajax) {
            $query['ajax'] = 1;
        }

        return Mage::helper('gomage_navigation')->getFilterUrl(
			'*/*/*', 
			array(
				'_current'		=> true, 
				'_nosid'		=> true, 
				'_use_rewrite'	=> true, 
				'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(),
				'_query'		=> $query, 
				'_escape'		=> false
			)
		);
    }
	
	/**
     * Get url for remove item from filter
     *
     * @return string
     */
    public function getRemoveUrl($ajax = false)
    {
		$filter_model		= $this->getFilter();			
		$filter_request_var	= $filter_model->getRequestVarValue();
		
		$filter_reset_val	= array(
			$filter_request_var	=> $this->getFilter()->getResetValue($this->getValue()),
		);
		
		if ($filter_model->hasAttributeModel()) {
			$filter_type = $filter_model->getAttributeModel()->getFilterType();
			
			if (
				in_array(
					$filter_type,
					array(
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT,
						GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER
					)
				) &&
				!Mage::helper('gomage_navigation')->isMobileDevice()
			) {
				$filter_reset_val = array(
					$filter_request_var . '_from'	=> null, 
					$filter_request_var . '_to'		=> null
				);
			}
		}
		
		$params = array(
			'_nosid'		=> true,
			'_current'		=> true,
			'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(),
			'_use_rewrite'	=> true,
			'_query'		=> array(
				'ajax'	=> ($ajax) ? $ajax : null,
			),
			'_escape'		=> false,
			
		);
		
		$params['_query'] = array_merge($params['_query'], $filter_reset_val);
		
        return Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $params);
    }
	
	/**
     * Get url for "clear" link
     * @deprecated
     * @return false|string
     */
	public function getClearLinkUrl()
    {
        if (
			$this->getFilter()->getRequestVar() != 'cat' && 
			$this->getFilter()->getRequestVar() != 'stock_status'
		) {
			
            if ($this->getFilter()->getAttributeModel()->getFrontendInput()) {
                $attribute = $this->getFilter()->getAttributeModel();
				
                if (
					(
						in_array(
							$attribute->getFilterType(), 
							array(
								GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT,
								GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER,
								GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT,
								GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER
							)
						) && 
						!Mage::helper('gomage_navigation')->isMobileDevice()
					) ||
                    (
						$attribute->getFilterType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT &&
                        $attribute->getRangeOptions() != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::NO
					)
                ) {
                    $params = array(
						'_current'		=> true,
						'_nosid'		=> true,
                    	'_use_rewrite'	=> true,
						'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(),
                    	'_escape'		=> false,
					);

                    $url = Mage::helper('gomage_navigation')->getFilterUrl('*//*/*', $params);
					
                    $clean_url = Mage::helper('gomage_navigation')->getFilterUrl(
						'*//*/*', 
						array(
							'_current'		=> true, 
							'_nosid'		=> true, 
							'_use_rewrite'	=> true, 
							'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(),
							'_query'		=> array(), 
							'_escape'		=> false
						)
					);

                    if (strpos($clean_url, "?") !== false) {
                        $clean_url = substr($clean_url, 0, strpos($clean_url, '?'));
                    }

                    $params = str_replace($clean_url, "", $url);
                    $params = str_replace("?", "", $params);

                    $parArray    = explode("&", $params);
                    $newParArray = array();
					
                    foreach ($parArray as $par) {
                        $expar = explode("=", $par);
                       
					    if ($expar[0] != $attribute->getAttributeCode() . '_from'
                            &&
                            $expar[0] != $attribute->getAttributeCode() . '_to'
                        ) {
                            $newParArray[] = $par;
                        }
                    }

                    if ($newParArray) {
                    	return $clean_url . '?' . implode("&", $newParArray);
                    } else {
						return $clean_url;
                    }
                }
            } else {
                return parent::getClearLinkUrl();
            }
        } else {
            return parent::getClearLinkUrl();
        }
    }
	
	/*****/
	
	/*
	 * @deprecated
	 */
    public function getRemoveUrlParams()
    {
        $query                  = array($this->getFilter()->getRequestVarValue() => $this->getFilter()->getResetValue($this->getValue()));
        $params['_nosid']       = true;
        $params['_current']     = true;
		$params['_secure']		= Mage::helper('gomage_navigation')->isCurrentlySecure();
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = false;

        $url = Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', $params);

        $clean_url = Mage::helper('gomage_navigation')->getFilterUrl('*/*/*', array('_current' => true, '_nosid' => true, '_use_rewrite' => true, '_query' => array(), '_escape' => false));

        if (strpos($clean_url, "?") !== false) {
            $clean_url = substr($clean_url, 0, strpos($clean_url, '?'));
        }

        $params = str_replace($clean_url, "", $url);

        if ($this->getFilter()->getRequestVarValue() == 'price') {
            $attribute = Mage::helper('gomage_navigation')->getProductAttribute('price');

            if (
				(
					$attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY ||
                    $attribute->getRangeOptions() == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO
				) &&
                $attribute->getFilterType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT
            ) {
                $params			= str_replace("?", "", $params);
                $parArray		= explode("&", $params);
                $newParArray	= array();

                foreach ($parArray as $par) {
                    $expar = explode("=", $par);
					
                    if ($expar[0] == 'price_from') {
                        $from_to = explode(',', $this->getFromTo());
                        
						if (count($from_to) == 2) {
                            $_par = explode(',', urldecode($expar[1]));
                            $key  = array_search($from_to[0], $_par);
                            
							if ($key !== false) {
                                unset($_par[$key]);
                            }
                            
							if (count($_par)) {
                                $newParArray[] = 'price_from=' . implode(',', $_par);
                            }
                        } else {
                            $newParArray[] = $par;
                        }

                    } elseif ($expar[0] == 'price_to') {
                        $from_to = explode(',', $this->getFromTo());
                        
						if (count($from_to) == 2) {
                            $_par = explode(',', urldecode($expar[1]));
                            $key  = array_search($from_to[1], $_par);
                            
							if ($key !== false) {
                                unset($_par[$key]);
                            }
                            
							if (count($_par)) {
                                $newParArray[] = 'price_to=' . implode(',', $_par);
                            }
                        } else {
                            $newParArray[] = $par;
                        }
                    } else {
                        $newParArray[] = $par;
                    }
                }

                return '?' . implode("&", $newParArray);
            }
        }

        return $params;
    }
	
	/*
	 * @deprecated
	 */
    public function getCleanUrl($type = false)
    {
        $url = Mage::helper('gomage_navigation')->getFilterUrl(
			'*/*/*', 
			array(
				'_current'		=> true, 
				'_nosid'		=> true, 
				'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(),
				'_use_rewrite'	=> true, 
				'_query'		=> array(), 
				'_escape'		=> false
			)
		);

        if (strpos($url, "?") !== false) {
            return substr($url, 0, strpos($url, '?'));
        }

        return $url;
    }
	
	/*
	 * @deprecated
	 */
    public function getUrlParams($stock = false)
    {
        $query = array(
            $this->getFilter()->getRequestVarValue() => $this->getValue(),
            Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
        );

        $url       = Mage::helper('gomage_navigation')->getFilterUrl(
			'*/*/*',
			 array(
			 	'_current'		=> true, 
				'_nosid'		=> true,
				'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(), 
				'_use_rewrite'	=> true, 
				'_query'		=> $query, 
				'_escape'		=> false
			)
		);
        $clean_url = Mage::helper('gomage_navigation')->getFilterUrl(
			'*/*/*',
			array(
				'_current'		=> true, 
				'_nosid'		=> true,
				'_secure'		=> Mage::helper('gomage_navigation')->isCurrentlySecure(), 
				'_use_rewrite'	=> true, 
				'_query'		=> array(), 
				'_escape'		=> false
			)
		);

        if (strpos($clean_url, "?") !== false) {
            $clean_url = substr($clean_url, 0, strpos($clean_url, '?'));
        }

        return str_replace($clean_url, "", $url);
    }
}
