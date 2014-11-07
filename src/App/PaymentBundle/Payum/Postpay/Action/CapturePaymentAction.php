<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace App\PaymentBundle\Payum\Postpay\Action;


use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\CaptureRequest;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Action\PaymentAwareAction;
use Payum\Core\Request\PostRedirectUrlInteractiveRequest;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Request\GetHttpQueryRequest;

class CapturePaymentAction extends PaymentAwareAction 
{
    protected $options;
    
    protected $securityVerifier;


    public function setOptions($options = array())
    {
        $this->options = $options;
    }
    
    public function setVerifier($verifier)
    {
        $this->securityVerifier = $verifier;
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        
        /** @var $request CaptureRequest */
        if (false == $this->supports($request)) {
            throw RequestNotSupportedException::createActionNotSupported($this, $request);
        }

        $payment = $request->getModel();
        
        $getHttpQuery = new GetHttpQueryRequest();
        $this->payment->execute($getHttpQuery);
        
        if ($payment->getDetails() && isset($getHttpQuery['status'])) {
            return;
        } else {
            $this->composeDetails($payment, $request->getToken());
            $this->securityVerifier->invalidate($request->getToken());
            throw new PostRedirectUrlInteractiveRequest(
                $this->options['redirectUrl'],
                $payment->getDetails()
            );
        }       
        
        
    }

    /**
     * @param PaymentInterface $payment
     * @param TokenInterface   $token
     *
     * @throws LogicException
     */
    protected function composeDetails(PaymentInterface $payment, TokenInterface $token)
    {
        $order = $payment->getOrder();
        
        $details = array();
        
        $details['PSPID'] = $this->options['pspid'];
        $details['USERID'] = $this->options['userId'];
        
        $details['ORDERID'] = $order->getNumber();
        $details['AMOUNT'] = $order->getTotal();
        $details['CURRENCY'] = $payment->getCurrency();
        $details['LANGUAGE'] = 'EN';
        
        $details['CN'] = $order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName();
        $details['EMAIL'] = $order->getEmail();
        $details['OWNERZIP'] = $order->getBillingAddress()->getPostcode();
        $details['OWNERADDRESS'] = $order->getBillingAddress()->getStreet();
        $details['OWNERCTY'] = $order->getBillingAddress()->getCountry()->getName();
        $details['OWNERTOWN'] = $order->getBillingAddress()->getCity();
        $details['OWNERTELNO'] = $order->getBillingAddress()->getPhoneNumber();
        //$details['COM'] = '';
        
        $details['TITLE'] = 'Italica Online Store';
        //$details['BGCOLOR'] = '';
        //$details['TXTCOLOR'] = '';
        //$details['TBLBGCOLOR'] = '';
        //$details['TBLTXTCOLOR'] = '';
        //$details['BUTTONBGCOLOR'] = '';
        //$details['BUTTONTXTCOLOR'] = '';
        //$details['FONTTYPE'] = '';
        //$details['LOGO'] = '';
        //$details['TP'] = '';
        
        $details['ACCEPTURL'] = $token->getAfterUrl();
        $details['DECLINEURL'] = $token->getAfterUrl();
        $details['EXCEPTIONURL'] = $token->getAfterUrl();
        $details['CANCELURL'] = $token->getAfterUrl();
        $details['BACKURL'] = $this->options['cartUrl'];
        $details['HOMEURL'] = $this->options['homeUrl'];
        $details['CATALOGURL'] = $this->options['homeUrl'];
        
        //$details['PM'] = '';
        //$details['BRAND'] = '';
        $details['WIN3DS'] = 'MAINW';
        $details['OPERATION'] = 'SAL';
        
        $details['SHASIGN'] = $this->calculateHash($details);
        //$details['ALIAS'] = '';
        //$details['ALIASUSAGE'] = '';
        //$details['ALIASOPERATION'] = '';
        
        //$details['COMPLUS'] = '';
        //$details['PARAMPLUS'] = '';
        
        //$details['CREDITCODE'] = '';

        $payment->setDetails($details);
    }
    

    private function calculateHash(array $params)
    {
        #Alpha sort
        ksort($params);

        $clearString = $this->options['password'];
        foreach ($params as $key => $value) {
            $clearString .= $key . '=' . $value . $this->options['password'];
        }

        return hash('sha256', $clearString);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof CaptureRequest &&
            $request->getModel() instanceof PaymentInterface
        ;
    }

}
