#!/bin/bash

if [ -f compropago.zip ]; then
    echo -e "\033[1;31mDeleting old file\033[0m"
    rm compropago.zip
fi

echo -e "\033[1;33mRemove .DS_Store files\033[0m"
find . -name ".DS_Store" -delete

echo -e "\033[1;32mBuilding zip plugin for WooCommerce\033[0m"
zip -r compropago.zip . -x "*.git*" "*.idea*" ".DS_Store" "build.sh"
