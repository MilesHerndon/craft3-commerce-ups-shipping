<?php
/**
 * Commerce UPS Shipping plugin for Craft CMS 3.x
 *
 * Plugin to receive UPS Shipping data
 *
 * @link      https://milesherndon.com
 * @copyright Copyright (c) 2019 MilesHerndon
 */

namespace milesherndon\commerceupsshipping\models;

use milesherndon\commerceupsshipping\CommerceUpsShipping;

use Craft;
use craft\base\Model;

/**
 * @author    MilesHerndon
 * @package   CommerceUpsShipping
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $shippingAddressZip = '';

    /**
     * @var string
     */
    public $upsShipperNumber = '';

    /**
     * @var string
     */
    public $upsLicenseNumber = '';

    /**
     * @var string
     */
    public $upsAccountUsername = '';

    /**
     * @var string
     */
    public $upsAccountPassword = '';

    /**
     * @var string
     */
    public $markup = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'shippingAddressZip',
                    'upsShipperNumber',
                    'upsLicenseNumber',
                    'upsAccountUsername',
                    'upsAccountPassword',
                    'markup'
                ],
                'required'
            ],
            ['shippingAddressZip', 'string'],
            ['upsShipperNumber', 'string'],
            ['upsLicenseNumber', 'string'],
            ['upsAccountUsername', 'string'],
            ['upsAccountPassword', 'string'],
            ['markup', 'number', 'min' => 0],
        ];
    }
}
