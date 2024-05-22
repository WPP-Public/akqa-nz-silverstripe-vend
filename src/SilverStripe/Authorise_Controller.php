<?php
namespace Heyday\Vend\SilverStripe;
/**
 * This controller is hit by Vend after Shop owner has authorised your app.
 * The authorisation code is used to make the first token request.
 * This token as well is saved in the siteconfig along with the refresh token
 * Class VendAuthorise_Controller
 */

use Heyday\Vend\TokenManager;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Control\Controller;

/**
 * Class Authorise_Controller
 * @package Heyday\Vend\SilverStripe
 */
class Authorise_Controller extends Controller
{
    /**
     * @var TokenManager
     */
    protected $tokenManager;

    /**
     * @param $tokenManager TokenManager
     */
    public function setTokenManager($tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return string
     */
    public function index()
    {
        if (Member::currentUserID() && Permission::check('ADMIN')) {
            $code = $this->request->getVar('code');
            if (isset($code) && !empty($code)) {
                if ($this->getFirstToken($code)) {
                    return $this->redirect('/admin/vend');
                }
            } else {
                return 'There has been an error';
            }
        }
        return $this->redirect('/admin');
    }

    /**
     * Gets first token and stores them
     * @param $code
     * @return bool
     */
    private function getFirstToken($code)
    {
        return $this->tokenManager->getFirstToken($code);
    }
}
