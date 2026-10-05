# Goaffpro
Goaffpro is an affiliate marketing platform which enables your ecommerce store to increase sales by running a completely custom affiliate marketing program.

## Requirements
- Magento Open Source / Adobe Commerce 2.4.9 (compatible with the 2.4.7+ release line)
- PHP 8.3, 8.4 or 8.5

## Documentation
- [Merchant guide](./MERCHANT_GUIDE.md) — what the extension does, how it tracks referrals and orders, what data is sent to Goaffpro, and how to configure it.

# Installation instructions
## via Composer
In your magento home directory in the server run the following commands
```
composer require goaffpro/affiliatemarketing
bin/magento setup:upgrade
bin/magento cache:flush
```

## via Module zip upload
1. Download the module zip file from https://goaffpro.com/goaffpro-affiliate_marketing-latest.zip
2. Unzip the file in your magento install directory -> app -> code folder
eg. `/htdocs/app/code`
3. Run the following command in your magento home directory
```
bin/magento setup:upgrade
bin/magento cache:flush
```

