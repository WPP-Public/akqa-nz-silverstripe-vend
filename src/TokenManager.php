<?php

namespace Heyday\Vend;

use Heyday\Vend\SilverStripe\VendToken;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use VendAPI\VendAPI;

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
        $config = SiteConfig::current_site_config();
        $vendShopName = $config->VendShopName;
        $this->url = "https://$vendShopName.vendhq.com";
        //config
        $this->client_id = Config::inst()->get(VendAPI::class, 'clientID');
        $this->client_secret = Config::inst()->get(VendAPI::class, 'clientSecret');
        $this->redirect_uri = Director::absoluteBaseURLWithAuth() . Config::inst()->get(VendAPI::class, 'redirectURI');
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
        $length = '0';

        if (isset($body) && !is_null($body)) {
            $length = strlen($body);
        }

        $headers = [];
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

        if ($this->hasTokenExpired($vendToken->AccessTokenExpiry)) { //if expired get new token
            $this->refreshToken();
            $vendToken = VendToken::get()->first();
        }

        return $vendToken->AccessToken;
    }

    /**
     * Checks if token has expired. Added a minute for extra safety
     * @param $tokenExpiry
     * @return bool
     */
    public static function hasTokenExpired($tokenExpiry)
    {
        $now = time() + 60;
        $expiry = $tokenExpiry;

        return ($expiry <= $now);
    }

    /**
     * Get the first token. Only ever called the first time from Authorise_Controller.php
     * @param $code
     * @return bool
     */
    public function getFirstToken($code)
    {
        $body = sprintf(
            "code=%s&client_id=%s&client_secret=%s&grant_type=authorization_code&redirect_uri=%s",
            $code,
            $this->client_id,
            $this->client_secret,
            $this->redirect_uri
        );

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
        $body = sprintf(
            "refresh_token=%s&client_id=%s&client_secret=%s&grant_type=refresh_token",
            $refresh_token,
            $this->client_id,
            $this->client_secret
        );

        return $this->send($body);
    }
}
