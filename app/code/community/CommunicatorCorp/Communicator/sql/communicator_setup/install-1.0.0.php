<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute('order', 'dispatch_triggered', array(
    'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    'visible' => false,
    'required'  => false,
));

$installer->endSetup();
