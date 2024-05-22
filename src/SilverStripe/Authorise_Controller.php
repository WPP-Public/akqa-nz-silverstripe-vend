<?php

namespace Heyday\Vend\SilverStripe;

use Heyday\Vend\TokenManager;
use SilverStripe\Security\Permission;
use SilverStripe\Control\Controller;
use SilverStripe\Security\Security;

/**
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
        $member = Security::getCurrentUser();

        if ($member && Permission::checkMember($member, 'ADMIN')) {
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
