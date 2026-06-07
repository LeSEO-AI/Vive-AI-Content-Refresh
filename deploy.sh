#!/bin/bash
# Deploy to local LAMP dev server + build zip
# Usage: ./deploy.sh

set -e

DEST="/opt/lampp/htdocs/wp-content/plugins/vive-ai"
SRC="$(dirname "$0")"

# Extract version from plugin header
VERSION=$(grep -oP "VIVE_VERSION', '\K[^']+" "$SRC/vive-ai.php")

echo "Syncing files..."
rsync -av --delete \
    --exclude='node_modules' \
    --exclude='src' \
    --exclude='package.json' \
    --exclude='postcss.config.js' \
    --exclude='.gitignore' \
    --exclude='deploy.sh' \
    --exclude='plan.md' \
    --exclude='README.md' \
    --exclude='*.zip' \
    "$SRC/" "$DEST/"

echo "Building vive-ai-${VERSION}.zip..."
cd "$SRC"
zip -r "/home/davilex/Desktop/wp-test/vive-ai-${VERSION}.zip" . \
    -x ".git" ".git/*" "src/*" "package.json" "postcss.config.js" \
    ".gitignore" "deploy.sh" "plan.md" "README.md" "*.zip"

echo "Done. Zip: vive-ai-${VERSION}.zip"
