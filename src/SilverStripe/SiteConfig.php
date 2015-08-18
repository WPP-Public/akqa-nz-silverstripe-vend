<?php
namespace Heyday\Vend\SilverStripe;
/**
 * Class VendConfig
 */
class SiteConfig extends \DataExtension {

    /**
     * @var array
     */
    private static $db = array(
        'VendShopName' => 'Varchar(255)'
    );

}