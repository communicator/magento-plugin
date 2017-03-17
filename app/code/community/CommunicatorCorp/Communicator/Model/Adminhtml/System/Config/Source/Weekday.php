<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

/**
 * Day source.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@honestempire.com>
 */
class CommunicatorCorp_Communicator_Model_Adminhtml_System_Config_Source_Weekday
{
    use CommunicatorCorp_Communicator_Helper_Trait;

    /**
     * Returns the days of the week as an options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = $this->getDataHelper();

        return array(
            $helper->__('Sunday'),
            $helper->__('Monday'),
            $helper->__('Tuesday'),
            $helper->__('Wednesday'),
            $helper->__('Thursday'),
            $helper->__('Friday'),
            $helper->__('Saturday'),
        );
    }
}
