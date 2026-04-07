# CHANGELOG
# Plugin: WC Product Loop Slider
# Format: [Version] YYYY-MM-DD — Description

All notable changes to this project will be documented in this file.
ห้ามเขียนทับ — ให้เพิ่มบันทึกใหม่ด้านบนเสมอ

---
## [0.1.3] — 2026-04-07

### Added
- `includes/class-wcpls-slider.php` — `WCPLS_Slider` class with:
  - `hook_into_loop()` — removes `woocommerce_template_loop_product_thumbnail`
    and registers `render_slider` on `woocommerce_before_shop_loop_item_title` (priority 10)
  - `get_image_ids( int $product_id )` — returns deduplicated array of
    [featured image, ...gallery images] attachment IDs
  - `render_slider()` — resolves current product, calls `get_image_ids()`,
    locates template; falls back to WC default thumbnail when no images or template missing
  - `locate_template( string $file )` — supports theme override at
    `{theme}/wc-product-loop-slider/{file}` before falling back to plugin bundled template
- `templates/loop-slider.php` — Swiper-compatible HTML template:
  - Outputs `.wcpls-slider.swiper` > `.swiper-wrapper` > `.swiper-slide` structure
  - Uses `wp_get_attachment_image()` for native srcset/sizes support
  - All images use `loading="lazy"` + `decoding="async"`
  - `.swiper-pagination` rendered only when slide count > 1
  - Action hooks: `wcpls_before_slider`, `wcpls_after_slider`,
    `wcpls_before_slide`, `wcpls_after_slide`
  - Filter: `wcpls_image_size` (default: `woocommerce_thumbnail`)

---
## [0.1.2] — 2026-04-07

### Added
- `includes/class-wcpls-assets.php` — class `WCPLS_Assets` with:
  - `__construct()` — hooks `enqueue()` into `wp_enqueue_scripts`
  - `enqueue()` — loads Swiper CSS/JS (vendor bundle) + `wcpls-front.css` /
    `wcpls-front.js` only on `is_shop()`, `is_product_category()`,
    `is_product_tag()`, `is_product_taxonomy()` pages
  - `is_product_archive()` — private helper for load condition
  - `get_js_config()` — returns PHP→JS config array via `wp_localize_script`
    (`window.wcplsConfig`); mirrors planned v0.3.0 settings options
- `assets/css/wcpls-front.css` — skeleton with 5-section TOC; styles deferred to v0.2.0
- `assets/js/wcpls-front.js` — skeleton with 4-section TOC; Swiper init deferred to v0.2.0

### Notes
- Swiper loaded from `assets/vendor/swiper/swiper-bundle.min.css|js` (no CDN)
- Swiper handle: `wcpls-swiper` (hardcoded version `11.0.0`)
- Plugin handle: `wcpls-front` (version from `WCPLS_VERSION` constant)
- JS config object `wcplsConfig` already exposes `pagination`, `navigation`,
  `autoplay`, `autoplayDelay` — ready for Settings page wiring in v0.3.0

---
## [0.1.1] — 2026-04-07

### Added
- `wc-product-loop-slider.php` — main plugin file with headers, 4 constants
  (`WCPLS_VERSION`, `WCPLS_PATH`, `WCPLS_URL`, `WCPLS_FILE`) and `wcpls_init()`
  bootstrap function hooked to `plugins_loaded`.
- `includes/class-wcpls-core.php` — singleton `WCPLS_Core` class with:
  - `instance()` — singleton accessor
  - `load_dependencies()` — conditionally requires and instantiates
    `WCPLS_Assets` and `WCPLS_Slider` (graceful no-op when files not yet present)
  - `check_woocommerce()` — hooks admin notice when WooCommerce is missing
  - `admin_notice_missing_woocommerce()` — outputs dismissible error notice
  - `is_woocommerce_active()` — supports single-site and Multisite network activation
---

## [0.1.0] — 2025-04-07
### Added
- สร้าง Master Architecture document (MASTER_ARCHITECTURE.md)
- กำหนดโครงสร้างไฟล์ทั้งหมด
- กำหนด class / function registry
- กำหนด WooCommerce hook strategy
- กำหนด version roadmap 0.1.0 → 1.0.0
- กำหนด GitHub branch strategy และ commit message format
- สร้าง CHANGELOG.md (ไฟล์นี้)
