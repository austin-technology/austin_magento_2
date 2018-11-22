<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 26/05/2016
 * Time: 14:57
 */

namespace Magenest\StripePayment\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class ConfigProvider implements ConfigProviderInterface
{
    protected $methodCodes = [
        StripePaymentMethod::CODE,
    ];

    protected $methods = [];

    protected $escaper;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        foreach ($this->methodCodes as $code) {
            $this->methods[$code] = $paymentHelper->getMethodInstance($code);
        }
    }

    public function getConfig()
    {
//        $config = [];
//        $config['payment']['stripe_payment']['uuuu'] = 'asdiajosidjaosd';
//
//        return $config;
    }
}
