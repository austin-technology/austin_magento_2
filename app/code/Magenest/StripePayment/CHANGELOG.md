# Change Log
All notable changes to this extension will be documented in this file.
This extension adheres to [Magenest](http://magenest.com/).


Stripe compatible with 
```
Magento Commerce 2.1.x, 2.2.x, 
Magento OpenSource 2.1.x, 2.2.x
```
## [2.2.0] - 2018-08-04
-   Add Stripe Library v6.13.0
-   Add Stripe WebHooks to get payment notification
-   Add Alipay Payments with Sources
-   Add Bancontact Payments with Sources
-   Add EPS Payments with Sources
-   Add DEAL Payments with Sources
-   Add Multibanco Payments with Sources
-   Add P24 Payments with Sources
-   Add SOFORT Payments with Sources

## [2.0.5] - 2018-05-10
### Added
-   Upgrade API to lastest version 2018-02-28
-   Working with all One Step Checkout
-   Stripe Element 
-   Stripe direct API
-   Stripe Microsoft Pay
-   Multiple language for stripe iframe
-   Option for Use customer save card in Backend order
### Fixed
-   Minify js library error
-   Fix bug Terms and Conditions at payment page 
-   error show save card section in customer_account
### Removed
-   Remove Bitcoin payment


## [2.0.0] - 2017-12-27
Stripe now compatible with 
```
Magento Commerce 2.1.x, 2.2.x, 
Magento OpenSource 2.1.x, 2.2.x
```
### Added
-   Improve security
-   Support: Stripe.js v3
-   Support: Apple Pay
-   Support: Android Pay(Pay with Google)
-   Support: Giro Pay
-   Support: Alipay
-   Add validate payment source when receive from customer
-   Stripe logger will stored in var/log/stripe
-   Add sort order option in backend
-   Add Payment Instruction text box in backend
-   Add support information in backend
### Fixed
-   Save card, delete card error
-   Fix bug response duplicated. 
### Removed
-   Remove dependency with Stripe Library (Don't need to run `composer require stripe/stripe-php`)
-   Remove option enable debug log

## [1.0.4] - 2017-17-16
### Added
-   User can save 3d secure card
### Fixed
-   Fix bug send email for customer
-   Fix bug order state
-   Fix bug show message error.
### Removed
-   Alipay (current not support)

## [1.0.3] - 2017-06-12
### Added
-   3d secure action
-   Admin payment
-   Payment with source
### Fixed
-   iframe payment
-   Fix bug shipping address

## [1.0.2] - 2017-05-19
### Added
-   3d secure check
### Fixed
-   iframe payment

## [1.0.1] - 2016-07-30
### Added
1. Magento 2.1 compatible

## [1.0.0] - 2016-06-15
### Added
1. Allow customers to checkout using Stripe Payment Gateway
2. Allow admins to easily tweak and manage payments via Stripe