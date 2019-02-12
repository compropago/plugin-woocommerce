if [ -f compropago.zip ]; then
    echo Delete old file
    rm compropago.zip
fi

echo Remove .DS_Store files
find . -name ".DS_Store" -delete

echo "Building tgz module for Magento"
zip -r compropago.zip . -x "*.git*" "*.idea*" ".DS_Store" "build.sh"