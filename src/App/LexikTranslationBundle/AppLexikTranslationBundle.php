<?php

namespace App\LexikTranslationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppLexikTranslationBundle extends Bundle
{
    public function getParent()
    {
        return 'LexikTranslationBundle';
    }    
}
