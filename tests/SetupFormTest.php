<?php

namespace Heyday\Vend\Tests;

use Heyday\Vend\SilverStripe\SetupForm;
use SilverStripe\Control\Controller;
use SilverStripe\Dev\SapphireTest;

class SetupFormTest extends SapphireTest
{
    protected $usesDatabase = true;

    public function testRenderForm()
    {
        $form = new SetupForm(new Controller(), 'VendSetupForm');

        $this->assertNotNull($form);
        $this->assertNotNull($form->Fields());
    }
}
