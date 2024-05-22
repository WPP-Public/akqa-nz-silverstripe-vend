<?php

namespace Heyday\Vend\SilverStripe;

use SilverStripe\ORM\DataExtension;

/**
 * Class VendConfig
 */
class SiteConfigExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $db = [
        'VendShopName' => 'Varchar(255)'
    ];
}
