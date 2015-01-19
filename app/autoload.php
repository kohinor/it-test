<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
define('AWS_DISABLE_CONFIG_AUTO_DISCOVERY', true);
$loader = require __DIR__.'/../vendor/autoload.php';

// Intl stubs.
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
}
//require_once __DIR__.'/../vendor/aws-sdk/sdk.class.php';
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/phpcr-odm/lib/Doctrine/ODM/PHPCR/Mapping/Annotations/DoctrineAnnotations.php');
AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
