<?php
namespace Heyday\Vend\SilverStripe;
/**
 * Class VendConfig
 */
class Config extends \DataExtension {

    /**
     * @var array
     */
    private static $db = array(
        'VendShopName' => 'Varchar(255)',
        'VendAccessToken' => 'Varchar(255)',
        'VendRefreshToken' => 'Varchar(255)'
    );

}