<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to get UPS shipping data.
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping\controllers;

use milesherndon\commerceupsshipping\CommerceUpsShipping;

use Craft;
use craft\web\Controller;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['register-shipping-methods'];

    // Public Methods
    // =========================================================================

    // /**
    //  * @return mixed
    //  */
    // public function actionIndex()
    // {
    //     $result = 'Welcome to the DefaultController actionIndex() method';

    //     return $result;
    // }

    // /**
    //  * @return mixed
    //  */
    // public function actionDoSomething()
    // {
    //     $result = 'Welcome to the DefaultController actionDoSomething() method';

    //     return $result;
    // }

    public function actionRegisterShippingMethods( $order=null )
    {
        if ($order && !$order->isEmpty())
        {
            $rates = craft()->craftCommerceUpsShipping_rates->getRates();

            $shippingMethods = [];

            foreach ($rates as $rate)
            {
                $shippingMethods[] = new ShippingMethod(['handle' => $rate->service, 'name' => $rate->name, 'description' => $rate->description], $order);
            }

            return $shippingMethods;

        }
    }
}
