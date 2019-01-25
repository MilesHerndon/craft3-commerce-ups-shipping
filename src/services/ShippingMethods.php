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
use milesherndon\commerceupsshipping\services\ShippingRules;

use Craft;
use craft\base\Component;
use craft\commerce\services\ShippingMethods as ShippingMethodsService;
use craft\commerce\elements\Order;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use craft\commerce\Plugin;
use craft\commerce\records\ShippingMethod as ShippingMethodRecord;
use craft\db\Query;
use yii\base\Exception;
use craft\commerce\base\ShippingMethod;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class ShippingMethods extends ShippingMethodsService
{
    // private $_rate;
    private $_handle;
    private $_name;
    private $_service;
    private $_order;
    private $_devMode;

    public function __construct()
    {
        // $this->_service = $service;
        // $this->_order = $order;
        // $this->_handle = $service['handle'];
        // $this->_name = $service['name'];
        // $this->_devMode = $devMode;
    }

    /**
     * Returns the name of this Shipping Method as displayed to the customer and in the control panel.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Craft Commerce UPS Shipping';
    }

    public function getIsEnabled(): bool
    {
        return true;
    }

    public function getHandle(): string
    {
        return 'commerce-ups-shipping';
    }

    public function getDescription(): string
    {
        return 'UPS Shipping plugin';
    }

    public function getType(): string
    {
        return 'Custom';
    }

    /**
     * Returns an array of rules that meet the `ShippingRules` interface.
     *
     * @return \Commerce\Interfaces\ShippingRules[] The array of ShippingRules
     */
    public function getShippingRules(): array
    {
        $cart = Plugin::getInstance()->getCarts()->getCart();

        if ($cart)
        {
            $rates = CommerceUpsShipping::getInstance()->CommerceUpsShippingService->getRates();

            $shippingMethods = [];
            $count = 1;

            foreach ($rates as $rate)
            {
                $shippingMethods[] = new ShippingRules([
                    'id' => $count,
                    'handle' => $rate->service,
                    'name' => $rate->name,
                    'description' => $rate->description,
                    'type' => $rate->type
                ], $cart);

                $count++;
            }

            echo '<pre>';
            var_dump($shippingMethods);
            echo '</pre>';
            die;

            return $shippingMethods;
        }
    }

    public function getAllShippingMethods(): array
    {

        var_dump('yo'); die;
        return $this->_shippingMethodsById;
    }

    public function getPriceForOrder(Order $order)
    {
        return 1;
    }

    public function matchOrder(Order $order): bool
    {
        return true;
    }
}
