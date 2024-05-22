<?php

namespace Heyday\Vend\SilverStripe;

use SilverStripe\ORM\DataObject;

class VendToken extends DataObject
{
    private static $table_name = 'VendToken';

    private static $db = [
        'AccessToken' => 'Varchar(255)',
        'RefreshToken' => 'Varchar(255)',
        'AccessTokenExpiry' => 'Varchar(255)'
    ];
}
