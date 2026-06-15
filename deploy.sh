#!/bin/bash
# Deploy to local LAMP dev server + build zip
# Usage: ./deploy.sh
## REMEMBER, BUMP VERSION IN PACKAGE.JSON, FOR LANDING PAGE IF YOU BUMP VERSION IN PLUGIN HEADER!!!!
set -e

DEST="/opt/lampp/htdocs/wp-content/plugins/vive-ai"
SRC="$(dirname "$0")"

# Extract version from plugin header
VERSION=$(grep -oP "Version: \K[0-9.]+" "$SRC/vive-ai.php")
PLUGIN_TESTED=$(grep -oP "Tested up to: \K[0-9.]+" "$SRC/vive-ai.php")
README_STABLE=$(grep -oP "Stable tag: \K[0-9.]+" "$SRC/readme.txt")
README_TESTED=$(grep -oP "Tested up to: \K[0-9.]+" "$SRC/readme.txt")

echo "Validating..."

if [ "$README_STABLE" != "$VERSION" ]; then
  echo "❌ readme.txt Stable tag ($README_STABLE) != plugin Version ($VERSION)"
  exit 1
fi

if [ "$README_TESTED" != "$PLUGIN_TESTED" ]; then
  echo "❌ readme.txt Tested up to ($README_TESTED) != plugin Tested up to ($PLUGIN_TESTED)"
  exit 1
fi

echo "✅ readme.txt matches plugin header"

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
