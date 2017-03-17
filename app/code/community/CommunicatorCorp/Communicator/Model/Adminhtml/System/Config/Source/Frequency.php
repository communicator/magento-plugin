<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency as Frequency;

/**
 * Source model used for custom frequencies in the scheduler.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Adminhtml_System_Config_Source_Frequency
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Returns an array of custom frequency options for the scheduler.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = $this->getDataHelper();

        return array(
            Frequency::CRON_DAILY  => $helper->__('Daily'),
            Frequency::CRON_WEEKLY => $helper->__('Weekly'),
        );
    }
}
