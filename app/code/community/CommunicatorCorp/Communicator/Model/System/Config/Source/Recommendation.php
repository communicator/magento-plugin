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
 * Source for recommendation sources.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Model_System_Config_Source_Recommendation extends CommunicatorCorp_Communicator_Model_System_Config_Source_Abstract
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    protected function getData()
    {
        $options = array();

        foreach (array(
            'getCrossSellProductIds' => 'Cross-sells',
            'getRelatedProductIds'   => 'Related products',
            'getUpSellProductIds'    => 'Up-sells'
        ) as $recommendationMethod => $source) {
            $options[] = (new ClientTableColumn)
                ->setId($recommendationMethod)
                ->setName($source)
            ;
        }

        return $options;
    }
}
