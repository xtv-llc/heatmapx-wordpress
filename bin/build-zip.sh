#!/usr/bin/env bash
# Build the distributable plugin zip (dev files excluded). No rm: zip -FS syncs.
set -euo pipefail
cd "$(dirname "$0")/.."
VERSION=$(grep -m1 '\* Version:' heatmapx.php | sed 's/.*Version: *//' | tr -d '[:space:]')
mkdir -p dist/stage
# Stage the repo under a top-level heatmapx/ dir (WordPress expects one) via symlink.
ln -sfn "$(pwd)" dist/stage/heatmapx
(
  cd dist/stage
  zip -FS -r "../heatmapx-${VERSION}.zip" \
    heatmapx/heatmapx.php heatmapx/uninstall.php heatmapx/readme.txt \
    heatmapx/includes \
    -x '*.DS_Store'
)
echo "Built dist/heatmapx-${VERSION}.zip"
unzip -l "dist/heatmapx-${VERSION}.zip"
