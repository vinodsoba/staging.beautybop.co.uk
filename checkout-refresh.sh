#!/bin/bash

echo ""
echo "🛒 BeautyBop Checkout Refresh"
echo "============================"

echo "🧹 Removing checkout static assets..."
rm -rf pub/static/frontend/beautybop/bbop/en_GB/Magento_Checkout
rm -rf var/view_preprocessed

echo "🧼 Cleaning cache..."
php bin/magento cache:clean

echo "🚀 Deploying checkout assets..."
php bin/magento setup:static-content:deploy -f en_GB

echo ""
echo "✅ Ready to test!"