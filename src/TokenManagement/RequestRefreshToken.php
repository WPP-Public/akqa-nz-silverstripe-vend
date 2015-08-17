<?php
namespace Heyday\Vend\SilverStripe;
class RequestRefreshToken extends Object
{

    /**
     * @var
     */
    protected $connector;

    /**
     * DI VendConnector.php
     * @param $connector
     */
    public function setConnector($connector)
    {
        $this->connector = $connector;
    }

    /**
     *
     */
    public function __construct()
    {
        $this->client_id = \Config::inst()->get('VendAPI', 'clientID');
        $this->client_secret = \Config::inst()->get('VendAPI', 'clientSecret');
        $config = \SiteConfig::current_site_config();
        $this->refresh_token = $config->VendRefreshToken;
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->connector->send($this->body());
    }

    /**
     * @return string
     */
    private function body()
    {
        return
            "refresh_token=$this->refresh_token&client_id=$this->client_id&client_secret=$this->client_secret&grant_type=refresh_token";
    }
}