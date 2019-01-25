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
// use milesherndon\commerceupsshipping\variables\CommerceUpsShippingVariable;
use milesherndon\commerceupsshipping\services\ShippingMethods as CommerceShippingMethods;
use craft\commerce\services\ShippingMethods;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\commerce\events\RegisterAvailableShippingMethodsEvent;

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
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'commerce-ups-shipping/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'commerce-ups-shipping/default/register-shipping-methods';
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        // Register our variables
        // Event::on(
        //     CraftVariable::class,
        //     CraftVariable::EVENT_INIT,
        //     function (Event $event) {
        //         // @var CraftVariable $variable
        //         $variable = $event->sender;
        //         $variable->set('commerceupsshipping', CommerceUpsShippingVariable::class);
        //     }
        // );

        Event::on(
            ShippingMethods::class,
            ShippingMethods::EVENT_REGISTER_AVAILABLE_SHIPPING_METHODS,
            function(RegisterAvailableShippingMethodsEvent $event) {
                $event->shippingMethods[] = new CommerceShippingMethods();
            }
        );

        $this->setComponents([
            'CommerceUpsShippingService' => \milesherndon\commerceupsshipping\services\CommerceUpsShippingService::class,
            'ShippingMethods' => \milesherndon\commerceupsshipping\services\ShippingMethods::class,
        ]);

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
