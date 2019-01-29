<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to get UPS shipping data.
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping\services;

use milesherndon\commerceupsshipping\CommerceUpsShipping;

use Craft;
use craft\base\Component;
use craft\commerce\elements\Order;
use craft\base\Plugin;

$path = Craft::$app->getPlugins()->getPlugin('commerce-ups-shipping')->getBasePath();
$vendor_path = $path . '/vendor/ups.rate.class.php';
include_once($vendor_path);

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class ShippingRules extends Component
{
    private $_description;
    private $_price;
    private $_name;
    private $_handle;
    private $_type;
    private $_order;
    private $_service;
    private $_pluginSettings;

    /**
     * ShippingRule constructor.
     *
     * @param      $carrier
     * @param      $service
     * @param null $rate
     */

    public function __construct($service, $order)
    {
        $this->_service = $service['handle'];
        $this->_order = $order;
        $this->_description = $service['description'];
        $this->_name = $service['name'];
        $this->_handle = $service['handle'];
        $this->_type = $service['type'];
        $this->_pluginSettings = CommerceUpsShipping::getInstance()->getSettings();

        if (!$this->_order->shippingAddress) {
            return false;
        }

        $shipperLicenseNumber = $this->_pluginSettings->upsLicenseNumber;
        $shipperUsername      = $this->_pluginSettings->upsAccountUsername;
        $shipperPassword      = $this->_pluginSettings->upsAccountPassword;
        $shipperNumber        = $this->_pluginSettings->upsShipperNumber;
        $from_zip             = $this->_pluginSettings->shippingAddressZip;

        $objUpsRate = new \UpsShippingQuote();

        $accountInfo =(object)[
            'strAccessLicenseNumber' => $shipperLicenseNumber,
            'strUserId' => $shipperUsername,
            'strPassword' => $shipperPassword,
            'strShipperNumber' => $shipperNumber,
            'strShipperZip' => $from_zip
        ];

        $shippingAddress = $this->_order->shippingAddress;
        $strDestinationZip = $shippingAddress->zipCode;
        $strPackageLength = 0;
        $strPackageWidth = 0;
        $strPackageHeight = 0;
        $strPackageWeight = 0;
        $items = $this->_order->getLineItems();

        foreach ($items as $item) {
            $strPackageLength += $item->length > 0 ? $item->length : 1;
            $strPackageWidth += $item->width > 0 ? $item->width : 1;
            $strPackageHeight += $item->height > 0 ? $item->height : 1;
            $strPackageWeight += $item->weight > 0 ? $item->weight : 1;
        }

        $boolReturnPriceOnly = true;

        $result = $objUpsRate->GetShippingRate(
            $accountInfo,
            $strDestinationZip,
            $this->_service,
            $strPackageLength,
            $strPackageWidth,
            $strPackageHeight,
            $strPackageWeight,
            $boolReturnPriceOnly
        );

        if ($result) {
            $this->_price = $this->_markupCalc($result);
            $service['amount'] = $this->_price;
            return $service;
        }

        return false;
    }

    private function _markupCalc($price)
    {
        $markupPercent = $this->_pluginSettings->markup;
        $multiplier = 1 + ($markupPercent/100);
        $markedUpPrice = $price * $multiplier;
        return $markedUpPrice;
    }

    /**
     * Is this rule a match on the order? If false is returned, the shipping engine tries the next rule.
     *
     * @return bool
     */
    public function matchOrder(Order $order)
    {
        // if ($this->_price)
        // {
            return true;
        // }
    }

    /**
     * Is this shipping rule enabled for listing and selection
     *
     * @return bool
     */
    public function getIsEnabled()
    {
        return true;
    }

    /**
     * Stores this data as json on the orders shipping adjustment.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * Returns the percentage rate that is multiplied per line item subtotal.
     * Zero will not make any changes.
     *
     * @return float
     */
    public function getPercentageRate()
    {
        return 0.00;
    }

    /**
     * Returns the flat rate that is multiplied per qty.
     * Zero will not make any changes.
     *
     * @return float
     */
    public function getPerItemRate()
    {
        return 0.00;
    }

    /**
     * Returns the rate that is multiplied by the line item's weight.
     * Zero will not make any changes.
     *
     * @return float
     */
    public function getWeightRate()
    {
        return 0.00;
    }

    /**
     * Returns a base shipping cost. This is added at the order level.
     * Zero will not make any changes.
     *
     * @return float
     */
    public function getBaseRate()
    {
        return $this->_price;
    }

    /**
     * Returns a max cost this rule should ever apply.
     * If the total of your rates as applied to the order are greater than this, the baseShippingCost
     * on the order is modified to meet this max rate.
     *
     * @return float
     */
    public function getMaxRate()
    {
        return 0.00;
    }

    /**
     * Returns a min cost this rule should have applied.
     * If the total of your rates as applied to the order are less than this, the baseShippingCost
     * on the order is modified to meet this min rate.
     * Zero will not make any changes.
     *
     * @return float
     */
    public function getMinRate()
    {
        return 0.00;
    }

    /**
     * Returns a description of the rates applied by this rule;
     * Zero will not make any changes.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Returns a description of the rates applied by this rule;
     * Zero will not make any changes.
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    public function getPrice()
    {
        return $this->_price;
    }
}
