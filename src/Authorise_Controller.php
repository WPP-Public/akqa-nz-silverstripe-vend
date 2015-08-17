<?php
namespace Heyday\Vend\SilverStripe;
/**
 * This controller is hit by Vend after Shop owner has authorised your app.
 * The authorisation code is used to make the first token request.
 * This token as well is saved in the siteconfig along with the refresh token
 * Class VendAuthorise_Controller
 */
class Authorise_Controller extends \Controller
{

    /**
     * @return string
     */
    public function index()
    {
        if (\Member::currentUserID() && \Permission::check('ADMIN')) {
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
     * Get tokens and store them
     * @param $code
     */
    private function getFirstToken($code)
    {
        $request = \Object::create('Heyday\Vend\SilverStripe\RequestAccessToken', $code);
        return $request->get();

    }
}