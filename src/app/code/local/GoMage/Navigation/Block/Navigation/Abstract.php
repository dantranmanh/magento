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
 
if (!function_exists('gan_cat_sort')) {
	function gan_cat_sort($a, $b) {
		$a_col = ($a->getData('navigation_pw_s_column') ? $a->getData('navigation_pw_s_column') : 1);
		$b_col = ($b->getData('navigation_pw_s_column') ? $b->getData('navigation_pw_s_column') : 1);
	
		if ($a_col == $b_col) {
			return ($a->getData('position') < $b->getData('position')) ? -1 : 1;
		} else {
			return ($a_col < $b_col) ? -1 : 1;
		}
	}
}

if (!function_exists('gan_cat_slide_sort')) {
	function gan_cat_slide_sort($a, $b) {
		$a_col = ($a->getData('navigation_column_side') ? $a->getData('navigation_column_side') : 1);
		$b_col = ($b->getData('navigation_column_side') ? $b->getData('navigation_column_side') : 1);
	
		if ($a_col == $b_col) {
			return ($a->getData('position') < $b->getData('position')) ? -1 : 1;
		} else {
			return ($a_col < $b_col) ? -1 : 1;
		}
	}
}

abstract class GoMage_Navigation_Block_Navigation_Abstract extends Mage_Core_Block_Template {    
	
	const MENU_BAR			= 1;
	const LEFT_COLUMN		= 2;
	const RIGTH_COLUMN		= 3;
	const CONTENT_COLUMN	= 4;	
	const NAVIGATION_PLACE	= null;
	const CONFIG_KEY		= null;
	
	public $can_display		= null;
	public $navigation_type	= null;
	
	protected $GMN					= null;
	protected $config_helper		= null;
	protected $url_helper			= null;
	protected $root_level			= 0;
	protected $columns				= 0;
    protected $current_column		= 0;
	protected $childs_count			= 0;
	protected $offer_block_html		= null;
	protected $plain_root_cat		= null;
	protected $categoryInstance		= null;
	protected $itemLevelPositions	= array();
	
	abstract protected function _prePrepareLayout();
	
	protected function _construct() {
        $this->addData(array(
                'cache_lifetime' => false,
                'cache_tags'     => array(
					Mage_Catalog_Model_Category::CACHE_TAG, 
					Mage_Core_Model_Store_Group::CACHE_TAG
				),
            )
        );
    }	
	
	protected function _prepareLayout() {
		$this->_prePrepareLayout();
        parent::_prepareLayout();
		
		if ($this->isGMN()) {
            if ($head_block = $this->getLayout()->getBlock('head')) {
                $head_block->addCss('css/gomage/advanced-navigation.css');
            }
        }
    }
	
	public function isActive() {	
		return (bool) Mage::getStoreConfig($this->configKey() . '/active');
	}
	
	public function navigationType() {
        if ($this->navigation_type === null) {
			$this->navigation_type = Mage::getStoreConfig($this->configKey() . '/filter_type');
        }

        return $this->navigation_type;
    }
	
	public function inblockType() {      
		return Mage::getStoreConfig($this->configKey() . '/inblock_type');
    }
	
	public function inblockHeight() {
		return (int) Mage::getStoreConfig($this->configKey() . '/inblock_height');
    }
	
	public function maxInblockHeight() {        
		return (int) Mage::getStoreConfig($this->configKey() . '/max_inblock_height');
    }
	
	public function isAjax() {
		if ($this->navigationType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_PRO) {
            return false;
        }
		
		return (bool) Mage::getStoreConfig($this->configKey() . '/ajax_enabled');
	}
	
	public function showInShopBy() {
		return (bool) Mage::getStoreConfig($this->configKey() . '/show_shopby');
	}
	
	public function hideEmptyCategory() {
		return (bool) Mage::getStoreConfig($this->configKey() . '/hide_empty');
	}
	
	public function showAllSubcategories() {
		return (bool) Mage::getStoreConfigFlag($this->configKey() . '/show_allsubcats'); 
    }
	
	public function columnColor() {
       return Mage::helper('gomage_navigation')->formatColor(Mage::getStoreConfig($this->configKey() . '/column_color'));
	}
	
	public function canShowMinimized() {
		$helper = Mage::helper('gomage_navigation');
		     
		if ($helper->getRequest()->getParam('content-category_is_open') === 'true') {
			return false;
		} elseif ($helper->getRequest()->getParam('content-category_is_open' === 'false')) {
			return true;
		}
		
		return (bool) Mage::getStoreConfigFlag($this->configKey() . '/show_minimized');
    }
	
	public function canShowCheckbox() {	
		return (bool) Mage::getStoreConfigFlag($this->configKey() . '/show_checkbox');
	}
	
	public function canShowImageName() {
		return (bool) Mage::getStoreConfigFlag($this->configKey() . '/show_image_name');
    }
	
	public function imageAlign() {
        $image_align = null;
      
		switch (Mage::getStoreConfig($this->configKey() . '/image_align')) {	
			case (1) :
				$image_align = 'horizontally';
			break;
			
			case (2) :
				$image_align = '2-columns';
			break;
			
			default :
				$image_align = 'default';
			break;
		}
		
        return $image_align;
    }
	
	public function imageWidth() {   
		return (int) Mage::getStoreConfig($this->configKey() . '/image_width');        
    }

    public function imageHeight() {
		return (int) Mage::getStoreConfig($this->configKey() . '/image_height');
    }
	
	public function canShowPopup() {
		return (bool) Mage::getStoreConfigFlag($this->configKey() . '/show_help');
    }
	
	public function popupText() {
		return trim(Mage::getStoreConfig($this->configKey() . '/popup_text'));
    }
	
	public function popupWidth() {
		return (int) Mage::getStoreConfig($this->configKey() . '/popup_width');      
    }

    public function popupHeight() { 
		return (int) Mage::getStoreConfig($this->configKey() . '/popup_height');
    }
	
	public function filterReset() {
		return (bool) Mage::getStoreConfig($this->configKey() . '/filter_reset');
	}
	
	public function navigationPlace() {
        return static::NAVIGATION_PLACE;
    }
	
	public function configKey() {
        return static::CONFIG_KEY;
    }
	
	public function isGMN() {
		if ($this->GMN === null) {
			$this->GMN = Mage::helper('gomage_navigation')->isGomageNavigation();
		} 
        
		return $this->GMN;
    }
	
	public function configHelper() {
		if ($this->config_helper === null) {
			$this->config_helper = Mage::helper('gomage_navigation/config');
		} 
        
		return $this->config_helper;
    }
	
	public function urlHelper() {
		if ($this->url_helper === null) {
			$this->url_helper = Mage::helper('gomage_navigation/url');
		} 
        
		return $this->url_helper;
    }
	
	public function canDisplay() {
		if ($this->can_display === null) {
			$this->can_display = $this->isActive();
		}
		
        return $this->can_display;
    }
	
	public function isCMSPage() {
		return $this->configHelper()->isCMSPage();
	}
	
	public function curentCategory() {		
		return $this->configHelper()->curentCategory();
	}

	/**
     * Get url for category data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function categoryUrl($category) {
        return $this->urlHelper()->categoryUrl($category, array('_query' => array('ajax' => null)));
    }
	
    public function categoryFilterUrl($category) {
		return $this->urlHelper()->categoryFilterUrl($category, array('_query' => array('ajax' => null)));
    }
	
    public function categoryFilterIsActive($category) {
        return $this->urlHelper()->categoryFilterIsActive($category);
    }
	
	/**
     * Checkin activity of category
     *
     * @param   Varien_Object $category
     * @return  bool
     */
    public function isCategoryActive($category) {
        if ($this->curentCategory()) {
            return in_array($category->getId(), $this->curentCategory()->getPathIds());
        }
		
        return false;
    }
	
	/**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
	public function renderCategoriesMenuHtml($level = 0, $outermostItemClass = '', $childrenWrapClass = '') {	  		
		$root_category = 
			($this->navigationPlace() == self::MENU_BAR || $this->navigationType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_PRO) ? 
				Mage::app()->getStore()->getRootCategoryId() : 
					$this->curentCategory()->getId();
        
        $this->root_level = Mage::getModel('catalog/category')->load($root_category)
			->getLevel() + 1;
		
		$activeCategories = array();
		
        foreach ($this->getStoreCategories($root_category) as $child) {
            if ($child->getIsActive()) {           
				if ($this->hideEmptyCategory() && !$child->getProductCount()) {
					continue;
				}
				
                $activeCategories[] = $child;
            }
        }
		
        $activeCategoriesCount    = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }

        $html = '';
        $j    = 0;
		
        foreach ($activeCategories as $category) {
            $children  = $category->getChildren();
            $columns   = array();
            $columns[] = 1;
			
            foreach ($children as $child) {
                if ($child->getIsActive()) {
                    if ($this->navigationPlace() == self::MENU_BAR) {
                        $_column = ($child->getData('navigation_pw_s_column') ? $child->getData('navigation_pw_s_column') : 1);
                    } else {
                        $_column = ($child->getData('navigation_column_side') ? $child->getData('navigation_column_side') : 1);
                    }

                    if (!in_array($_column, $columns)) {
                        $columns[] = $_column;
                    }
                }
            }

            $this->columns          = count($columns);
			
            $this->current_column   = min($columns);
            $this->childs_count     = 0;
            $this->offer_block_html = null;

            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true
            );
            $j++;
        }

        return $html;
    }
	
	/**
     * Get catagories of current store
     *
     * @return Varien_Data_Tree_Node_Collection
     */
    public function getStoreCategories($root_category = null) {		
        $tree  = Mage::getResourceModel('catalog/category_tree');
        $nodes = $tree->loadNode($root_category)
            ->loadChildren(max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth')))
            ->getChildren();

        $collection = Mage::getResourceModel('catalog/category_collection')->setLoadProductCount(true);
        $collection->addAttributeToSelect('*');
		
        $tree->addCollectionData($collection, Mage::app()->getStore()->getId(), $root_category, true, true);

        return $nodes;
    }
	
	/**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml(
        $category, 
		$level = 0, 
		$isLast = false, 
		$isFirst = false,
        $isOutermost = false, 
		$outermostItemClass = '', 
		$childrenWrapClass = '', 
		$noEventAttributes = false
    ) {		
        $html = array();

        // get all children        
        $children      = $category->getChildren();
        $childrenCount = $children->count();

        // select active children
        $activeChildren = array();
		
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
		
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren   = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes   = array();
        $classes[] = 'level' . $level;

        if ($this->navigationPlace() == self::MENU_BAR) {
            if ($this->isCategoryActive($category)) {
                $classes[] = 'active';
            }
        }

        $linkClass = '';
		
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="' . $outermostItemClass . '"';
        }
		
        if ($isFirst) {
            $classes[] = 'first';
        }
		
        if ($isLast) {
            $classes[] = 'last';
        }
		
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }

        if ($isFirst && $this->navigationType() == GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_ACCORDION) {
            $classes[] = 'accordion-active';
        }

        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        switch ($this->navigationType()) {
            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DROPDOWN :
                if ($this->isAjax()) {
                    $attributes['onchange'] = "GomageNavigation.setNavigationUrl(this.value); return false;";
                } else {
                    $attributes['onchange'] = "window.location=this.value";
                }

                $curent_id = 0;
				
                if (Mage::registry('current_category')) {
                    $curent_id = Mage::registry('current_category')->getId();
                }

                if ($category->getLevel() == $this->root_level) {
                    $htmlSel = '<li><select';
                    
					foreach ($attributes as $attrName => $attrValue) {
                        $htmlSel .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }
					
                    $htmlSel .= '>';
                    $html[] = $htmlSel;

                    $option_value = ($this->isAjax() ? $this->categoryFilterUrl($category) : $this->categoryUrl($category));

                    $html[] = '<option class="gan-dropdown-top" value="' . $option_value . '">' . (str_repeat('&nbsp;&nbsp;', $category->getLevel() - $this->root_level) . $category->getName()) . '</option>';

                }

                $option_selected = ($curent_id == $category->getId() ? 'selected="selected"' : '');
                $option_value    = ($this->isAjax() ? $this->categoryFilterUrl($category) : $this->categoryUrl($category));
                $html[] = '<option ' . $option_selected . ' value="' . $option_value . '">' . (str_repeat('&nbsp;&nbsp;', $category->getLevel() - $this->root_level) . $category->getName()) . '</option>';

                // render children
                $htmlChildren = '';
                $j            = 0;
				
                foreach ($activeChildren as $child) {
                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
				
                if (!empty($htmlChildren)) {
                    $html[] = $htmlChildren;
                }
				
                if ($category->getLevel() == $this->root_level) {
                    $html[] = '</select></li>';
                }

            break;
			
            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_PLAIN :
                $linkClass = '';
				
                if ($isOutermost && $outermostItemClass) {
                    $linkClass = $outermostItemClass;
                }
				
                if ($this->categoryFilterIsActive($category) || $this->isCategoryActive($category)) {
                    $linkClass .= ' active';
                }

                $linkClass = ' class="' . $linkClass . '" ';

                if ($category->getLevel() == $this->root_level) {
                    $this->plain_root_cat = $category;

                    //Offer Block
                    $this->offer_block_html = null;
                    
					if ($this->navigationPlace() == self::MENU_BAR) {
                        if ($category->getData('navigation_pw_ob_show')) {
                            $offer_block_styles = '';
                            
							if ($category->getData('navigation_pw_ob_bgcolor')) {
                                $offer_block_styles .= 'background-color:' . Mage::helper('gomage_navigation')->formatColor($category->getData('navigation_pw_ob_bgcolor')) . ';';
                           
						    }
                            if ($category->getData('navigation_pw_ob_width')) {
                                $offer_block_styles .= 'width:' . $category->getData('navigation_pw_ob_width') . 'px;';
                            }
							
                            if ($category->getData('navigation_pw_ob_height')) {
                                $offer_block_styles .= 'height:' . $category->getData('navigation_pw_ob_height') . 'px;';
                            }

                            $offer_block_class = GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::getOfferBlockPositionClass($category->getData('navigation_pw_ob_pos'));

                            if (in_array($category->getData('navigation_pw_ob_pos'), array(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::LEFT, GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::RIGHT)) &&
                                !$category->getData('navigation_pw_ob_height')
                            ) {
                                $offer_block_class .= ' gan-ob-noheight';
                            }

                            $this->offer_block_html = '<div class="' . $offer_block_class . '" style="' . $offer_block_styles . '">';
                            $_desc                   = $category->getData('navigation_pw_ob_desc');
                            $_desc                   = nl2br($this->helper('cms')->getBlockTemplateProcessor()->filter($_desc));
                            $this->offer_block_html .= $_desc;
                            $this->offer_block_html .= '</div>';
                        }
                    }

                    if ($hasActiveChildren && !$noEventAttributes) {
                        $attributes['onmouseover'] = 'toggleMenu(this,1)';
                        $attributes['onmouseout']  = 'toggleMenu(this,0)';
                    }

                    if ($this->navigationPlace() == self::MENU_BAR) {
                        if (isset($attributes['class'])) {
                            $attributes['class'] = $attributes['class'] . ' nav-' . $category->getId();
                        } else {
                            $attributes['class'] = 'nav-' . $category->getId();
                        }
						
                        if ($category->getData('navigation_pw_s_template')) {
                            $attributes['class'] = $attributes['class'] . ' gan-plain-style' . $category->getData('navigation_pw_s_template');
                        }
                    }

                    $htmlLi = '<li';
                    
					foreach ($attributes as $attrName => $attrValue) {
                        $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }
					
                    $htmlLi .= '>';
                    $html[] = $htmlLi;
                    $htmlA = '<a href="' . $this->categoryUrl($category) . '"' . $linkClass;
                    
					if ($this->isAjax()) {
                        $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                    }
					
                    $htmlA .= '>';
                    $html[] = $htmlA;
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    $html[] = '</a>';

                    if ($hasActiveChildren) {
                        if ($this->navigationPlace() == self::MENU_BAR) {
                            $_width = $category->getData('navigation_pw_s_width');
                        } else {
                            $_width = $category->getData('navigation_pw_side_width');
                        }

                        $gan_plain_style = '';
						
                        if ($this->navigationPlace() == self::MENU_BAR) {
                            if ($category->getData('navigation_pw_s_bgcolor')) {
                                $gan_plain_style .= 'background-color:' . Mage::helper('gomage_navigation')->formatColor($category->getData('navigation_pw_s_bgcolor')) . ';';
                            }
							
                            if ($category->getData('navigation_pw_s_height')) {
                                $gan_plain_style .= 'height:' . $category->getData('navigation_pw_s_height') . 'px;';
                            }
							
                            if ($category->getData('navigation_pw_s_bsize') && $category->getData('navigation_pw_s_bcolor')) {
                                $gan_plain_style .= 'border:' . $category->getData('navigation_pw_s_bsize') . 'px solid ' . Mage::helper('gomage_navigation')->formatColor($category->getData('navigation_pw_s_bcolor')) . ';';
                            }
                        }
						
                        $gan_plain_style .= ($_width ? 'width:' . $_width . 'px;' : '');

                        if ($gan_plain_style) {
                            $gan_plain_style = 'style="' . $gan_plain_style . '"';
                        }

                        $gan_plain_class = 'gan-plain';
						
                        if ($this->navigationPlace() == self::MENU_BAR) {
                            $gan_plain_class .= ' nav-' . $category->getId();
                        }

                        $html[] = '<div ' . $gan_plain_style . ' class="' . $gan_plain_class . '" >';

                        if (!($this->navigationPlace() == self::MENU_BAR)) {
                            $html[] = '<span class="gan-plain-border"></span>';
                        }

                        $gan_plain_items_class = 'gan-plain-items';
                        $gan_plain_items_style = '';

                        if ($this->offer_block_html) {
                            switch ($category->getData('navigation_pw_ob_pos')) {
                                case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::TOP :
                                    $html[] = $this->offer_block_html;
                                break;
								
                                case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::LEFT :
                                    $gan_plain_items_style .= 'float:right;';
                                    $_width = intval($category->getData('navigation_pw_s_width')) - intval($category->getData('navigation_pw_ob_width')) - 10;
                                    if ($_width > 0) {
                                        $gan_plain_items_style .= 'width:' . $_width . 'px;';
                                    }
                                break;
									
                                case GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::RIGHT :
                                    $gan_plain_items_style .= 'float:left;';
                                    $_width = intval($category->getData('navigation_pw_s_width')) - intval($category->getData('navigation_pw_ob_width')) - 10;
                                    if ($_width > 0) {
                                        $gan_plain_items_style .= 'width:' . $_width . 'px;';
                                    }
                                break;
                            }
                        }

                        $html[] = '<div class="' . $gan_plain_items_class . '" style="' . $gan_plain_items_style . '">';
                        $activeChildren = $this->sort_category($activeChildren);
                    }
                } else {
                    $_cat_column = null;
                    
					if ($category->getLevel() == ($this->root_level + 1)) {
                        if ($this->navigationPlace() == self::MENU_BAR) {
                            $_cat_column = ($category->getData('navigation_pw_s_column') ? $category->getData('navigation_pw_s_column') : 1);
                        } else {
                            $_cat_column = ($category->getData('navigation_column_side') ? $category->getData('navigation_column_side') : 1);
                        }
                    }

                    if (($this->childs_count == 1) || ($_cat_column && ($_cat_column != $this->current_column))) {
                        $this->current_column = $_cat_column;

                        if ($this->childs_count != 1) {
                            $html[] = '</ul>';
                        }

                        $_ul_styles = '';
						
                        if ($this->plain_root_cat->getData('navigation_pw_s_cwidth')) {
                            $_ul_styles .= 'width:' . $this->plain_root_cat->getData('navigation_pw_s_cwidth') . 'px;';
                        } else {
                            if ($this->plain_root_cat->getData('navigation_pw_s_width') && $this->columns) {
                                $_width = (intval($this->plain_root_cat->getData('navigation_pw_s_width')) -
                                    intval($this->plain_root_cat->getData('navigation_pw_ob_width'))) / $this->columns -
                                    intval($this->plain_root_cat->getData('navigation_pw_s_c_indentl')) -
                                    intval($this->plain_root_cat->getData('navigation_pw_s_c_indentr'));
                               
							    if ($_width > 0) {
                                    $_ul_styles .= 'width:' . $_width . 'px;';
                                }
                            }
                        }
						
                        if ($this->plain_root_cat->getData('navigation_pw_s_c_indentl')) {
                            $_ul_styles .= 'padding-left:' . $this->plain_root_cat->getData('navigation_pw_s_c_indentl') . 'px;';
                        }
						
                        if ($this->plain_root_cat->getData('navigation_pw_s_c_indentr')) {
                            $_ul_styles .= 'padding-right:' . $this->plain_root_cat->getData('navigation_pw_s_c_indentr') . 'px;';
                        }

                        $html[] = '<ul style="' . $_ul_styles . '" class="gan-plain-item">';
                    }

                    $li_class = ($category->getLevel() == ($this->root_level + 1) ? 'gan-plain-item-bold' : '');
					
                    if ($this->navigationPlace() == self::MENU_BAR) {
                        if ($category->getLevel() == ($this->root_level + 1)) {
                            $li_class .= ' sub-level1';
                        }
						
                        if ($category->getLevel() == ($this->root_level + 2)) {
                            $li_class .= ' sub-level2';
                        }
                    }

                    $navigation_image = '';
                    $image_position   = 0;
                    $category_view    = 0;
					
                    if ($this->navigationPlace() == self::MENU_BAR && $category->getData('navigation_pw_s_img')) {
                        if ($category->getLevel() == ($this->root_level + 1)) {
                            $category_view = $this->plain_root_cat->getData('navigation_pw_fl_view');
                            
							if ($category_view != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Attribute_View::TEXT) {
                                $navigation_image = $this->renderPlainImage($category->getData('navigation_pw_s_img'), $category, true);
                                $image_position   = $this->plain_root_cat->getData('navigation_pw_fl_ipos');
                            }
                        }
						
                        if ($category->getLevel() == ($this->root_level + 2)) {
                            $category_view = $this->plain_root_cat->getData('navigation_pw_sl_view');
                           
						    if ($category_view != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Attribute_View::TEXT) {
                                $navigation_image = $this->renderPlainImage($category->getData('navigation_pw_s_img'), $category, false);
                                $image_position   = $this->plain_root_cat->getData('navigation_pw_sl_ipos');
                            }
                        }
						
                        if ($navigation_image) {
                            $li_class .= GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::getListPositionClass($image_position);
                        }
                    }

                    $html[] = '<li class="' . $li_class . '">';
                    $htmlA = '<a style="padding-left:' . (10 * ($category->getLevel() - ($this->root_level + 1))) . 'px;" href="' . $this->categoryUrl($category) . '"' . $linkClass;
                    
					if ($this->isAjax()) {
                        $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                    }
					
                    $htmlA .= '>';
                    $html[] = $htmlA;

                    if ($navigation_image && in_array($image_position, array(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::TOP, GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::LEFT))) {
                        $html[] = $navigation_image;
                    }

                    if (!($navigation_image && $category_view == GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Attribute_View::IMAGE)) {
                        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    }

                    if ($navigation_image && in_array($image_position, array(GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::RIGHT, GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::BOTTOM))) {
                        $html[] = $navigation_image;
                    }

                    $html[] = '</a>';
                    $html[] = '</li>';
                }

                // render children
                $htmlChildren = '';
                $j            = 0;
				
                foreach ($activeChildren as $child) {
                    $this->childs_count++;

                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
               
			    if (!empty($htmlChildren)) {
                    $html[] = $htmlChildren;
                }
				
                if ($category->getLevel() == $this->root_level) {
                    if ($hasActiveChildren) {
                        $html[] = '</ul>';
                        $html[] = '</div>'; //gan-plain-items
                        
						if ($this->offer_block_html && $category->getData('navigation_pw_ob_pos') != GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::TOP) {
                            $html[] = $this->offer_block_html;
                        }
						
                        $html[] = '</div>'; //gan-plain
                    }
                    $html[] = '</li>';
                }
			break;

            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_FOLDING :
                $htmlLi = '<li';
				
                foreach ($attributes as $attrName => $attrValue) {
                    $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                }
				
                $htmlLi .= '>';
                $html[] = $htmlLi;
                $htmlA = '<a href="' . $this->categoryUrl($category) . '"';
                $htmlA .= ' style="padding-left: ' . (10 * ($category->getLevel() - $this->root_level)) . 'px;" ';

                if ($this->isAjax()) {
                    $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                }

                if ($this->categoryFilterIsActive($category) || $this->isCategoryActive($category)) {
                    $htmlA .= ' class="active" ';
                }

                $htmlA .= '>';
                $html[] = $htmlA;
                $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                $html[] = '</a>';

                // render children
                $htmlChildren = '';
                $j            = 0;
				
                foreach ($activeChildren as $child) {
                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
				
                if (!empty($htmlChildren)) {
                    $html[] = $htmlChildren;
                }
				
                $html[] = '</li>';
			break;
			
            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_IMAGE :
                $htmlLi = '<li';
				
                foreach ($attributes as $attrName => $attrValue) {
                    $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                }
				
                $htmlLi .= '>';
                $html[] = $htmlLi;
                $htmlA = '<a href="' . $this->categoryUrl($category) . '"';

                if ($this->isAjax()) {
                    $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                }
				
                if ($this->categoryFilterIsActive($category) || $this->isCategoryActive($category)) {
                    $htmlA .= ' class="active" ';
                }

                $htmlA .= '>';
                $html[] = $htmlA;
                $image_url = $category->getData('filter_image');
				
                if ($image_url) {
                    $image_url = Mage::getBaseUrl('media') . '/catalog/category/' . $image_url;

                    if ($image_width = $this->imageWidth()) {
                        $image_width = 'width="' . $image_width . '"';
                    } else {
                        $image_width = '';
                    }
					
                    if ($image_height = $this->imageHeight()) {
                        $image_height = 'height="' . $image_height . '"';
                    } else {
                        $image_height = '';
                    }

                    $html[] = '<img ' . $image_width . ' ' . $image_height . ' title="' . $category->getName() . '" src="' . $image_url . '" alt="' . $category->getName() . '" />';

                }

                if ($this->canShowImageName()) {
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                }

                $html[] = '</a>';
                $html[] = '</li>';
			break;

            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_PRO :
                if ($hasActiveChildren && !$noEventAttributes) {
                    $attributes['onmouseover'] = 'toggleMenu(this,1)';
                    $attributes['onmouseout']  = 'toggleMenu(this,0)';
                }
                // assemble list item with attributes
                $htmlLi = '<li';
				
                foreach ($attributes as $attrName => $attrValue) {
                    $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                }
				
                $htmlLi .= '>';				
                $html[] = $htmlLi;
                $htmlA = '<a href="' . $this->categoryUrl($category) . '"';

                if ($this->isAjax()) {
                    $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                }
				
                if ($this->isCategoryActive($category)) {
                    $htmlA .= ' class="active" ';
                }
				
                $htmlA .= '>';
                $html[] = $htmlA;
                $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                $html[] = '</a>';

                // render children
                $htmlChildren = '';
                $j            = 0;
				
                foreach ($activeChildren as $child) {
                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
				
                if (!empty($htmlChildren)) {
                    if ($childrenWrapClass) {
                        $html[] = '<div class="' . $childrenWrapClass . '">';
                    }
					
                    $html[] = '<ul class="level' . $level . '">';
                    $html[] = $htmlChildren;
                    $html[] = '</ul>';
					
                    if ($childrenWrapClass) {
                        $html[] = '</div>';
                    }
                }
				
                $html[] = '</li>';
			break;
			
            case GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_ACCORDION :
                $linkClass = '';
				
                if ($isOutermost && $outermostItemClass) {
                    $linkClass = $outermostItemClass;
                }
				
                if ($this->categoryFilterIsActive($category) || $this->isCategoryActive($category)) {
                    $linkClass .= ' active';
                }

                $linkClass = ' class="' . $linkClass . '" ';

                if ($category->getLevel() == $this->root_level) {
                    $htmlLi = '<li';
                    
					foreach ($attributes as $attrName => $attrValue) {
                        $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }
					
                    $htmlLi .= '>';
                    $html[] = $htmlLi;
                    $htmlA = '<a href="' . $this->categoryUrl($category) . '"' . $linkClass;
                    $htmlA .= ' onclick="ganShowAccordionItem(this);return false;" ';
                    $htmlA .= '>';
                    $html[] = $htmlA;
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    $html[] = '</a>';

                    if ($hasActiveChildren) {
                        $html[] = '<div class="gan-accordion-items">';
                    }
                } else {
                    if ($this->childs_count == 1) {
                        $html[] = '<ul class="gan-accordion-item">';
                    }

                    $html[] = '<li>';
                    $htmlA  = '<a href="' . $this->categoryUrl($category) . '"' . $linkClass;

                    if ($this->isAjax()) {
                        $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                    }
					
                    $htmlA .= '>';
                    $html[] = $htmlA;
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    $html[] = '</a>';
                    $html[] = '</li>';
                }

                // render children
                $htmlChildren = '';
                $j            = 0;
				
                foreach ($activeChildren as $child) {
                    $this->childs_count++;

                    $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                        $child,
                        ($level + 1),
                        ($j == $activeChildrenCount - 1),
                        ($j == 0),
                        false,
                        $outermostItemClass,
                        $childrenWrapClass,
                        $noEventAttributes
                    );
                    $j++;
                }
				
                if (!empty($htmlChildren)) {
                    $html[] = $htmlChildren;
                }
				
                if ($category->getLevel() == $this->root_level) {
                    if ($hasActiveChildren) {
                        $html[] = '</ul>';
                        $html[] = '</div>'; //gan-accordion-items
                    }
					
                    $html[] = '</li>';
                }		
			break;
			
            default :
                if ($this->navigationPlace() == self::MENU_BAR) {
                    if ($hasActiveChildren && !$noEventAttributes) {
                        $attributes['onmouseover'] = 'toggleMenu(this,1)';
                        $attributes['onmouseout']  = 'toggleMenu(this,0)';
                    }
                    // assemble list item with attributes
                    $htmlLi = '<li';
					
                    foreach ($attributes as $attrName => $attrValue) {
                        $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }
					
                    $htmlLi .= '>';
                    $html[] = $htmlLi;
                    $html[] = '<a href="' . $this->categoryUrl($category) . '"' . $linkClass . '>';
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    $html[] = '</a>';

                    // render children
                    $htmlChildren = '';
                    $j            = 0;
					
                    foreach ($activeChildren as $child) {
                        $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                            $child,
                            ($level + 1),
                            ($j == $activeChildrenCount - 1),
                            ($j == 0),
                            false,
                            $outermostItemClass,
                            $childrenWrapClass,
                            $noEventAttributes
                        );
                        $j++;
                    }
					
                    if (!empty($htmlChildren)) {
                        if ($childrenWrapClass) {
                            $html[] = '<div class="' . $childrenWrapClass . '">';
                        }
						
                        $html[] = '<ul class="level' . $level . '">';
                        $html[] = $htmlChildren;
                        $html[] = '</ul>';
						
                        if ($childrenWrapClass) {
                            $html[] = '</div>';
                        }
                    }
					
                    $html[] = '</li>';
                } else {
                    $htmlLi = '<li';
                   
				    foreach ($attributes as $attrName => $attrValue) {
                        $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
                    }

                    $htmlLi .= '>';
                    $html[] = $htmlLi;
                    $htmlA = '<a href="' . $this->categoryUrl($category) . '"';
                    $htmlA .= ' style="padding-left: ' . (10 * ($category->getLevel() - $this->root_level)) . 'px;" ';

                    if ($this->isAjax()) {
                        $htmlA .= ' onclick="GomageNavigation.setNavigationUrl(\'' . $this->categoryFilterUrl($category) . '\'); return false;" ';
                    }
					
                    if ($this->categoryFilterIsActive($category) || $this->isCategoryActive($category)) {
                        $htmlA .= ' class="active" ';
                    }
					
                    $htmlA .= '>';
                    $html[] = $htmlA;
                    $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
                    $html[] = '</a>';

                    //render children
                    $htmlChildren = '';
                    $j            = 0;

                    if ($this->categoryFilterIsActive($category) || $this->showAllSubcategories()) {
                        foreach ($activeChildren as $child) {
                            $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                                $child,
                                ($level + 1),
                                ($j == $activeChildrenCount - 1),
                                ($j == 0),
                                false,
                                $outermostItemClass,
                                $childrenWrapClass,
                                $noEventAttributes
                            );
                            $j++;
                        }
                    } else {
                        foreach ($activeChildren as $child) {
                            if ($this->_getActiveChildren($child)) {
                                $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                                    $child,
                                    ($level + 1),
                                    ($j == $activeChildrenCount - 1),
                                    ($j == 0),
                                    false,
                                    $outermostItemClass,
                                    $childrenWrapClass,
                                    $noEventAttributes
                                );
                                $j++;
                            }
                        }
                    }

                    if (!empty($htmlChildren)) {
                        if ($childrenWrapClass) {
                            $html[] = '<div class="' . $childrenWrapClass . '">';
                        }
						
                        $html[] = '<ul class="level' . $level . '">';
                        $html[] = $htmlChildren;
                        $html[] = '</ul>';
						
                        if ($childrenWrapClass) {
                            $html[] = '</div>';
                        }
                    }
					
                    $html[] = '</li>';
                }
        }

        $html = implode("\n", $html);
		
        return $html;
    }
	
    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo() {
        $helper			= Mage::helper('gomage_navigation');
        $shortCacheId	= array(
            'CATALOG_NAVIGATION',
            $this->navigationPlace(),
            Mage::app()->getStore()->getId(),
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            'template' => $this->getTemplate(),
            'name'     => $this->getNameInLayout(),
            'cats'     => $helper->getRequest()->getParam('cat')
        );
        $cacheId      = $shortCacheId;
        $shortCacheId = array_values($shortCacheId);
        $shortCacheId = implode('|', $shortCacheId);
        $shortCacheId = md5($shortCacheId);

        $cacheId['category_path']  = $this->getCurrenCategoryKey();
        $cacheId['short_cache_id'] = $shortCacheId;

        return $cacheId;
    }

    public function getCurrenCategoryKey(){
        if ($category = Mage::registry('current_category')) {
            return $category->getPath();
        } else {
            return Mage::app()->getStore()->getRootCategoryId();
        }
    }

    public function getResizedImage($image, $width = null, $height = null, $quality = 100) {
        $imageUrl = Mage::getBaseDir('media') . DS . "catalog" . DS . "category" . DS . $image;
        
		if (!is_file($imageUrl)) {
            return false;
        }

        $image_name_resized = '_' . $width . '_' . $height . '_' . $image;
        $image_resized      = Mage::getBaseDir('media') . DS . "catalog" . DS . "product" . DS . "cache" . DS . "cat_resized" . DS . $image_name_resized;
		
        if (!file_exists($image_resized) && file_exists($imageUrl) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($image_resized)) {
            $imageObj = new Varien_Image ($imageUrl);
            $imageObj->constrainOnly(true);
            $imageObj->keepFrame(false);
            $imageObj->quality($quality);
            
			if ($width) {
                $imageObj->resize($width, ($height ? $height : null));
            }
			
            $imageObj->save($image_resized);
        }

        if (file_exists($image_resized)) {
            return Mage::getBaseUrl('media') . "catalog/product/cache/cat_resized/" . $image_name_resized;
        } else {
            return false;
        }
    }

    public function sort_category($array) {
        if ($this->navigationPlace() == self::MENU_BAR) {
            usort($array, "gan_cat_sort");
        } else {
            usort($array, "gan_cat_slide_sort");
        }

        return $array;
    }

    protected function _getActiveChildren($category) {
        $category_model   = Mage::getModel('catalog/category');
        $category         = $category_model->load($category->getId());
        $parent           = $category->getParentCategory();
        $child_categories = $category_model->getResource()->getAllChildren($parent);

        foreach ($child_categories as $id) {
            $child = $category_model->load($id);

            if ($this->categoryFilterIsActive($child)) {
                return true;
            }
        }
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getCurrentCategoryPath() {
        if ($this->curentCategory()) {
            return explode(',', $this->curentCategory()->getPathInStore());
        }
		
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function drawOpenCategoryItem($category) {
        $html = '';
        
		if (!$category->getIsActive()) {
            return $html;
        }

        $html .= '<li';

        if ($this->isCategoryActive($category)) {
            $html .= ' class="active"';
        }

        $html .= '>' . "\n";
        $html .= '<a href="' . $this->categoryUrl($category) . '"><span>' . $this->htmlEscape($category->getName()) . '</span></a>' . "\n";

        if (in_array($category->getId(), $this->getCurrentCategoryPath())) {
            $children    = $category->getChildren();
            $hasChildren = $children && $children->count();

            if ($hasChildren) {
                $htmlChildren = '';
				
                foreach ($children as $child) {
                    $htmlChildren .= $this->drawOpenCategoryItem($child);
                }

                if (!empty($htmlChildren)) {
                    $html .= '<ul>' . "\n"
                        . $htmlChildren
                        . '</ul>';
                }
            }
        }
        $html .= '</li>' . "\n";
        
		return $html;
    }

    /**
     * Render categories menu in HTML
     *
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderPlainImage($navigation_image, $category, $first_level) {
        if ($first_level) {
            $image_position = $this->plain_root_cat->getData('navigation_pw_fl_ipos');
            $width          = $this->plain_root_cat->getData('navigation_pw_fl_iwidth');
            $height         = $this->plain_root_cat->getData('navigation_pw_fl_iheight');
        } else {
            $image_position = $this->plain_root_cat->getData('navigation_pw_sl_ipos');
            $width          = $this->plain_root_cat->getData('navigation_pw_sl_iwidth');
            $height         = $this->plain_root_cat->getData('navigation_pw_sl_iheight');
        }

        $plain_image = '';

        $navigation_image = $this->getResizedImage($navigation_image, $width, $height);

        if ($navigation_image) {
            $_add_image_style = '';
            
			if ($width) {
                $_add_image_style = 'width:' . $width . 'px;';
            }
			
            if ($height) {
                $_add_image_style .= 'height:' . $height . 'px;';
            }

            if ($_add_image_style) {
                $_add_image_style = 'style="' . $_add_image_style . '"';
            }
			
            $plain_image .= '<img class="' . GoMage_Navigation_Model_Adminhtml_System_Config_Source_Category_Image_Position::getPositionClass($image_position) . '" ' . $_add_image_style . ' src="' . $navigation_image . '" alt="' . $this->escapeHtml($category->getName()) . '" />';
        }

        return $plain_image;
    }
}
