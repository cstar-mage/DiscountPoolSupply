bin/magento maintenance:enable
wait
rm -rf var/cache/* ; rm -rf var/page_cache/* ; rm -rf pub/static/* ; rm -rf var/view_preprocessed/* ; rm -rf generated/* ; rm -rf var/di/*
wait
bin/magento setup:upgrade
wait
bin/magento setup:di:compile
wait
bin/magento setup:static-content:deploy -f
wait
bin/magento maintenance:disable
