#!/bin/bash
# Deploy to local LAMP dev server
# Usage: ./deploy.sh

set -e

DEST="/opt/lampp/htdocs/wp-content/plugins/leseo-ai"
SRC="$(dirname "$0")"

echo "Syncing files..."
rsync -av --delete \
    --exclude='node_modules' \
    --exclude='src' \
    --exclude='package.json' \
    --exclude='postcss.config.js' \
    --exclude='.gitignore' \
    --exclude='deploy.sh' \
    --exclude='plan.md' \
    "$SRC/" "$DEST/"

echo "Done. Refresh WordPress admin."
