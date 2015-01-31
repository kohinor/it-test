<?php


namespace App\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * ExceptionController.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExceptionController extends ContainerAware
{
    /**
     * Converts an Exception to a Response.
     *
     * @param FlattenException     $exception A FlattenException instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     * @param string               $format    The format to use for rendering (html, xml, ...)
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(FlattenException $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $this->container->get('request')->setRequestFormat($format);

        // the count variable avoids an infinite loop on
        // some Windows configurations where ob_get_level()
        // never reaches 0
        $count = 100;
        $currentContent = '';
        while (ob_get_level() && --$count) {
            $currentContent .= ob_get_clean();
        }

        $templating = $this->container->get('templating');
        $code = $exception->getStatusCode();
        
        $response = $templating->renderResponse(
            $this->findTemplate($templating, $format, $code, $this->container->get('kernel')->isDebug()),
            array(
                'status_code'    => $code,
                'status_text'    => Response::$statusTexts[$code],
                'exception'      => $exception,
                'logger'         => $logger,
                'currentContent' => $currentContent,
            )
        );
        if ($code != 400) {
            $this->sendException($exception);
        }
        $response->setStatusCode($code);
        $response->headers->replace($exception->getHeaders());

        return $response;
    }
    
    public function sendException(FlattenException $exception)
    {
        $internalEmail = $this->container->getParameter('email.to.exception');
        $fromEmail = $this->container->getParameter('email.from.exception');
        $subject = sprintf('Exception on italica:%s',$this->container->get('kernel')->getEnvironment());
        $body = sprintf('%s : %s: %s', $exception->getStatusCode(), Response::$statusTexts[$exception->getStatusCode()], $exception->getMessage());
        $mailer = \Swift_Message::newInstance();
        $message = $mailer->setSubject($subject)
                          ->setFrom($fromEmail)
                          ->setTo($internalEmail)
                          ->setBody($body)
                          ->setContentType('text/html');
        return $this->container->get('mailer')->send($message);
    }

    protected function findTemplate($templating, $format, $code, $debug)
    {
        $name = $debug ? 'exception' : 'error';
        if ($debug && 'html' == $format) {
            $name = 'exception_full';
        }

        // when not in debug, try to find a template for the specific HTTP status code and format
        if (!$debug) {
            $template = new TemplateReference('AppSiteBundle', 'Exception', $name.$code, $format, 'twig');
            if ($templating->exists($template)) {
                return $template;
            }
        }

        // try to find a template for the given format
        $template = new TemplateReference('TwigBundle', 'Exception', $name, $format, 'twig');
        if ($templating->exists($template)) {
            return $template;
        }

        // default to a generic HTML exception
        $this->container->get('request')->setRequestFormat('html');

        return new TemplateReference('TwigBundle', 'Exception', $name, 'html', 'twig');
    }
}
