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
 * Helper trait.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
trait CommunicatorCorp_Communicator_Helper_Trait
{
    /**
     * Returns the Communicator data helper.
     *
     * @return CommunicatorCorp_Communicator_Helper_Data
     */
    public function getDataHelper()
    {
        return $this->getCommunicatorHelper('communicator/data');
    }

    /**
     * Returns the Communicator event helper.
     *
     * @return CommunicatorCorp_Communicator_Helper_Event
     */
    public function getEventHelper()
    {
        return $this->getCommunicatorHelper('communicator/event');
    }

    /**
     * Returns the Communicator mapping helper.
     *
     * @return CommunicatorCorp_Communicator_Helper_Mapping
     */
    public function getMappingHelper()
    {
        return $this->getCommunicatorHelper('communicator/mapping');
    }

    /**
     * Returns a Communicator helper.
     *
     * @param string $helper The helper name.
     *
     * @return Mage_Core_Helper_Abstract
     */
    private function getCommunicatorHelper($helper)
    {
        return Mage::helper($helper);
    }
}
