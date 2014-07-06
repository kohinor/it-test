<?php

namespace App\SyliusWebBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppSyliusWebBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusWebBundle';
    }    
}
