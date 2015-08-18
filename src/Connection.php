<?php
namespace Heyday\Vend\SilverStripe;
use VendAPI\VendAPI;

/**
 * Class Connector
 * Making calls to the VendAPI to request the access token and refresh token
 */
class Connection extends VendAPI
{

    /**
     *
     */
    public function __construct()
    {
        $config = \SiteConfig::current_site_config();
        $shopName = $config->VendShopName;
        $accesToken = $config->VendAccessToken;
        $url = "https://$shopName.vendhq.com";
        parent::__construct($url, 'Bearer', $accesToken);
    }

}