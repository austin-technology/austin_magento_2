<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 07/05/2016
 * Time: 13:58
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Magenest_StripePayment',
    __DIR__
);
include_once 'lib/stripe-php/init.php';