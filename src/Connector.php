<?php
namespace Heyday\Vend\SilverStripe;
/**
 * Class Connector
 * Making calls to the VendAPI to request the access token and refresh token
 */
class Connector extends \Object
{

    /**
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger = null)
    {
        $config = \SiteConfig::current_site_config();
        $vendShopName = $config->VendShopName;
        $this->url = "https://$vendShopName.vendhq.com/api/1.0/token";

        $this->logger = $logger;
    }

    /**
     * @param $body
     * @return bool
     * @throws ConnectorException
     */
    public function send($body)
    {

        $ch = curl_init($this->url);

        //body
        if (isset($body)) {
            $length = strlen($body);
        } else {
            $length = '0';
        }

        //setting the headers
        $headers[] = "Content-length: " . $length;
        $headers[] = "accept: application/json";
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //making the call
        if ($response = curl_exec($ch)) {
            curl_close($ch);
            $json = json_decode($response);
            return $this->setTokens($json);
        } else {
            throw new ConnectorException;
        }
    }

    /**
     * set the tokens inside Site Config
     * @param $json
     * @return bool
     * @throws ValidationException
     * @throws null
     */
    public function setTokens($json)
    {
        $config = \SiteConfig::current_site_config();
        $config->VendAccessToken = $json->access_token;
        $refresh_token = $json->refresh_token;
        if (isset($refresh_token) && !empty($refresh_token)) {
            $config->VendRefreshToken = $refresh_token;
        }

        $config->write();
        return true;
    }


}
