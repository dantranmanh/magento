<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento enterprise edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Ajaxcartpro
 * @version    3.2.11
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Ajaxcartpro_Test_WebDriver_SettingDisplayFor extends EcomDev_PHPUnit_Test_WebdriverCase
{

    public function checkRequiredOptionsOnly()
    {
        $this->_setDisplayOptionsPopupFor('Only products with required options');
        //go to category
        $this->_webDriver->get(
            Mage::getBaseUrl() . '/product-types.html'
        );
        $btnList = $this->_webDriver->findElements(WebDriverBy::cssSelector('.btn-cart'));
        $btn = $btnList[2];/** @var WebDriverElement $btn */
        $btn->click();
        $this->_webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::id('product-addtocart-form-acp')
            )
        );
        $elements = $this->_webDriver->findElements(
            WebDriverBy::id('product-addtocart-form-acp')
        );
        $this->assertEquals(count($elements), 1, 'Options popup is not show');
    }

    public function checkAllOptions()
    {
        $this->_setDisplayOptionsPopupFor('Only Products With Options');
        //go to category
        $this->_webDriver->get(
            Mage::getBaseUrl() . '/product-types.html'
        );
        $btnList = $this->_webDriver->findElements(WebDriverBy::cssSelector('.btn-cart'));
        $btn = $btnList[4];/** @var WebDriverElement $btn */
        $btn->click();
        $this->_webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::id('product-addtocart-form-acp')
            )
        );
        $elements = $this->_webDriver->findElements(
            WebDriverBy::id('product-addtocart-form-acp')
        );
        $this->assertEquals(count($elements), 1, 'Options popup is not show');
    }

    public function checkAllProducts()
    {
        $this->_setDisplayOptionsPopupFor('All Products');
        //try to add product with option
        $this->_webDriver->get(
            Mage::getBaseUrl() . '/product-types.html'
        );
        $btnList = $this->_webDriver->findElements(WebDriverBy::cssSelector('.btn-cart'));
        $btn = $btnList[4];/** @var WebDriverElement $btn */
        $btn->click();
        $this->_webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::id('product-addtocart-form-acp')
            )
        );
        $elements = $this->_webDriver->findElements(
            WebDriverBy::id('product-addtocart-form-acp')
        );
        $this->assertEquals(count($elements), 1, 'Options popup is not show');

        //try to add product without option
        $this->_webDriver->get(
            Mage::getBaseUrl() . '/product-types.html'
        );
        $btnList = $this->_webDriver->findElements(WebDriverBy::cssSelector('.btn-cart'));
        $btn = $btnList[0];/** @var WebDriverElement $btn */
        $btn->click();
        $this->_webDriver->wait(10, 500)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::id('product-addtocart-form-acp')
            )
        );
        $elements = $this->_webDriver->findElements(
            WebDriverBy::id('product-addtocart-form-acp')
        );
        $this->assertEquals(count($elements), 1, 'Options popup is not show');
    }

    protected function _setDisplayOptionsPopupFor($optionText)
    {
        EcomDev_PHPUnit_Test_WebDriver_Snippet::loginToAdminArea($this->_webDriver);
        $this->_webDriver->get(
            EcomDev_PHPUnit_Test_WebDriver_Helper::getUrlByRoute(
                'adminhtml/system_config/edit', array('section' => 'ajaxcartpro')
            )
        );
        $this->assertContains('Configuration / System / Magento Admin', $this->_webDriver->getTitle());
        $elements = $this->_webDriver->findElements(WebDriverBy::id('ajaxcartpro_general-head'));
        $this->assertGreaterThan(0, count($elements), 'Can not find #ajaxcartpro_general-head on system configuration');

        $this->_openFieldset('ajaxcartpro_general');
        $displayForElement = new WebDriverSelect($this->_webDriver->findElement(
            WebDriverBy::id('ajaxcartpro_general_displaypopupfor')
        ));
        $displayForElement->selectByVisibleText($optionText);
        $this->_webDriver->findElement(WebDriverBy::cssSelector('.form-buttons button'))->click();//submit form
    }

    private function _openFieldset($elementID)
    {
        $openElements = $this->_webDriver->findElements(WebDriverBy::className('open'));
        $isTargetElementOpen = false;
        foreach ($openElements as $element) {
            if ($element->getID() === $elementID) {
                $isTargetElementOpen = true;
                break;
            }
        }
        if ($isTargetElementOpen) {
            $this->_webDriver->findElement(WebDriverBy::id($elementID . '-head'))->click();
        }
    }
}