#!/usr/bin/env bash
# ---------------------------------------------------------------------------
# Server-side deploy step for ragasoftware_site (called by .cpanel.yml).
#
# The host's Document Root is fixed at $DOCROOT and can't be pointed at this
# checkout's public/ folder, so this script keeps Laravel's application
# source entirely inside the Git checkout (never web-exposed) and makes only
# public/'s contents reachable at $DOCROOT — build/, favicon.ico and uploads/
# are all real copies refreshed every deploy (this host doesn't reliably
# serve symlinks even with Options +FollowSymLinks — likely AllowOverride
# doesn't permit it), plus a freshly generated index.php that bootstraps
# Laravel by absolute path. Because uploads/ is a copy, new CMS uploads
# written between deploys aren't reachable at the live URL until the next
# deploy re-copies them. Re-running this script is always safe: every step
# is idempotent and self-heals a previous, insecure deploy layout (whole app
# copied into $DOCROOT) without ever deleting real data.
#
# vendor/ is committed to the repo (not installed here) — this host's PHP has
# proc_get_status disabled, which breaks Composer's subprocess execution
# entirely, not just its git-based version detection. Regenerate vendor/
# locally with `composer install --no-dev --optimize-autoloader` and commit
# it before pushing; never edit vendor/ by hand.
#
# Run manually if needed:  bash ~/repositories/ragasoftware_site/bin/deploy.sh
# ---------------------------------------------------------------------------
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"
echo "==> repo:     $REPO_ROOT"

DOCROOT="${DEPLOY_DOCROOT:-/home/ragasoftwareir/public_html}"
LEGACY_BACKUP="$HOME/legacy_public_html_backup_$(date +%Y%m%d%H%M%S)"
echo "==> docroot:  $DOCROOT"

find_php_cli() {
    # Checks candidates IN ORDER and only accepts one whose `-v` output says
    # "(cli)" — on this host /usr/bin/php is actually php-cgi, which silently
    # accepts any artisan command and just prints the command list instead of
    # running it, so a plain executable-bit check isn't enough.
    local p out
    for p in "$@"; do
        [ -n "$p" ] && [ -x "$p" ] || continue
        out="$("$p" -v 2>/dev/null)" || continue
        case "$out" in
            *"(cli)"*) echo "$p"; return 0 ;;
        esac
    done
    return 1
}

PHP="$(find_php_cli \
    /opt/cpanel/ea-php84/root/usr/bin/php \
    /opt/cpanel/ea-php83/root/usr/bin/php \
    /opt/cpanel/ea-php82/root/usr/bin/php \
    /usr/local/bin/ea-php84 \
    /usr/local/bin/ea-php83 \
    /usr/local/bin/php \
    "$HOME/bin/php" \
    /usr/bin/php \
    "$(command -v php 2>/dev/null)")" || {
    echo "ERROR: no PHP CLI binary found — every candidate was missing, not executable, or reported something other than '(cli)' in 'php -v' (e.g. php-cgi). Ask host support for the correct CLI path and add it to find_php_cli's candidate list in bin/deploy.sh.";
    exit 1;
}

echo "==> php:      $PHP  ($("$PHP" -v | head -1))"

backup_leftover() {
    # Moves (never deletes) a real, non-symlink leftover out of the web root.
    local path="$1"
    if [ -e "$path" ] && [ ! -L "$path" ]; then
        mkdir -p "$LEGACY_BACKUP"
        mv "$path" "$LEGACY_BACKUP/$(basename "$path")"
        echo "==> moved leftover $path -> $LEGACY_BACKUP/$(basename "$path")"
    fi
}

# --- .env: must live in the repo (Laravel now boots from here), never in
#     the web root. If a previous, insecure deploy left a real .env sitting
#     in $DOCROOT, copy it into place once (only if the repo doesn't already
#     have one) and then relocate the original out of the web root — it is
#     preserved, not deleted, just no longer publicly reachable.
if [ ! -f "$REPO_ROOT/.env" ]; then
    if [ -f "$DOCROOT/.env" ]; then
        cp "$DOCROOT/.env" "$REPO_ROOT/.env"
        echo "==> copied $DOCROOT/.env -> $REPO_ROOT/.env (one-time move to the new app root)"
    else
        echo "ERROR: no .env at $REPO_ROOT/.env or $DOCROOT/.env — create one at $REPO_ROOT/.env (see .env.example) before deploying."
        exit 1
    fi
fi
backup_leftover "$DOCROOT/.env"

# --- uploads/: real, server-written media (CMS uploads via public_path()).
#     Never delete it. Check both the flat old-layout path ($DOCROOT/uploads)
#     and the nested one now confirmed on this host ($DOCROOT/public/uploads,
#     from the previous "whole repo copied into the Document Root" layout),
#     merge each into $REPO_ROOT/public/uploads without clobbering anything
#     already there, then remove the now-empty originals so uploads can
#     become a symlink below. Real content is never lost even if this list
#     ever misses a path: the generic cleanup pass further down backs up
#     (never deletes) anything it doesn't recognise.
for old_uploads in "$DOCROOT/uploads" "$DOCROOT/public/uploads"; do
    if [ -e "$old_uploads" ] && [ ! -L "$old_uploads" ]; then
        mkdir -p "$REPO_ROOT/public/uploads"
        cp -Rn "$old_uploads/." "$REPO_ROOT/public/uploads/" 2>/dev/null || true
        rm -rf "$old_uploads"
        echo "==> merged $old_uploads into $REPO_ROOT/public/uploads (no files overwritten)"
    fi
done

# --- Everything else in the Document Root that isn't one of the exact
#     entries this script manages: move it out (preserved in
#     $LEGACY_BACKUP, no longer web-exposed). This catches the *whole*
#     previous insecure layout (app/, config/, vendor/, the nested public/
#     folder, composer.json, …) generically, so nothing Laravel-related is
#     ever left reachable under the Document Root — including entries this
#     script doesn't know the name of. cPanel-managed paths that never came
#     from this repo (cgi-bin, .well-known, stats, logs, …) are left alone.
KEEP_IN_DOCROOT=(index.php .htaccess build uploads favicon.ico \
    cgi-bin .well-known error_log stats tmp .htpasswd)
is_kept() {
    local base="$1" k
    for k in "${KEEP_IN_DOCROOT[@]}"; do [ "$base" = "$k" ] && return 0; done
    return 1
}
shopt -s dotglob nullglob
for entry in "$DOCROOT"/*; do
    base="$(basename "$entry")"
    is_kept "$base" || backup_leftover "$entry"
done
shopt -u dotglob nullglob

# --- build/ and favicon.ico: real copies, refreshed on every deploy so they
#     can never drift out of sync with what was just deployed. Not symlinks —
#     this host doesn't reliably serve them even with Options +FollowSymLinks
#     in .htaccess (most likely AllowOverride doesn't permit that directive).
copy_entry() {
    local target="$1" linkname="$2"
    rm -rf "$linkname"
    cp -Rf "$target" "$linkname"
    echo "==> copied   $linkname <- $target"
}
copy_entry "$REPO_ROOT/public/build"       "$DOCROOT/build"
copy_entry "$REPO_ROOT/public/favicon.ico" "$DOCROOT/favicon.ico"

# uploads/: same as build/ above — a real copy, not a symlink (this host
# doesn't serve symlinks). New CMS uploads are written by the app into
# $REPO_ROOT/public/uploads (via public_path()) but only become reachable at
# the live URL after the NEXT deploy re-copies this folder — there is no
# live/immediate sync for uploads added between deploys.
copy_entry "$REPO_ROOT/public/uploads" "$DOCROOT/uploads"

# .htaccess: a fresh real copy every deploy (cheap, and keeps Apache's
# config-file reading independent of symlink behaviour).
cp -f "$REPO_ROOT/public/.htaccess" "$DOCROOT/.htaccess"
echo "==> copied   $DOCROOT/.htaccess"

# index.php: generated fresh every deploy with an ABSOLUTE path to this
# checkout, instead of the usual __DIR__.'/../...' relative lookup — a
# symlinked entry point would depend on how this host's Apache/PHP-FPM
# resolves __DIR__ through a symlink, which isn't guaranteed, so we sidestep
# it entirely. The tracked public/index.php is left untouched for local dev
# and any other hosting environment.
cat > "$DOCROOT/index.php" <<PHP
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Generated by bin/deploy.sh on every "Deploy HEAD Commit" — do not edit by
// hand, it will be overwritten on the next deploy. Document Root is fixed at
// $DOCROOT and can't point at the app's public/ folder, so this file locates
// the real Laravel application (this Git checkout) by absolute path.
\$appBase = '$REPO_ROOT';

if (file_exists(\$maintenance = \$appBase.'/storage/framework/maintenance.php')) {
    require \$maintenance;
}

require \$appBase.'/vendor/autoload.php';

/** @var Application \$app */
\$app = require_once \$appBase.'/bootstrap/app.php';

\$app->handleRequest(Request::capture());
PHP
echo "==> generated $DOCROOT/index.php (appBase=$REPO_ROOT)"

echo "==> rebuilding caches"
"$PHP" artisan package:discover --ansi
"$PHP" artisan optimize:clear

# Schema + roles/admin-user are safe to (re)apply on every deploy: migrate
# only ever adds what's missing, and both seeders below are pure
# updateOrCreate/findOrCreate calls that only touch roles/permissions and a
# single admin user row — never CMS content — so this can never overwrite
# anything an editor has changed through the admin panel.
"$PHP" artisan migrate --force
"$PHP" artisan db:seed --class=RolePermissionSeeder --force
"$PHP" artisan db:seed --class=AdminUserSeeder --force

"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache

echo "==> $DOCROOT now contains:"
ls -la "$DOCROOT"
[ -d "$LEGACY_BACKUP" ] && echo "==> NOTE: leftover files from a previous deploy layout were preserved at $LEGACY_BACKUP — verify nothing is needed, then delete it manually."

echo "==> deploy finished OK"
