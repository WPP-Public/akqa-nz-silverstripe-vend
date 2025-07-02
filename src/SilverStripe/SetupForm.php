<?php

namespace Heyday\Vend\SilverStripe;

use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FormAction;
use SilverStripe\Core\Config\Config;
use SilverStripe\Control\Director;
use Heyday\Vend\SilverStripe\VendToken;
use VendAPI\VendAPI;

/**
 * Class VendSetupForm
 */
class SetupForm extends Form
{
    /**
     * @var array
     */
    private static $allowed_actions = ['doSave'];


    public function __construct($controller, $name)
    {
        $this->addExtraClass('vend-form');

        $this->controller = $controller;
        $config = SiteConfig::current_site_config();

        $vendToken = VendToken::get()->first();
        $vendAccessToken = false;
        $vendShopName = false;

        if ($vendToken) {
            $vendAccessToken = $vendToken->AccessToken;
            $vendShopName = $config->VendShopName;
        }

        $fields = FieldList::create([
            LiteralField::create(
                'vend',
                "<h1>Vend Integration</h1>"
            )
        ]);

        $actions = FieldList::create([
            FormAction::create('doSave', 'Save')
                ->addExtraClass('btn btn-primary')
        ]);

        if (!is_null($vendAccessToken) && !empty($vendAccessToken)) {
            $url = $this->getAuthURL();
            $fields->add(
                LiteralField::create(
                    'explanation',
                    sprintf(
                        "<div class='alert alert-success'>" .
                            "You're all setup!<br> If you need to reauthenticate then <a href='%s' target='_blank'>" .
                            "select this</a> to do so.</div>",
                        $url
                    )
                )
            );
        } else {
            if (!is_null($vendShopName) && !empty($vendShopName)) {
                $url = $this->getAuthURL();
                $fields->add(
                    LiteralField::create(
                        'explanation',
                        "<p>Please authenticate by <a href='$url' target='_blank'>selecting this</a>.</p>"
                    )
                );
            } else {
                $fields->add(
                    LiteralField::create(
                        'explanation',
                        "<p>Please remember to set your app settings in a config file.</p>"
                    )
                );
            }
        }

        $fields->add(
            TextField::create(
                'VendShopName',
                'Vend Shop Name (as in: <Name>.vendhq.com)',
                $vendShopName
            )
        );

        $this->setFormMethod('POST', true);

        parent::__construct($controller, $name, $fields, $actions);
    }

    /**
     * Returns the URL needed for shop owner authorisation
     */
    public function getAuthURL(): string
    {
        $clientID = Config::inst()->get(VendAPI::class, 'clientID');
        $redirectURI = Director::absoluteBaseURLWithAuth() . Config::inst()->get(VendAPI::class, 'redirectURI');

        // Generate a secure random state parameter (minimum 8 characters as required by Vend)
        $state = bin2hex(random_bytes(16)); // 32 characters, more than sufficient

        // Store the state in the session for validation
        $this->controller->getRequest()->getSession()->set('vend_oauth_state', $state);

        return sprintf(
            "https://secure.vendhq.com/connect?response_type=code&client_id=%s&redirect_uri=%s&state=%s",
            $clientID,
            $redirectURI,
            $state
        );
    }

    /**
     * @param $data
     * @return null
     */
    public function doSave($data)
    {
        $shopName = $data['VendShopName'];
        $config = SiteConfig::current_site_config();
        $config->VendShopName = $shopName;
        $config->write();
        $this->controller->redirectBack();
    }
}
