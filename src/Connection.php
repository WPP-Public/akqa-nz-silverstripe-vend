<?php
namespace Heyday\Vend;

use VendAPI\VendAPI;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * Class Connector
 * Making calls to the VendAPI to request the access token and refresh token
 */
class Connection extends VendAPI
{
    /**
     * instantiating the VendAPI object and passing the required parameters
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
        $config = SiteConfig::current_site_config();
        $shopName = $config->VendShopName;
        $url = "https://$shopName.vendhq.com";
        parent::__construct($url, 'Bearer', $tokenManager->getToken());
    }

}
