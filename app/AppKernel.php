<?php

use Symfony\Component\ClassLoader\DebugUniversalClassLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
use Sylius\Bundle\CoreBundle\Kernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),
            new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle(),
            
            new Kitpages\FileSystemBundle\KitpagesFileSystemBundle(),
            new Kitpages\CmsBundle\KitpagesCmsBundle(),
            new Kitpages\FileBundle\KitpagesFileBundle(),
            new Kitpages\SimpleCacheBundle\KitpagesSimpleCacheBundle(),
            new Kitpages\UtilBundle\KitpagesUtilBundle(),

            new App\KitPagesBundle\AppKitPagesBundle(),
            new App\SiteBundle\AppSiteBundle(),
            new App\PaymentBundle\AppPaymentBundle(),
            new App\UserBundle\AppUserBundle(),
            new App\LexikTranslationBundle\AppLexikTranslationBundle(),
            new App\SyliusWebBundle\AppSyliusWebBundle(),
            new App\SyliusCoreBundle\AppSyliusCoreBundle(),
            new A2lix\TranslationFormBundle\A2lixTranslationFormBundle(),
            new Nelmio\SolariumBundle\NelmioSolariumBundle(),
            new App\SolrSearchBundle\AppSolrSearchBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new App\SyliusProductBundle\AppSyliusProductBundle(),
            new App\CartBundle\AppCartBundle()
        );

        return array_merge(parent::registerBundles(), $bundles);
    }
}
