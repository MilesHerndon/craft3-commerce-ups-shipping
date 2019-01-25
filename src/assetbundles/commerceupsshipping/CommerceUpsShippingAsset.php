<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to get UPS shipping data.
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping\assetbundles\CommerceUpsShipping;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class CommerceUpsShippingAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@milesherndon/commerceupsshipping/assetbundles/commerceupsshipping/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/CommerceUpsShipping.js',
        ];

        $this->css = [
            'css/CommerceUpsShipping.css',
        ];

        parent::init();
    }
}
