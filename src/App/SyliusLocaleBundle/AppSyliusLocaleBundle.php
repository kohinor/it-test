<?php

namespace App\SyliusLocaleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppSyliusLocaleBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusLocaleBundle';
    }    
}
