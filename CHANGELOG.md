# CHANGELOG
# Plugin: WC Product Loop Slider
# Format: [Version] YYYY-MM-DD — Description

All notable changes to this project will be documented in this file.
ห้ามเขียนทับ — ให้เพิ่มบันทึกใหม่ด้านบนเสมอ

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
