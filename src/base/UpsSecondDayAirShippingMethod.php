<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to get UPS shipping data.
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping\base;

use milesherndon\commerceupsshipping\CommerceUpsShipping;
use milesherndon\commerceupsshipping\services\ShippingRules;

use Craft;
use craft\base\Component;
use craft\commerce\elements\Order;
use craft\commerce\base\ShippingMethod;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class UpsSecondDayAirShippingMethod extends ShippingMethod
{
    /**
     * Returns the name of this Shipping Method as displayed to the customer and in the control panel.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'UPS Second Day Air';
    }

    public function getIsEnabled(): bool
    {
        return true;
    }

    public function getHandle(): string
    {
        return '2da';
    }

    public function getDescription(): string
    {
        return 'Please allow one business day for packaging';
    }

    public function getType(): string
    {
        return 'Custom';
    }

    public function getPriceForOrder(Order $order)
    {
        return $this->getMatchingShippingRule($order)->getPrice();
    }

    public function matchOrder(Order $order): bool
    {
        return true;
    }

    public function getMatchingShippingRule(Order $order)
    {
        return new ShippingRules([
                    'handle' => $this->getHandle(),
                    'name' => $this->getName(),
                    'description' => $this->getDescription(),
                    'type' => $this->getType()
                ], $order);
    }
}
