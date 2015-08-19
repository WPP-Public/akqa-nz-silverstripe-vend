<?php
namespace Heyday\Vend;

use Heyday\Vend\SilverStripe\VendToken;

/**
 * This class is responsible for returning the token,
 * saving it into the database,
 * getting the first ever token after setup,
 * and refreshing the token if expired
 * Class TokenManager
 *
 */
class TokenManager
{

    /**
     * Loading all the neede variables
     * @throws Exceptions\SetupException
     */
    public function __construct()
    {
        $this->config = \SiteConfig::current_site_config();
        $vendShopName = $this->config->VendShopName;
        $this->url = "https://$vendShopName.vendhq.com";
        //config
        $this->client_id = \Config::inst()->get('VendAPI', 'clientID');
        $this->client_secret = \Config::inst()->get('VendAPI', 'clientSecret');
        $this->redirect_uri = \Director::absoluteBaseURLWithAuth() . \Config::inst()->get('VendAPI', 'redirectURI');
        if (is_null($this->client_id) || is_null($this->client_secret)) {
            throw new Exceptions\SetupException;
        }
    }

    /**
     * Making the curl call to the api and calling setTokens() on success
     * @param $body
     * @return bool
     * @throws Exceptions\TokenException
     */
    public function send($body)
    {
        $ch = curl_init($this->url . '/api/1.0/token');

        if (isset($body) && !is_null($body)) {
            $length = strlen($body);
        } else {
            $length = '0';
        }

        //setting the headers
        $headers[] = "Content-length: " . $length;
        $headers[] = "accept: application/json";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //making the call
        if ($response = curl_exec($ch)) {
            curl_close($ch);
            $json = json_decode($response);
            if (isset($json->error)) {
                throw new Exceptions\TokenException($json->error);
            }
            return $this->setTokens($json);
        } else {
            throw new Exceptions\TokenException('Curl call failed');
        }
    }

    /**
     * set the tokens in the DB
     * @param $json
     * @return bool
     * @throws null
     */
    public function setTokens($json)
    {
        $vendToken = VendToken::get()->first();
        if (is_null($vendToken)) {
            $vendToken = new VendToken();
        }
        $vendToken->AccessToken = $json->access_token;
        $vendToken->AccessTokenExpiry = $json->expires;
        $refresh_token = $json->refresh_token;
        if (isset($refresh_token) && !empty($refresh_token)) {
            $vendToken->RefreshToken = $refresh_token;
        }
        $vendToken->write();
        return true;
    }

    /**
     * Return the token, refresh it if expired
     * @return mixed
     */
    public function getToken()
    {
        $vendToken = VendToken::get()->first();
        $now = time();
        if ($vendToken->AccessTokenExpiry < $now) { //if expired get new token
            $this->refreshToken();
            $vendToken = VendToken::get()->first();
        }
        return $vendToken->AccessToken;

    }

    /**
     * Get the first token. Only ever called the first time from Authorise_Controller.php
     * @param $code
     * @return bool
     */
    public function getFirstToken($code)
    {
        $body = "code=$code&client_id=$this->client_id&client_secret=$this->client_secret&grant_type=authorization_code&redirect_uri=$this->redirect_uri";
        return $this->send($body);
    }


    /**
     * Refresh the token
     * @return bool
     */
    public function refreshToken()
    {
        $vendToken = VendToken::get()->first();
        $refresh_token = $vendToken->RefreshToken;
        $body = "refresh_token=$refresh_token&client_id=$this->client_id&client_secret=$this->client_secret&grant_type=refresh_token";
        return $this->send($body);
    }


}
