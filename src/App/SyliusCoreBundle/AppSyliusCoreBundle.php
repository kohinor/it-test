<?php

namespace App\SyliusCoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppSyliusCoreBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCoreBundle';
    }    
}
