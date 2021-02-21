# Goaffpro
Goaffpro is an affiliate marketing platform which enables your ecommerce store to increase sales by running a completely custom affiliate marketing program.

# Installation instructions
## via Composer
In your magento home directory in the server run the following commands
```
composer require goaffpro/affiliatemarketing:1.0.1
bin/magento-cli setup:upgrade
bin/magento-cli cache:flush
```

## via Module zip upload
1. Download the module zip file from https://goaffpro.com/goaffpro-affiliate_marketing-1.0.0.zip
2. Unzip the file in your magento install directory -> app -> code folder
eg. `/htdocs/app/code`
3. Run the following command in your magento home directory
```
bin/magento-cli setup:upgrade
bin/magento-cli cache:flush
```

