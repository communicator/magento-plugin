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
 * Newsletter observer.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_Observer_Newsletter extends CommunicatorCorp_Communicator_Model_Observer_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @param Varien_Event_Observer $observer The event observer.
     *
     * @return $this
     */
    public function observe(Varien_Event_Observer $observer)
    {
        if (!$this->getDataHelper()->isEnabled()) {
            return $this;
        }

        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        $subscriber = $observer->getSubscriber();

        $this
            ->getEventHelper()
            ->upsertSubscriber($subscriber)
        ;

        return $this;
    }
}
