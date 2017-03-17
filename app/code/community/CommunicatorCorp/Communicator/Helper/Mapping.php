<?php

/*
 * CommunicatorCorp\Communicator
 *
 * Copyright © 2016 Rippleffect Studio Ltd
 * Rights reserved.
 *
 * PHP version 5.4+
 */

use CommunicatorCorp\Client\DateTime\DateTime;
use CommunicatorCorp\Client\ObjectDefinition\ColumnMapping;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Mapping helper.
 *
 * @package CommunicatorCorp\Communicator
 * @author  Daniel Morris <daniel@rippleffect.com>
 */
class CommunicatorCorp_Communicator_Helper_Mapping extends CommunicatorCorp_Communicator_Helper_Abstract
{
    /** @const string The configuration path to the contact email mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_CONTACT_EMAIL = 'communicator_mapping/contact/email';

    /** @const string The configuration path to the contact forename mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_CONTACT_FORENAME = 'communicator_mapping/contact/forename';

    /** @const string The configuration path to the contact surname mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_CONTACT_SURNAME = 'communicator_mapping/contact/surname';

    /** @const string The configuration path to the contact mobile mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_CONTACT_MOBILE = 'communicator_mapping/contact/mobile';

    /** @const string The configuration path to the contact last order date mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_CONTACT_LAST_ORDER_DATE = 'communicator_mapping/contact/last_order_date';

    /** @const string The configuration path to the product ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_PRODUCT_ID = 'communicator_mapping/product/product_id';

    /** @const string The configuration path to the product description mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_DESCRIPTION = 'communicator_mapping/product/product_description';

    /** @const string The configuration path to the product image mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_IMAGE = 'communicator_mapping/product/product_image';

    /** @const string The configuration path to the order quote ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_QUOTE_ID = 'communicator_mapping/order/quote_id';

    /** @const string The configuration path to the checkout URL mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_CHECKOUT_URL = 'communicator_mapping/order/checkout_url';

    /** @const string The configuration path to the order ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ORDER_ID = 'communicator_mapping/order/order_id';

    /** @const string The configuration path to the order description mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_DESCRIPTION = 'communicator_mapping/order/description';

    /** @const string The configuration path to the order status mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_STATUS = 'communicator_mapping/order/status';

    /** @const string The configuration path to the order email mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_EMAIL = 'communicator_mapping/order/email';

    /** @const string The configuration path to the order address line 1 mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_1 = 'communicator_mapping/order/address_1';

    /** @const string The configuration path to the order address line 2 mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_2 = 'communicator_mapping/order/address_2';

    /** @const string The configuration path to the order town mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOWN = 'communicator_mapping/order/town';

    /** @const string The configuration path to the order county mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_COUNTY = 'communicator_mapping/order/county';

    /** @const string The configuration path to the order postcode mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTCODE = 'communicator_mapping/order/postcode';

    /** @const string The configuration path to the order's first recommendation mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_RECOMMENDATION_1 = 'communicator_mapping/order/recommendation_1';

    /** @const string The configuration path to the order's second recommendation mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_RECOMMENDATION_2 = 'communicator_mapping/order/recommendation_2';

    /** @const string The configuration path to the order's third recommendation mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_RECOMMENDATION_3 = 'communicator_mapping/order/recommendation_3';

    /** @const string The configuration path to the order subtotal mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_SUBTOTAL = 'communicator_mapping/order/subtotal';

    /** @const string The configuration path to the order postage mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTAGE = 'communicator_mapping/order/postage';

    /** @const string The configuration path to the order VAT mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_VAT = 'communicator_mapping/order/vat';

    /** @const string The configuration path to the order total mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOTAL = 'communicator_mapping/order/total';

    /** @const string The configuration path to the order creation date mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_CREATED_AT = 'communicator_mapping/order/created_at';

    /** @const string The configuration path to the order item email mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_EMAIL = 'communicator_mapping/order_item/email';

    /** @const string The configuration path to the order item product name mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_NAME = 'communicator_mapping/order_item/product_name';

    /** @const string The configuration path to the order item quote ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUOTE_ID = 'communicator_mapping/order_item/quote_id';

    /** @const string The configuration path to the order item ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_ORDER_ITEM_ID = 'communicator_mapping/order_item/order_item_id';

    /** @const string The configuration path to the order item product ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_ID = 'communicator_mapping/order_item/product_id';

    /** @const string The configuration path to the order item shipping ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_SHIPPING_ID = 'communicator_mapping/order_item/shipping_id';

    /** @const string The configuration path to the order item quantity mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUANTITY = 'communicator_mapping/order_item/quantity';

    /** @const string The configuration path to the order item value mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_VALUE = 'communicator_mapping/order_item/value';

    /** @const string The configuration path to the order item total mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_TOTAL = 'communicator_mapping/order_item/total';

    /** @const string The configuration path to the shipping carrier mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_CARRIER = 'communicator_mapping/shipping/carrier';

    /** @const string The configuration path to the shipping email mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_EMAIL = 'communicator_mapping/shipping/email';

    /** @const string The configuration path to the shipping order ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_ORDER_ID = 'communicator_mapping/shipping/order_id';

    /** @const string The configuration path to the shipping ID mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_ID = 'communicator_mapping/shipping/shipping_id';

    /** @const string The configuration path to the shipping tracking number mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_TRACK_ID = 'communicator_mapping/shipping/track_id';

    /** @const string The configuration path to the shipping tracking number mapping. */
    const XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_TRACKING_NUMBER = 'communicator_mapping/shipping/tracking_number';

    /**
     * The property accessor.
     *
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * Creates a `ColumnMapping` object.
     *
     * @param int   $columnId    The column ID.
     * @param mixed $columnValue The column value.
     *
     * @return ColumnMapping
     *
     * @api
     */
    public static function createColumnMapping($columnId, $columnValue)
    {
        return (new ColumnMapping)
            ->setColumnId($columnId)
            ->setValue($columnValue)
        ;
    }

    /**
     * Returns the mapped columns for a given subscriber.
     *
     * @param Mage_Newsletter_Model_Subscriber $subscriber The subscriber.
     *
     * @return array
     */
    public function getSubscriberColumns(Mage_Newsletter_Model_Subscriber $subscriber)
    {
        $columns = $this->getColumns($subscriber, $this->getSubscriberConfigurationMap());

        $customerId = $subscriber->getCustomerId();

        if (!empty($customerId)) {
            return $columns;
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer = Mage::getModel('customer/customer')->load($customerId);

        array_merge($columns, $this->getCustomerColumns($customer));

        return $columns;
    }

    /**
     * Returns the mapped columns for a given customer.
     *
     * @param Mage_Customer_Model_Customer $customer The customer.
     *
     * @return array
     */
    public function getCustomerColumns(Mage_Customer_Model_Customer $customer)
    {
        return $this->getColumns($customer, $this->getCustomerConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given product.
     *
     * @param Mage_Catalog_Model_Product $product The product.
     *
     * @return ColumnMapping[]
     */
    public function getProductColumns(Mage_Catalog_Model_Product $product)
    {
        return $this->getColumns($product, $this->getProductConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given quote.
     *
     * @param Mage_Sales_Model_Quote $quote The quote.
     *
     * @return ColumnMapping[]
     */
    public function getQuoteColumns(Mage_Sales_Model_Quote $quote)
    {
        return $this->getColumns($quote, $this->getQuoteConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given quote item.
     *
     * @param Mage_Sales_Model_Quote_Item $quoteItem The quote.
     *
     * @return ColumnMapping[]
     */
    public function getQuoteItemColumns(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        return $this->getColumns($quoteItem, $this->getQuoteItemConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given order.
     *
     * @param Mage_Sales_Model_Order $order The order.
     *
     * @return array
     */
    public function getOrderColumns(Mage_Sales_Model_Order $order)
    {
        return $this->getColumns($order, $this->getOrderConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given order item.
     *
     * @param Mage_Sales_Model_Order_Item $orderItem The order item.
     *
     * @return ColumnMapping[]
     */
    public function getOrderItemColumns(Mage_Sales_Model_Order_Item $orderItem)
    {
        return $this->getColumns($orderItem, $this->getOrderItemConfigurationMap());
    }

    /**
     * Returns the mapped columns for a given track.
     *
     * @param Mage_Sales_Model_Order_Shipment_Track $track The track.
     *
     * @return ColumnMapping[]
     */
    public function getShipmentTrackColumns(Mage_Sales_Model_Order_Shipment_Track $track)
    {
        return $this->getColumns($track, $this->getShipmentTrackConfigurationMap());
    }

    /**
     * Returns an array of `ColumnMapping` objects from a given Varien object.
     *
     * @param Varien_Object $object           The object.
     * @param array         $configurationMap The configuration map.
     *
     * @return ColumnMapping[]
     */
    private function getColumns(Varien_Object $object, array $configurationMap)
    {
        $columns = array();
        $propertyAccessor = $this->getPropertyAccessor();

        foreach ($configurationMap as $attribute => $configurationPath) {
            $columnId = (int) Mage::getStoreConfig($configurationPath);

            if (!$propertyAccessor->isReadable($object, $attribute)) {
                continue;
            }

            $columnValue = $propertyAccessor->getValue($object, $attribute);

            // Image is an edge-case whereby we need to prepend the base URL.
            if ('image' === $attribute) {
                if ((null === $columnValue || 'no_selection' === $columnValue) && 'simple' === $object->getTypeId()) {
                    $configurableProductType = Mage::getResourceSingleton('catalog/product_type_configurable');
                    $configurableProductIds = $configurableProductType->getParentIdsByChild($object->getId());

                    if (is_array($configurableProductIds) && isset($configurableProductIds[0])) {
                        /** @var Mage_Catalog_Model_Product $configurableProduct */
                        $configurableProduct = Mage::getModel('catalog/product')->load($configurableProductIds[0]);
                        $columnValue = Mage::getModel('catalog/product_media_config')->getMediaUrl($configurableProduct->getImage());
                    }
                } else {
                    /** @var Mage_Catalog_Model_Product $object */
                    $columnValue = $this->getImageForProduct($object);
                }

                // If a simple product is added via the associated products section and then the configurable
                // product is saved, we retrieve the simple product collection and upsert them in one API call
                // to Communicator, each simple product will have it's columns retrieved by this method, but
                // within the condition above, this is so that a simple product uses the parent product image
                // if it does not have an image of it's own.
                if ('configurable' === $object->getTypeId()) {
                    /** @var Mage_Catalog_Model_Product_Type_Configurable $configurableProduct */
                    $configurableProduct = Mage::getModel('catalog/product_type_configurable')->setProduct($object);
                    $simpleProducts = $configurableProduct->getUsedProductCollection();

                    $this
                        ->getEventHelper()
                        ->upsertProducts($simpleProducts)
                    ;
                }
            }

            // Price is an edge-case whereby if the product is a child of a configurable product,
            // it may inherit the price, but in the object itself, the price is set to zero.
            if ('price' === $attribute && 'simple' === $object->getProductType() && $columnValue == 0) {
                $columnValue = $object->getParentItem()->getPrice();
            }

            // Row total is an edge-case whereby if the product is a child of a configurable product,
            // it may inherit the row total, but in the object itself, the row total is set to zero.
            if ('row_total' === $attribute && 'simple' === $object->getProductType() && $columnValue == 0) {
                $columnValue = $object->getParentItem()->getRowTotal();
            }

            // Telephone is an edge-case whereby the number must be validated and formatted before being sent to
            // Communicator, an incorrectly formatted telephone number can prevent persistence of
            // the customer record within Communicator.
            if ('primary_billing_address.telephone' === $attribute && !empty($columnValue)) {
                $columnValue = str_replace([' ', '+'], '', $columnValue);
                preg_match('/^(0|\+?44)(7[4,5,7,8,9]{1}\d{8})$/', $columnValue, $matches);

                $columnValue = isset($matches[2]) ? '44' . $matches[2] : '';
            }

            // Created at is an edge-case whereby we need to convert the
            // value to one that is accepted by the Communicator API.
            if ('created_at' === $attribute) {
                $columnValue = (new DateTime($columnValue))->format('d/m/Y H:i:s');
            }

            if (!empty($columnId) && isset($columnValue)) {
                $column = $this->createColumnMapping($columnId, $columnValue);
                array_push($columns, $column);
            }
        }

        return $columns;
    }

    /**
     * Returns the image for a product.
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string|null
     */
    private function getImageForProduct(Mage_Catalog_Model_Product $product)
    {
        /** @var Mage_Catalog_Helper_Image $imageHelper */
        $imageHelper = Mage::helper('catalog/image');

        try {
            $image = (string) $imageHelper->init($product, 'image');
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'communicator.log');
            return null;
        }

        return $image;
    }

    /**
     * Returns the configuration map for a subscriber.
     *
     * @return array
     */
    private function getSubscriberConfigurationMap()
    {
        return array(
            'email' => self::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_EMAIL,
        );
    }

    /**
     * Returns the configuration map for a customer.
     *
     * @return array
     */
    private function getCustomerConfigurationMap()
    {
        return array(
            'email'                             => self::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_EMAIL,
            'firstname'                         => self::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_FORENAME,
            'lastname'                          => self::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_SURNAME,
            'primary_billing_address.telephone' => self::XML_PATH_COMMUNICATOR_MAPPING_CONTACT_MOBILE,
        );
    }

    /**
     * Returns the configuration map for a product.
     *
     * @return array
     */
    private function getProductConfigurationMap()
    {
        return array(
            'id'    => self::XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_PRODUCT_ID,
            'name'  => self::XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_DESCRIPTION,
            'image' => self::XML_PATH_COMMUNICATOR_MAPPING_PRODUCT_IMAGE,
        );
    }

    /**
     * Returns the configuration map for an quote.
     *
     * @return array
     */
    private function getQuoteConfigurationMap()
    {
        return array(
            'customer_email'             => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_EMAIL,
            'entity_id'                  => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_QUOTE_ID,
            'subtotal'                   => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_SUBTOTAL,
            'tax_amount'                 => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_VAT,
            'shipping_amount'            => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTAGE,
            'grand_total'                => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOTAL,
            'shipping_address.street[0]' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_1,
            'shipping_address.street[1]' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_2,
            'shipping_address.city'      => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOWN,
            'shipping_address.region'    => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_COUNTY,
            'shipping_address.postcode'  => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTCODE,
            'created_at'                 => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_CREATED_AT,
        );
    }

    /**
     * Returns the configuration map for a quote item.
     *
     * @return array
     */
    private function getQuoteItemConfigurationMap()
    {
        return array(
            'name'        => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_NAME,
            'product_id'  => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_ID,
            'qty_ordered' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUANTITY,
            'row_total'   => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_TOTAL,
            'price'       => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_VALUE,
        );
    }

    /**
     * Returns the configuration map for an order.
     *
     * @return array
     */
    private function getOrderConfigurationMap()
    {
        return array(
            'customer_email'             => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_EMAIL,
            'increment_id'               => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ORDER_ID,
            'quote_id'                   => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_QUOTE_ID,
            'status'                     => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_STATUS,
            'state'                      => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_DESCRIPTION,
            'subtotal'                   => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_SUBTOTAL,
            'tax_amount'                 => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_VAT,
            'shipping_amount'            => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTAGE,
            'grand_total'                => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOTAL,
            'shipping_address.street[0]' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_1,
            'shipping_address.street[1]' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ADDRESS_2,
            'shipping_address.city'      => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_TOWN,
            'shipping_address.region'    => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_COUNTY,
            'shipping_address.postcode'  => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_POSTCODE,
            'created_at'                 => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_CREATED_AT,
        );
    }

    /**
     * Returns the configuration map for an order item.
     *
     * @return array
     */
    private function getOrderItemConfigurationMap()
    {
        return array(
            'name'        => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_NAME,
            'product_id'  => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_PRODUCT_ID,
            'qty_ordered' => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_QUANTITY,
            'row_total'   => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_TOTAL,
            'price'       => self::XML_PATH_COMMUNICATOR_MAPPING_ORDER_ITEM_VALUE,
        );
    }

    /**
     * Returns the configuration map for a shipment track.
     *
     * @return array
     */
    private function getShipmentTrackConfigurationMap()
    {
        return array(
            'title'                 => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_CARRIER,
            'email'                 => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_EMAIL,
            'order_increment_id'    => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_ORDER_ID,
            'shipment_increment_id' => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_ID,
            'entity_id'             => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_TRACK_ID,
            'track_number'          => self::XML_PATH_COMMUNICATOR_MAPPING_SHIPPING_TRACKING_NUMBER,
        );
    }

    /**
     * Returns the `PropertyAccessor`.
     *
     * @return PropertyAccessorInterface
     */
    private function getPropertyAccessor()
    {
        if (!isset($this->propertyAccessor)) {
            $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
                ->enableMagicCall()
                ->getPropertyAccessor()
            ;
        }

        return $this->propertyAccessor;
    }
}
