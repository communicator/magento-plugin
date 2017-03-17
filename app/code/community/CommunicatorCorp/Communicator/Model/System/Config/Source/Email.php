<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\EnumerationType\SortOrder;
use CommunicatorCorp\Client\ObjectDefinition\PageOption;

/**
 * Source for email options.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Email extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getData()
    {
        $pageOption = $this->getPageOption();

        return $this
            ->getDataHelper()
            ->getMessageService()
            ->getEmailList($pageOption)
        ;
    }

    /**
     * Returns a `PageOption` for returning emails from Communicator.
     *
     * @return PageOption
     */
    private function getPageOption()
    {
        $sortOrder = new SortOrder(SortOrder::OLDEST_FIRST);

        $pageOption = (new PageOption)
            ->setOrder($sortOrder)
            ->setPageSize(25)
        ;

        return $pageOption;
    }
}
