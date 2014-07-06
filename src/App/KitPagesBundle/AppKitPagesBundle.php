<?php

namespace App\KitPagesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppKitPagesBundle extends Bundle
{
    public function getParent()
    {
        return 'KitpagesCmsBundle';
    }    
}
