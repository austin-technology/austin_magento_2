<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14/11/2017
 * Time: 17:01
 */

namespace Magenest\StripePayment\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magenest\StripePayment\Helper\Config;

class Webhooks extends Action
{
    protected $_config;

    protected $stripeHelper;

    protected $sofortEventHandler;
    protected $multibancoEventHandler;

    public function __construct(
        Context $context,
        Config $config,
        \Magenest\StripePayment\Helper\Config $stripeHelper,
        \Magenest\StripePayment\Model\WebhookManager\SofortEventHandler $sofortEventHandler,
        \Magenest\StripePayment\Model\WebhookManager\MultibancoEventHandler $multibancoEventHandler
    ) {
        $this->_config = $config;
        $this->stripeHelper = $stripeHelper;
        $this->sofortEventHandler = $sofortEventHandler;
        $this->multibancoEventHandler = $multibancoEventHandler;
        parent::__construct($context);
    }

    public function execute()
    {
        \Stripe\Stripe::setApiKey($this->stripeHelper->getSecretKey());
        $endpoint_secret = $this->stripeHelper->getWebhooksSecret();

        $payload = @file_get_contents("php://input");
        $sig_header = isset($_SERVER["HTTP_STRIPE_SIGNATURE"])?$_SERVER["HTTP_STRIPE_SIGNATURE"]:"";
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            $object = $event->object;
            if($object == "event"){
                $response = $event->data->object;
                $objectType = $response->object;
                if($objectType == 'charge'){
                    $sourceType = isset($response->source->type)?$response->source->type:"";
                    if($sourceType == 'sofort'){
                        if(!$this->sofortEventHandler->handleResponse($response)){
                            http_response_code(400);
                            exit();
                        }
                    }
                }
                if($objectType == 'source'){
                    $sourceType = isset($response->type)?$response->type:"";
                    if($sourceType == 'multibanco'){
                        if(!$this->multibancoEventHandler->handleSource($response)){
                            http_response_code(400);
                            exit();
                        }
                    }
                }
            }
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        http_response_code(200);
    }
}