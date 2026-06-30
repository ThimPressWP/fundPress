# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

FundPress is a free WordPress donation / crowdfunding plugin by ThimPress (think Kickstarter-style campaigns). Current version 2.0.9; requires WordPress 6.0+ and PHP 7.0+. Text domain: `fundpress`.

## Commands

```bash
# Install deps
composer install            # PHP deps (stripe/stripe-php)
npm install                 # JS/build tooling

# Frontend asset development
npm run start               # wp-scripts watch (assets/src -> build/)
npm run build               # wp-scripts production build
gulp watch                  # compile assets/sass/*.scss -> assets/css (dart-sass)
gulp                        # full release pipeline: clean, sass, RTL CSS, minify, copy, zip into release/

# i18n
npm run makepot             # generate languages/fundpress.pot via wp-cli

# Full release bundle (build JS + makepot + zip)
npm run release             # runs build-release.js -> orchestrates build-makepot-zip + gulp
npm run format              # wp-scripts format on assets/src/**/*.js

# Tests (PHPUnit, WP test harness)
bash tests/bin/install.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]
phpunit                     # runs ./tests/unit-tests via phpunit.xml
phpunit tests/unit-tests/path/to/Test.php   # single test file
```

Note: the local dev site is at `http://lp.test` (WordPress root two levels up at `F:\laragon\www\lp`), DB name `lp`, root user, no password. This plugin lives under `wp-content/plugins/fundPress`.

## Architecture

### Bootstrap & loading
- `fundpress.php` is the entry point. It defines path/URI constants (`FUNDPRESS_PATH`, `FUNDPRESS_INC`, `FUNDPRESS_TEMP`, etc.) and the meta-key prefixes `TP_DONATE_META_DONOR`/`_DONATE`/`_CAMPAIGN` (all `thimpress_*`). The singleton `FundPress` class boots on the `init` hook (priority 0), **not** `plugins_loaded`. Access it via the global helper `FP()`.
- `FundPress::includes()` manually `require`s the core `inc/class-dn-*.php` files in dependency order, then `_autoload()`s whole directories (`abstracts`, `settings`, `products`) by globbing `class-dn-<dir>-*.php`.
- `inc/class-dn-autoloader.php` (`DN_Autoloader`) is an spl autoloader: class `DN_Foo_Bar` → `inc/class-dn-foo-bar.php`. It special-cases prefixes to route into subdirs: `DN_Payment_*` → `gateways/<name>/`, `DN_Widget_*` → `widgets/`, `DN_Metabox_*` → `admin/metaboxes/`.

### Naming convention
Everything is prefixed `DN_` (legacy "TP Donation" name) in PHP classes and `dn_`/`thimpress_` in post types, meta, and sessions. The plugin was renamed to FundPress but internals keep the `dn`/`donate` vocabulary — treat `dn`, `donate`, and `fundpress` as the same product.

### Data model (custom post types)
Registered in `inc/class-dn-custom-post-type.php` on `init` priority 99:
- `dn_campaign` — a fundraising campaign (the main product). Taxonomies `dn_campaign_cat`, `dn_campaign_tag`.
- `dn_donate` — a donation order.
- `dn_donate_item` — line items within a donation.
- `dn_donor` — a donor record.

Each CPT has a corresponding model class (`DN_Campaign`, `DN_Donate`, `DN_Donor`, `DN_Donate_Item`) that extends the `DN_Post_Base` abstract (`inc/abstracts/class-dn-abstract-posts.php`) and is instantiated via a per-id cached `::instance( $post )` factory.

### Donation flow (cart → checkout → payment)
This mirrors a WooCommerce-style flow:
1. **Cart** (`DN_Cart`, singleton, `process_cart` on `init` p99). Stores state in `DN_Sessions` under keys `thimpress_donate_cart` / `thimpress_donate_info`. Cart items are resolved to a product class `DN_Product_<Type>` (e.g. `DN_Product_Campaign` in `inc/products/`), filterable via `donate_product_type_class`.
2. **Checkout** (`DN_Checkout`, singleton, `process_checkout`). Creates/updates a `DN_Donor`, then a `DN_Donate` order, then hands off to the selected gateway.
3. **Payment gateways** (`DN_Payment_Gateways` registry, singleton). Each gateway lives in `inc/gateways/<name>/` and extends the `DN_Abstract_Payment` abstract. Built-in: `paypal`, `stripe`, `authorize-net`. `get_payment_available()` returns enabled gateways.

`DN_Sessions` (`inc/class-dn-sessions.php`) is a custom session store (not PHP `$_SESSION`) — request `DN_Sessions::instance( '<namespace-key>' )`.

### Settings
- `DN_Settings` (`inc/class-dn-setting.php`, singleton, loaded early before the rest of the plugin) is the options accessor.
- Setting groups live in `inc/settings/class-dn-setting-{general,checkout,donate,email}.php`, each extending `DN_Abstract_Settings`. Admin setting field views render from `inc/admin/views/settings/`.

### Admin
- Only loaded when `is_admin()`: `inc/admin/class-dn-admin.php`. Metaboxes are `DN_Metabox_*` classes in `inc/admin/metaboxes/` (autoloaded by prefix), extending `DN_Abstract_Metaboxs`. Metabox UI uses the bundled **CMB2** library in `inc/vendors/cmb2/` (the plugin filters `cmb2_meta_box_url` to point CMB2 at its bundled copy when CMB2 isn't already loaded).

### Frontend rendering
- `DN_Template_Include` resolves templates; the `templates/` directory (cart, checkout, loop, notices, payments, shortcodes, single) holds overridable theme templates rooted at `FUNDPRESS_TEMP`.
- Hooks split across `inc/dn-core-hooks.php` (logic) and `inc/dn-template-hooks.php` (presentation), plus global helpers in `inc/dn-core-functions.php`.
- `DN_Shortcodes` + `DN_Abstract_Shortcodes`; widgets in `inc/widgets/`.
- Assets: `DN_Assets` / `DN_Frontend_Assets`. Source SCSS in `assets/sass/`, compiled CSS in `assets/css/` and minified/RTL in `assets/dist/css/`. JS source in `assets/src` (wp-scripts) and `assets/js/{admin,frontend}`.

### AJAX
`DN_Ajax` (`inc/class-dn-ajax.php`) registers handlers from an `$actions` map (`action => nopriv`) onto `wp_ajax_*` / `wp_ajax_nopriv_*`. Active actions: `donate_load_form`, `donate_submit`. (`donate_remove_compensate` and `donate_action_status` are intentionally commented out — see commit `b2f9122`, disabled for security.)

### Security helper
`DN_Helpper` (`inc/class-dn-helpper.php`, accessor `DN()`) — use `DN_Helpper::DN_sanitize_params_submitted( $value, $type )` to sanitize submitted request data (recurses arrays; types map to `sanitize_text_field` / `sanitize_textarea_field` / `sanitize_key`). Prefer this over hand-rolled sanitization when handling `$_GET`/`$_POST`. GDPR integration lives in `inc/class-dn-gdpr.php`.

## Conventions when editing

- New gateway → add `inc/gateways/<slug>/class-dn-payment-<slug>.php` extending `DN_Abstract_Payment`; the autoloader and `DN_Payment_Gateways` pick it up by the `DN_Payment_<Slug>` class name.
- New setting group → drop `class-dn-setting-<name>.php` into `inc/settings/` (autoloaded by the `settings` glob in `includes()`).
- New metabox → `inc/admin/metaboxes/class-dn-metabox-<name>.php` with class `DN_Metabox_<Name>`.
- The release zip excludes `node_modules`, `assets/src`, build config, composer/npm manifests, tests, and `gulpfile.mjs` (see `copyFilesToRelease` in `gulpfile.mjs`). Don't rely on those files being present at runtime in a shipped build.
- `FUNDPRESS_VER` (`2.0.1`) is the asset cache-busting version and is separate from the plugin header `Version` (`2.0.9`) used for releases — bump the plugin header for releases.
