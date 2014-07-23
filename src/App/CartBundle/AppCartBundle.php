<?php

namespace App\CartBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppCartBundle extends Bundle
{
    public function getParent()
    {
        return 'SyliusCartBundle';
    }    
}
