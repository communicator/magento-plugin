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
 * Source for mailing list select options.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_List extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getData($source = null)
    {
        return $this
            ->getDataHelper()
            ->getDataService()
            ->getMailingLists()
        ;
    }
}
