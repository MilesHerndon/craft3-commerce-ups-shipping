<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to get UPS shipping data.
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping;

use milesherndon\commerceupsshipping\services\CommerceUpsShippingService;
use milesherndon\commerceupsshipping\models\Settings;
use milesherndon\commerceupsshipping\base\UpsGroundShippingMethod;
use milesherndon\commerceupsshipping\base\UpsNextDayAirShippingMethod;
use milesherndon\commerceupsshipping\base\UpsSecondDayAirShippingMethod;
use milesherndon\commerceupsshipping\base\UpsThreeDaySelectShippingMethod;
use craft\commerce\services\ShippingMethods;

use Craft;
use craft\base\Plugin;
use craft\base\Model;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;
use craft\commerce\events\RegisterAddressRulesEvent;
use craft\commerce\models\Address;

use yii\base\Event;

/**
 * Class CommerceUpsShipping
 *
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 *
 * @property  CommerceUpsShippingServiceService $commerceUpsShippingService
 */
class CommerceUpsShipping extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CommerceUpsShipping
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                // @var CraftVariable $variable
                $variable = $event->sender;
                $variable->set('commerceupsshipping', CommerceUpsShippingVariable::class);
            }
        );

        Event::on(
            ShippingMethods::class,
            ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS,
            function(RegisterAvailableShippingMethodsEvent $event) {
                $event->shippingMethods[] = new UpsGroundShippingMethod();
                $event->shippingMethods[] = new UpsNextDayAirShippingMethod();
                $event->shippingMethods[] = new UpsSecondDayAirShippingMethod();
                $event->shippingMethods[] = new UpsThreeDaySelectShippingMethod();
            }
        );

        Event::on(Address::class, Model::EVENT_DEFINE_RULES, function(RegisterAddressRulesEvent $event) {
             $event->rules[] = [['firstName', 'lastName', 'address1', 'city', 'stateId', 'zipCode',], 'required'];
        });

        Event::on(Address::class, Address::EVENT_BEFORE_VALIDATE, function (Event $event) {
            $address = $event->sender;

            if (empty($address->firstName)) {
                $address->addError('firstName', Craft::t('app', 'First Name is required'));
                $event->handled = true;
            }

            if (empty($address->lastName)) {
                $address->addError('lastName', Craft::t('app', 'Last Name is required'));
                $event->handled = true;
            }

            if (empty($address->address1)) {
                $address->addError('address1', Craft::t('app', 'Address Line 1 is required'));
                $event->handled = true; // stop other handlers for this event
            }

            if (empty($address->city)) {
                $address->addError('city', Craft::t('app', 'City is required'));
                $event->handled = true;
            }

            if (empty($address->state)) {
                $address->addError('stateId', Craft::t('app', 'State is required'));
                $event->handled = true;
            }

            if (empty($address->zipCode)) {
                $address->addError('zipCode', Craft::t('app', 'Zip Code is required'));
                $event->handled = true;
            }
        });

        Craft::info(
            Craft::t(
                'commerce-ups-shipping',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        return Craft::$app->view->renderTemplate(
            'commerce-ups-shipping/settings',
            [
                'settings' => $this->getSettings()
            ]
        );
    }
}
