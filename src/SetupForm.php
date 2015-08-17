<?php
namespace Heyday\Vend\SilverStripe;
/**
 * Class VendSetupForm
 */
class SetupForm extends \Form
{

    /**
     * @var array
     */
    private static $allowed_actions = array('doSave');

    /**
     * @param \Controller $controller
     */
    public function __construct($controller, $name)
    {
        $this->addExtraClass('vend-form');
        $this->controller = $controller;
        $config = \SiteConfig::current_site_config();
        $vendAccessToken = $config->VendAccessToken;
        $vendShopName = $config->VendShopName;
        $fields = new \FieldList();
        $actions = new \FieldList();
        $fields->add(
            new \LiteralField(
                'vend',
                "<h1>Vend Integration</h1>"
            )
        );
        if (!is_null($vendAccessToken) && !empty($vendAccessToken)) {
            $url = $this->getAuthURL();
            $fields->add(
                new \LiteralField(
                    'explanation',
                    "<p>you're all setup!<br> If you need to reauthenticate, click <a href='$url' target='_blank'>here</a></p>"
                )
            );
        } else {
            if (!is_null($vendShopName) && !empty($vendShopName)) {
                $url = $this->getAuthURL();
                $fields->add(
                    new \LiteralField(
                        'explanation',
                        "Please authenticate by clicking <a href='$url' target='_blank'>here</a>"
                    )
                );
            } else {
                $fields->add(
                    new \LiteralField(
                        'explanation',
                        "Please remember to set your app settings in a config file."
                    )
                );
                $fields->add(
                    new \TextField(
                        'VendShopName',
                        'Vend Shop Name (yourshopname.vendhq.com)'
                    )
                );
                $actions->push(new \FormAction('doSave', 'Save'));

            }
        }


        // Reduce attack surface by enforcing POST requests
        $this->setFormMethod('POST', true);

        parent::__construct($controller, $name, $fields, $actions);

    }

    /**
     * Returns the URL needed for shop owner authorisation
     * @return string
     */
    public function getAuthURL()
    {
        $clientID = \Config::inst()->get('VendAPI', 'clientID');
        $redirectURI = \Director::absoluteBaseURLWithAuth() . \Config::inst()->get('VendAPI', 'redirectURI');
        return "https://secure.vendhq.com/connect?response_type=code&client_id=$clientID&redirect_uri=$redirectURI";
    }

    /**
     * @param $data
     * @return null
     */
    public function doSave($data)
    {
        $shopName = $data['VendShopName'];
        $config = \SiteConfig::current_site_config();
        $config->VendShopName = $shopName;
        $config->write();
        $this->controller->redirectBack();
    }
}