<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\ObjectDefinition\ClientTable;

/**
 * Source for client table select options.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Table extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getData()
    {
        $notApplicableTable = (new ClientTable)
            ->setId(0)
            ->setName($this->getDataHelper()->__('Not Applicable'))
        ;

        $clientTables = $this
            ->getDataHelper()
            ->getDataService()
            ->getClientTables()
        ;

        array_unshift($clientTables, $notApplicableTable);

        return $clientTables;
    }
}
