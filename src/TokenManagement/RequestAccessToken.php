<?php
namespace Heyday\Vend\SilverStripe;
/**
 * This will request a token from the Vend API. This should be called straight after the
 * shop owner authorise your application (called in VendAuthorise_Controller.php)
 * Class RequestVendAccessToken
 */
class RequestAccessToken extends \Object
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
     * @param $authcode
     * @throws SetupException
     */
    public function __construct($authcode)
    {
        $this->code = $authcode;
        $this->client_id = \Config::inst()->get('VendAPI', 'clientID');
        $this->client_secret = \Config::inst()->get('VendAPI', 'clientSecret');
        $this->redirect_uri = \Director::absoluteBaseURLWithAuth() . \Config::inst()->get('VendAPI', 'redirectURI');
        if (is_null($this->client_id) || is_null($this->client_secret)) {
            throw new SetupException;
        }
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
            "code=$this->code&client_id=$this->client_id&client_secret=$this->client_secret&grant_type=authorization_code&redirect_uri=$this->redirect_uri";
    }
}