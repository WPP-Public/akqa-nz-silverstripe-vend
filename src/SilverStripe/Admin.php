<?php

namespace Heyday\Vend\SilverStripe;

use Heyday\Vend\Exceptions\SetupException;
use SilverStripe\Admin\LeftAndMain;
use SilverStripe\View\Requirements;
use VendAPI\VendAPI;
use SilverStripe\Core\Config\Config;

/**
 * Class AvailabilityAdmin
 */
class Admin extends LeftAndMain
{

    /**
     * @var string
     */
    private static $url_base = "admin";

    /**
     * @var string
     */
    private static $url_segment = 'vend';

    /**
     * @var string
     */
    private static $url_rule = '/$Action/$ID/$OtherID';

    /**
     * @var string
     */
    private static $menu_title = 'Vend Admin';

    /**
     * @var int
     */
    private static $menu_priority = 0;

    /**
     * @var int
     */
    private static $url_priority = 50;

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'VendSetupForm'
    );

    /**
     * init method
     */
    public function init()
    {
        Requirements::css('heyday/silverstripe-vend:css/vend-admin.css');

        parent::init();
    }

    /**
     * @param Int $id
     * @param FieldList $fields
     * @return SetupForm
     * @throws SetupException
     */
    public function getEditForm($id = null, $fields = null)
    {
        $client_id = Config::inst()->get(VendAPI::class, 'clientID');
        $client_secret = Config::inst()->get(VendAPI::class, 'clientSecret');
        $redirect_uri = Config::inst()->get(VendAPI::class, 'redirectURI');

        if (is_null($client_id) || is_null($client_secret) || is_null($redirect_uri)) {
            throw new SetupException;
        }

        return new SetupForm($this, 'EditForm');
    }
}
