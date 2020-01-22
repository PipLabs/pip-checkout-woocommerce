# Cryptocurrency Payment Gateway for WooCommerce - Pip Checkout 

**Contributors:** piplabs
**Tags:** bitcoin, veil, digibyte, cryptocurrency, accept bitcoin, accept veil, accept digbyte, BTC, DGB, crypto woocommerce, crypto wordpress plugin
**Requires at least:** 3.0.1
**Tested up to:** 3.4
**Stable tag:** 4.3
**License:** GPLv2 or later
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

Easily accept cryptocurrency payments on your WordPress Ecommerce store. No redirecting to 3rd party intermediaries. Non-custodial. No merchant fees.

## Description
The Pip Checkout cryptocurrency gateway for WooCommerce makes it simple for your online store to accept cryptocurrencies like Bitcoin, Veil and Digitbyte on WordPress. Simply install and activate the plugin, paste in your wallet address(es) and click Save. You're now ready for peer to peer decentralised payments, that are 100% non-custodial, with no merchant fees, and no middlemen. 

Pip Checkout's WordPress plugin also removes the need to redirect your customers to a 3rd party site to complete payment. Everything takes place on your site thanks to our [PipTX API](https://docs.pip.cash) for generating quotes. Our algorithm ensures that you can easily match each order to its corresponding payment. The information used for POST request is amount, currency and wallet address(es), as detailed in the [API documentation.]

### Merchant setup:
1. Ensure plugin is installed and activated
2. Configure at WooCommerce > Settings > Payments > Pip Checkout
3. Paste in VEIL, BTC and DGB addresses (will only display those provided at checkout)
4. Ensure Enabled and that you are happy with Title and Description
5. Click Save
We recommend using a newly created receiving address for setup. This can be either a public or stealth address.

### Merchant experience:
1. When you get a "New order" email check your wallet for receiving address 
2. Ensure you have received the specified amount as shown in email
3. In WordPress go to WooCommerce > Orders
4. Open the corresponding order, it will show the same amount as specified in email
5. Once you've confirmed, change order status to "Completed" and you customer will be notified

### Customer experience:
1. Arrive at the Checkout and enter required information
2. Select Pip Checkout as payment method and click “Place Order”
3. Then pay exact amount of your chosen cryptocurrency to the address provided
4. You will have already received an email saying “Your Order Has Been Received”
5. Once merchant has confirmed payment, you will be notified by email of completed order

## Installation

1. Go to WordPress Plugins > Add New
2. Click Upload Plugin Zip File
3. Upload pip-checkout-woocommerce.zip file and click “Upload Now”
4. Go to Installed Plugins
5. Activate the “Pip Woocommerce Payment Gateway”

## Frequently Asked Questions

### What do I do if error message "Merchant has not configured their receiving address" is shown?
If you are the merchant, ensure you have correctly entered a receiving address for at least one cryptocurrency type.
If you are the customer, please contact the merchant to let them know in case they are unaware.

### Can I change the Payment Method label and description?
Of course, you can easily customise these at WooCommerce > Settings > Payments > Pip Checkout

### How does Pip Checkout ensure that I can match payments with orders?
Our API called PipTX houses an algorithm that ensures no quotes generated within a 15 minute sliding window are duplicates. This modifier is applied as the lowest possible decimal level to ensure no impact is seen on the USD (or whatever default currency) amount. 
