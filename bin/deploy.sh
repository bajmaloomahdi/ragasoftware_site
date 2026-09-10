#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Server-side deploy step for ragasoftware_site (called by .cpanel.yml).
#
# Auto-detects composer + php across the paths cPanel commonly uses, installs
# production dependencies, and rebuilds Laravel's caches. Fails loudly (visible
# in the cPanel deploy log) if composer or php cannot be found.
#
# Run manually if needed:  bash ~/public_html/bin/deploy.sh
# ---------------------------------------------------------------------------
set -euo pipefail

cd "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
echo "==> deploy: $(pwd)"

find_bin() {
    # $1 = command name, rest = candidate absolute paths
    local name="$1"; shift
    local p
    if p="$(command -v "$name" 2>/dev/null)"; then echo "$p"; return 0; fi
    for p in "$@"; do
        [ -x "$p" ] && { echo "$p"; return 0; }
    done
    return 1
}

PHP="$(find_bin php \
    /usr/local/bin/php \
    "$HOME/bin/php" \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /opt/cpanel/ea-php84/root/usr/bin/php \
    /usr/local/bin/ea-php83 \
    /usr/bin/php)" || { echo "ERROR: php CLI not found — set PATH in .cpanel.yml or run composer manually"; exit 1; }

COMPOSER="$(find_bin composer \
    /opt/cpanel/composer/bin/composer \
    /usr/local/bin/composer \
    "$HOME/bin/composer" \
    "$HOME/composer.phar" \
    ./composer.phar)" || { echo "ERROR: composer not found — install it or run 'composer install' in cPanel Terminal once"; exit 1; }

echo "==> php:      $PHP  ($("$PHP" -r 'echo PHP_VERSION;' 2>/dev/null))"
echo "==> composer: $COMPOSER"

# A .phar needs the php interpreter in front of it.
case "$COMPOSER" in
    *.phar) COMPOSER="$PHP $COMPOSER" ;;
esac

export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=-1

echo "==> composer install --no-dev --optimize-autoloader"
$COMPOSER install --no-dev --optimize-autoloader --no-interaction --prefer-dist

echo "==> rebuilding caches"
"$PHP" artisan optimize:clear
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache

echo "==> deploy finished OK"
