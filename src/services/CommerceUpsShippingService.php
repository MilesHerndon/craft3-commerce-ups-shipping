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
use craft\commerce\services\ShippingMethods;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class CommerceUpsShippingService extends ShippingMethods
{
    private $_shipmentsBySignature;

    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->_shipmentsBySignature = [];
    }

    /*
     * @return array
     */
    public function getRates()
    {
        $rates = [
            (object)[
                'service'=>'gnd',
                'name'=>'UPS Ground',
                'description'=>'',
                'type'=>'custom'
            ],
            (object)[
                'service'=>'3ds',
                'name'=>'UPS Three Day Select',
                'description'=>'Please allow one business day for packaging',
                'type'=>'custom'
            ],
            (object)[
                'service'=>'2da',
                'name'=>'UPS Second Day Air',
                'description'=>'Please allow one business day for packaging',
                'type'=>'custom'
            ],
            (object)[
                'service'=>'1da',
                'name'=>'UPS Next Day Air',
                'description'=>'Please allow one business day for packaging',
                'type'=>'custom'
            ],
        ];

        return $rates;
    }
}
