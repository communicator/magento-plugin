<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\ObjectDefinition\ClientTableColumn;

/**
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Interval extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getData()
    {
        $options = array();

        foreach (array(30, 45, 60, 90, 120) as $interval) {
            $options[] = (new ClientTableColumn)
                ->setId($interval)
                ->setName(sprintf('%d minutes', $interval))
            ;
        }

        return $options;
    }
}
