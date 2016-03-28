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
 * @since        Release available since Release 4.7
 */

$installer = $this;

$installer->startSetup();

$pageTable = $installer->getTable('cms/page');
$installer->getConnection()->addColumn($pageTable, 'navigation',
    "TINYINT(1) NOT NULL DEFAULT 0");

$installer->endSetup(); 