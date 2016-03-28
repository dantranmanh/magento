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

class GoMage_Navigation_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Main extends Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
{
    /**
     * Adding product form elements for editing attribute
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        
        $helper = Mage::helper('gomage_navigation');
        
        $form = $this->getForm();
        
        $fieldset = $form->addFieldset('advanced_navigation_fieldset', array('legend'=>Mage::helper('catalog')->__('Advanced Navigation Properties')));
             
        $field = $fieldset->addField('filter_type', 'select', array(
            'name' => 'filter_type',
            'label' => Mage::helper('catalog')->__('Filter Type'),
            'title' => Mage::helper('catalog')->__('Filter Type'),
            'values'=>Mage::getModel('gomage_navigation/adminhtml_system_config_source_filter_type_attribute')->toOptionArray(),
        ));
        
        $field = $fieldset->addField('inblock_type', 'select', array(
            'name' => 'inblock_type',
            'label' => Mage::helper('catalog')->__('Block Height'),
            'title' => Mage::helper('catalog')->__('Block Height'),
            'values'=>Mage::getModel('gomage_navigation/adminhtml_system_config_source_filter_type_inblock')->toOptionArray(),
        ));
        
        $field = $fieldset->addField('round_to', 'text', array(
            'name' => 'round_to',
            'label' => Mage::helper('catalog')->__('Slider Step'),
            'title' => Mage::helper('catalog')->__('Slider Step'),
            'class' => 'gomage-validate-number',
        ));
        
        $field = $fieldset->addField('show_currency', 'select', array(
            'name' => 'show_currency',
            'label' => Mage::helper('catalog')->__('Show currency in slider'),
            'title' => Mage::helper('catalog')->__('Show currency in slider'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));

        $field = $fieldset->addField('is_ajax', 'select', array(
            'name' => 'is_ajax',
            'label' => Mage::helper('catalog')->__('Use Ajax'),
            'title' => Mage::helper('catalog')->__('Use Ajax'),
            'values'=>array(
                0 => $this->__('No'),
                1 => $this->__('Yes'),
            ),
        ));

        $field = $fieldset->addField('range_options', 'select', array(
            'name' => 'range_options',
            'label' => Mage::helper('catalog')->__('Use Options Range'),
            'title' => Mage::helper('catalog')->__('Use Options Range'),
            'values'=>Mage::getModel('gomage_navigation/adminhtml_system_config_source_filter_optionsrange')->toOptionArray(),
        ));

        $fieldset->addField('range_auto', 'text', array(
            'name' => 'range_auto',
            'label' => Mage::helper('catalog')->__('Ranges'),
            'title' => Mage::helper('catalog')->__('Ranges'),
        ));

        $fieldset->addField('range_manual', 'text', array(
            'name' => 'range_manual',
            'label' => Mage::helper('catalog')->__('Ranges'),
            'title' => Mage::helper('catalog')->__('Ranges'),
        ));
        
        $fieldset->addField('inblock_height', 'text', array(
            'name' => 'inblock_height',
            'label' => Mage::helper('catalog')->__('Block Height, px'),
            'title' => Mage::helper('catalog')->__('Block Height, px'),
            'class' => 'gomage-validate-number',
        ));
        
        $fieldset->addField('max_inblock_height', 'text', array(
            'name' => 'max_inblock_height',
            'label' => Mage::helper('catalog')->__('Max. Block Height, px'),
            'title' => Mage::helper('catalog')->__('Max. Block Height, px'),
            'class' => 'gomage-validate-number',
        ));
        
        $fieldset->addField('filter_button', 'select', array(
            'name' => 'filter_button',
            'label' => Mage::helper('catalog')->__('Show Filter Button '),
            'title' => Mage::helper('catalog')->__('Show Filter Button '),
            'values'=>array(
                	0 => $this->__('No'),
                	1 => $this->__('Yes'),
                ),
            
        ));
        
        $field->setValue(0);
        
        $fieldset->addField('show_minimized', 'select', array(
            'name' => 'show_minimized',
            'label' => Mage::helper('catalog')->__('Show Collapsed'),
            'title' => Mage::helper('catalog')->__('Show Collapsed'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));
        
        $field = $fieldset->addField('show_checkbox', 'select', array(
            'name' => 'show_checkbox',
            'label' => Mage::helper('catalog')->__('Show Checkboxes'),
            'title' => Mage::helper('catalog')->__('Show Checkboxes'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));
        
        $field->setValue(0);
        
        $field = $fieldset->addField('show_image_name', 'select', array(
            'name' => 'show_image_name',
            'label' => Mage::helper('catalog')->__('Show Image Name'),
            'title' => Mage::helper('catalog')->__('Show Image Name'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));
        
        $field->setValue(0);
        
        $fieldset->addField('image_align', 'select', array(
            'name' => 'image_align',
            'label' => Mage::helper('catalog')->__('Options Alignment'),
            'title' => Mage::helper('catalog')->__('Options Alignment'),
            'values'=> Mage::getModel('gomage_navigation/adminhtml_system_config_source_filter_image_align')->toOptionArray(),
        ));
        
        $fieldset->addField('image_width', 'text', array(
            'name' => 'image_width',
            'label' => Mage::helper('catalog')->__('Image Width, px'),
            'title' => Mage::helper('catalog')->__('Image Width, px'),
            'class' => 'gomage-validate-number',
        ));
        
        $fieldset->addField('image_height', 'text', array(
            'name' => 'image_height',
            'label' => Mage::helper('catalog')->__('Image Height, px'),
            'title' => Mage::helper('catalog')->__('Image Height, px'),
            'class' => 'gomage-validate-number',
        ));
        
        $fieldset->addField('visible_options', 'text', array(
            'name' => 'visible_options',
            'label' => Mage::helper('catalog')->__('Visible Options per Attribute'),
            'title' => Mage::helper('catalog')->__('Visible Options per Attribute'),
            'class' => 'gomage-validate-number',
        ));
        
        $field= $fieldset->addField('show_help', 'select', array(
            'name' => 'show_help',
            'label' => Mage::helper('catalog')->__('Show Help Icon'),
            'title' => Mage::helper('catalog')->__('Show Help Icon'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));
        
        $field->setValue(0);
        
        $fieldset->addField('popup_width', 'text', array(
            'name' => 'popup_width',
            'label' => Mage::helper('catalog')->__('Popup Width, px'),
            'title' => Mage::helper('catalog')->__('Popup Width, px'),
            'class' => 'gomage-validate-number',
        ));
        $fieldset->addField('popup_height', 'text', array(
            'name' => 'popup_height',
            'label' => Mage::helper('catalog')->__('Popup Height, px'),
            'title' => Mage::helper('catalog')->__('Popup Height, px'),
            'class' => 'gomage-validate-number',
        ));
        
        $field = $fieldset->addField('filter_reset', 'select', array(
            'name' => 'filter_reset',
            'label' => Mage::helper('catalog')->__('Show Reset Link'),
            'title' => Mage::helper('catalog')->__('Show Reset Link'),
            'values'=>array(
            	0 => $this->__('No'),
            	1 => $this->__('Yes'),
            ),
        ));
        
        $field = $fieldset->addField('category_ids_filter', 'text', array(
            'name' => 'category_ids_filter',
            'label' => Mage::helper('catalog')->__('Exclude Categories'),
            'title' => Mage::helper('catalog')->__('Exclude Categories'),
            'class' => 'gomage-validate-number',
        ));
        
        $field = $fieldset->addField('attribute_location', 'select', array(
            'name' => 'attribute_location',
            'label' => Mage::helper('catalog')->__('Attribute Location'),
            'title' => Mage::helper('catalog')->__('Attribute Location'),
            'values'=> Mage::getModel('gomage_navigation/adminhtml_system_config_source_filter_attributelocation')->toOptionArray(),
        ));
        
        $field->setValue(0);
		
        $field->setData('after_element_html', '<script type="text/javascript">
        	
        	is_price = false;  	
			var options = $("filter_type").select("option");
			
        	if($("frontend_input").value != "price"){
        		var id = "range_options"; 
				
                if ($(id)){
                  	$(id).up("td").up("tr").hide();	
				}	
    			
    			for (var i = 0; i < options.length; i++) {
    				e = options[i];
    				
    				if(e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER.'){
    					e.parentNode.removeChild(e);
    				}
    			};
        	} else {
				for (var i = 0; i < options.length; i++) {
    				e = options[i];
    				
					if(e.value == ' . GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_IMAGE . '){
    					e.parentNode.removeChild(e);
    				}
    			};
				
        		is_price = true;
        	}
        	
        	Event.observe($("frontend_input"), "change", function(e){
        		
        		if(this.value == "price"){
        			
        			if(!is_price){
	        			
	        			var option = document.createElement("option");
						option.value = "'.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT.'";
						option.innerHTML = "'.$helper->__('Input').'";
						
						$("filter_type").appendChild(option);
						
						var option = document.createElement("option");
						option.value = "'.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER.'";
						option.innerHTML = "'.$helper->__('Slider').'";
						
						$("filter_type").appendChild(option);
						
						var option = document.createElement("option");
						option.value = "'.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT.'";
						option.innerHTML = "'.$helper->__('Slider and Input').'";
						
						$("filter_type").appendChild(option);
												
						var option = document.createElement("option");
						option.value = "'.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER.'";
						option.innerHTML = "'.$helper->__('Input and Slider').'";
						
						$("filter_type").appendChild(option);
						
	        			is_price = true;
	        			
    				}
    				
        		}else{
        			
        			is_price = false;
        			
        			var options = $("filter_type").select("option");
        			
        			for(var i = 0; i < options.length; i++){
        				
        				e = options[i];
        				
        				if(e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT.' || e.value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER. '){
        					
        					e.parentNode.removeChild(e);
        					
        				}
        			};
        			
        		}
        		
        	});
        	
        	Event.observe("filter_type", "change", function(){
                    var value = $("filter_type").value;                    
                    var elements = eval('.$this->_getAssociatedElements().');
                    if (value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_IMAGE.'){
                    	for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").show();	
    						}
                        }
                        var id = "inblock_type"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_options"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();
						}
						
						var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
    				}else if (value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT_INBLOCK.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        
                        var id = "inblock_type"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
                        if ($("inblock_type").up("td").up("tr").visible() == true){
                        
                        	if ( $("inblock_type").value == '.GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Inblock::TYPE_FIXED.' )
                        	{
                        		var id_new = "inblock_height";						
						
		                        if ($(id_new)){
		                        	$(id_new).up("td").up("tr").show();	
								}	
                        	}
                        	else
                        	{
                        		var id_new = "max_inblock_height";						
						
		                        if ($(id_new)){
		                        	$(id_new).up("td").up("tr").show();	
								}
                        	}	
						}
						else
						{
							$(id).up("td").up("tr").hide();
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_options"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();
						}
						
						var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "image_align"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
    				}
    				else if (value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER_INPUT.' || value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_INPUT_SLIDER.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "inblock_type"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "range_options"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();
						}
						
						var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();
						}
						
    				}
    				else if (value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_SLIDER.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "inblock_type"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "range_options"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();
						}
						
						var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
    				}
    				else if (value == '.GoMage_Navigation_Model_Catalog_Layer::FILTER_TYPE_DEFAULT.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "inblock_type"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_options"; 
                        if ($(id) && $("frontend_input").value == "price"){
                        	$(id).up("td").up("tr").show();

                            Gomage_Navigation_fireEvent($("range_options"), "change");

						}
						
						var id = "image_align"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}


    				}
    				else{
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "inblock_type"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "filter_button"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "round_to"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_options"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "show_currency"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
        			}
                });
                
                Event.observe(window, "load", function() {
						init_filter_type();	
					}
				);
                                
                function init_filter_type() {
                	Gomage_Navigation_fireEvent($("filter_type"), "change");
                }
                
                
                Event.observe("inblock_type", "change", function(){
                    var value = $("inblock_type").value;                    
                    var elements = {};
                    if (value == '.GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Inblock::TYPE_FIXED.'){
                    
                    	for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").show();	
    						}
                        }
                        var id = "max_inblock_height";

                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						var id = "inblock_height"; 
                        if ($(id) && $("inblock_type").up("td").up("tr").visible() == true){
                        
                        	$(id).up("td").up("tr").show();	
						}
						else
						{
							$(id).up("td").up("tr").hide();
						}
    				}else if (value == '.GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Type_Inblock::TYPE_AUTO.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        
                        var id = "max_inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").show();	
						}
						
						var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
    				}else{
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "max_inblock_height"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "inblock_height"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
        			}
                });
                
                Event.observe(window, "load", function() {
						init_inblock_type();	
					}
				);
                                
                function init_inblock_type() {
                	Gomage_Navigation_fireEvent($("inblock_type"), "change");
                }
                
                var range_auto = false;
                var range_count = 0;


                function addRange()
                {
                    range_count++;

                    var range_count_id = "range_"+range_count;
                	var id = "add_range_button";
                    if ($(id))
                    {
                    	var html = "<div id=\'range_"+range_count+"\'><input class=\'to_value input-text\' type=\'text\' name=\'to_value[]\' style=\'width:120px;margin:0 0 5px\'><input type=\'text\' name=\'step[]\' class=\'step input-text\' style=\'width:120px;margin: 0 0 5px 8px;\'><span style=\'padding-left: 10px; cursor: pointer;\' onclick=\'remove_range(this);\'>&#x2715;</span></div>";
                    	$(id).insert({before: html});
					}
                }
                function remove_range(t)
                {
                    var parent = $(t).up();
                    if ( parent )
                    {
                        parent.remove();
                    }
                }
                
                 Event.observe("range_options", "change", function(){
                    var value = $("range_options").value;                    
                    var elements = {};
                    if (value == '.GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::AUTO.'){
                    	for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").show();	
    						}
                        }
                        var id = "range_manual"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}

                        var show_auto_range = false;
						var id = "range_options";
						if ($(id)){
                        	show_auto_range = $(id).up("td").up("tr").visible();
						}

						var id = "range_auto";
                        if ($(id) && show_auto_range){
                        	$(id).up("td").up("tr").show();
							$(id).hide();
                        	var td = $(id).up("td");

							var data = $("range_auto").value;
							var auto_fields = "<label style=\'display:inline-block;width:126px;\'>To Value</label><label id=\'place_ranges\' style=\'display: inline-block; margin-left: 10px; width: 124px;\'>Step</label>";

							if ( data != "" )
							{

								var bigArray = data.split(",");

								var length = bigArray.length,
								element = null;
								for (var i = 0; i < length; i++) {
								    range_count++;
								  element = bigArray[i];

								  if ( element != undefined )
								  {
									  var smallArray = element.split("=");

									  if ( smallArray[0] != undefined && smallArray[1] != undefined )
									  {
									  	auto_fields = auto_fields + "<div id=\'range_"+range_count+"\'><input type=\'text\' name=\'to_value[]\' value=\'" + smallArray[0] + "\' class=\'to_value input-text\' style=\'width:120px;margin:0 0 5px;\'><input class=\'step input-text\' style=\'width:120px;margin: 0 0 5px 8px;\' type=\'text\' name=\'step[]\' value=\'" + smallArray[1] + "\'><span style=\'padding-left: 10px; cursor: pointer;\' onclick=\'remove_range(this);\'>&#x2715;</span></div>";
									  }
								  }
								}

								auto_fields = auto_fields + "<div style=\'width:280px; text-align:right\'><button id=\'add_range_button\' class=\'button\' onclick=\'addRange();return false;\'><span><span><span>' . Mage::helper('catalog')->__('Add Range') .'</span></span></span></button></div>";
							}
							else
							{
								auto_fields = auto_fields + "<div id=\'range_0\'><input class=\'to_value input-text\' type=\'text\' name=\'to_value[]\' style=\'width:120px;margin:0 0 5px;\'><input class=\'step input-text\' type=\'text\' name=\'step[]\' style=\'width:120px;margin: 0 0 5px 8px;\'><span style=\'padding-left: 10px; cursor: pointer;\' onclick=\'remove_range(this);\'>&#x2715;</span></div><div style=\'width:280px; text-align:right\'><button id=\'add_range_button\' class=\'button\' onclick=\'addRange();return false;\'><span><span><span>' . Mage::helper('catalog')->__('Add Range') .'</span></span></span></button></div>";
							}

                        	td.innerHTML = "<input type=\'hidden\' id=\'range_auto\'>" + auto_fields;

                        	$("range_auto").value = data;
						}
						
    				}else if (value == '.GoMage_Navigation_Model_Adminhtml_System_Config_Source_Filter_Optionsrange::MANUALLY.'){
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        
                        var id = "range_auto"; 
                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
						
						var id = "range_manual"; 
                        if ($(id) && $("range_options").up("td").up("tr").visible() == true){
                        
                        	$(id).up("td").up("tr").show();	
						}
						else
						{
							$(id).up("td").up("tr").hide();
						}
    				}else{
    					for (var i = 0; i < elements.length; i++) {
                    		var id = elements[i]; 
                            if ($(id)){
                            	$(id).up("td").up("tr").hide();	
    						}
                        }
                        var id = "range_auto"; 
                        if ($(id)){
                            $(id).up("td").up("tr").hide();	
    					}
    					var id = "range_manual";

                        if ($(id)){
                        	$(id).up("td").up("tr").hide();	
						}
        			}
                });
                
                Event.observe(window, "load", function() {
						init_range_options();
					}
				);

                                
                function init_range_options() {
                
                	Gomage_Navigation_fireEvent($("range_options"), "change");
                }
        	
        </script>');

        return $this;
    }
    
    protected function _getAssociatedElements()
    {
        return  json_encode(array('show_image_name', 'image_align', 'image_width', 'image_height'));
    }
}
