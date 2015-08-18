<?php
namespace Heyday\Vend\SilverStripe;
/**
 * Class Token
 */
class VendToken extends \DataObject
{

    private static $db = array(
        'AccessToken' => 'Varchar(255)',
        'RefreshToken' => 'Varchar(255)',
        'AccessTokenExpiry' => 'Varchar(255)'
    );
}