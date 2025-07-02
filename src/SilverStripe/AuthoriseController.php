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
            $state = $this->request->getVar('state');

            if (isset($code) && !empty($code)) {
                // Validate the state parameter to prevent CSRF attacks
                if (!$this->validateState($state)) {
                    return 'Invalid state parameter. This could be a security issue. Please try again.';
                }

                if ($this->getFirstToken($code)) {
                    return $this->redirect('/admin/vend');
                } else {
                    return 'Failed to obtain access token from Vend. Please check your configuration and try again.';
                }
            } else {
                return 'Authorization code is missing. Please try the authorization process again.';
            }
        }

        return $this->redirect('/admin');
    }

    /**
     * Validates the state parameter against the stored session value
     * @param string $state
     * @return bool
     */
    private function validateState($state)
    {
        if (empty($state)) {
            return false;
        }

        $session = $this->request->getSession();
        $storedState = $session->get('vend_oauth_state');

        if (empty($storedState)) {
            return false;
        }

        // Clear the state from session after validation
        $session->clear('vend_oauth_state');

        return hash_equals($storedState, $state);
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
