# CHANGELOG
# Plugin: WC Product Loop Slider
# Format: [Version] YYYY-MM-DD — Description

All notable changes to this project will be documented in this file.
ห้ามเขียนทับ — ให้เพิ่มบันทึกใหม่ด้านบนเสมอ

---

## [0.3.4] — 2026-04-16

### Added

* `includes/widgets/class-wcpls-widget.php` (0.3.1 → 0.3.4):
  - เพิ่ม `link` control (URL type) พร้อม dynamic tag support
    ใน Loop Item ใช้ dynamic tag "Post URL" เพื่อ link ไป product อัตโนมัติ
    รองรับ is_external (target="_blank") และ nofollow
  - เพิ่ม `woocommerce_thumbnail` เป็น default image size option

* `templates/elementor-slider.php` (0.3.1 → 0.3.4):
  - ห่อ `.wcpls-slider-wrapper` ด้วย `<a>` เมื่อมีค่า `link.url`
  - รองรับ `is_external` และ `nofollow` attributes
  - ไม่มี link → render แบบเดิม ไม่มี `<a>` tag

---

## [0.3.3] — 2026-04-16

### Fixed

* `wc-product-loop-slider.php` (0.1.1 → 0.3.3):
  - อัปเดต `Version` header และ `WCPLS_VERSION` constant เป็น `0.3.3`
  - เพิ่ม HPOS compatibility declaration via
    `FeaturesUtil::declare_compatibility( 'custom_order_tables', true )`
    แก้ WooCommerce admin warning "incompatible plugin detected"
    (plugin ไม่ได้ใช้ orders จึง declare compatible ได้ทันที)

* `assets/css/wcpls-front.css` (0.3.2 → 0.3.3):
  - เพิ่ม `--wcpls-min-height: 200px` CSS variable ใน Section 1
    เป็น fallback height เมื่อ aspect-ratio ยังไม่ resolve บน mobile
  - เปลี่ยน `min-height: 300px` hardcode เป็น `min-height: var(--wcpls-min-height)`
  - เพิ่ม Section 7 — Elementor Compatibility:
    `align-self: flex-start` บน `.elementor-widget-wcpls-product-slider`
    แก้ปัญหา widget ยืด height เต็ม grid row ทำให้ footer คำนวณผิด
    และข้อความ product ซ้อนทับ slider บน mobile

---
## [0.3.2] — 2026-04-09

### Fixed

* `assets/css/wcpls-front.css` (0.2.2 → 0.3.2):
  - เปลี่ยน `.wcpls-slider-wrapper .swiper` จาก static เป็น `position: absolute`
    แก้ปัญหา Swiper width overflow บน Elementor Flexbox container:
    `.swiper-wrapper` (display:flex) ขยาย parent chain จนได้ width = 3.35544e+07px
    การใช้ position:absolute เอา .swiper ออกจาก normal flow
    ทำให้ parent Flexbox ไม่ขยายตาม Swiper content อีกต่อไป
    รองรับทั้ง CSS Grid และ Flexbox container ใน Elementor Loop Item

* `assets/js/wcpls-front.js` (0.3.0 → 0.3.2):
  - ลบ `observer/observeParents` ออก (ไม่จำเป็นหลัง CSS fix)
  - ลบ debug console.log + debug code ทั้งหมด
  - Section 5: restore `createSlider(el)` call ที่หายไประหว่าง debug
  - cleanup commented-out code ทั้งหมด

### Notes

* Root cause: Flexbox circular dependency —
  Swiper flex layout ขยาย parent → parent ขยาย → Swiper อ่านค่าผิด → วนซ้ำ
* CSS Grid ทำงานได้เพราะ `grid-template-columns` บังคับ width ไว้ก่อน
* Solution: `position: absolute` ตัด circular dependency ได้ทันที
---

## [0.3.1] — 2026-04-08

### Added

* `includes/widgets/class-wcpls-widget.php` — `WCPLS_Widget` class with:
  + `get_name()`       → `wcpls-product-slider`
  + `get_title()`      → `Product Slider`
  + `get_icon()`       → `eicon-media-carousel`
  + `get_categories()` → `['woocommerce-elements']`
  + `register_controls()` — 3 controls:
    - `image_size`      : SELECT (thumbnail / medium / large / full; default: thumbnail)
    - `show_pagination` : SWITCHER (default: yes)
    - `show_navigation` : SWITCHER (default: off)
  + `render()` — resolves `product` post type via `get_the_ID()`;
    reuses `WCPLS_Slider::get_image_ids()`; loads `elementor-slider.php`;
    falls back gracefully when not a product or no images found;
    shows editor hint in Elementor edit mode
  + `locate_template()` — private; supports theme override at
    `{theme}/wc-product-loop-slider/{file}` before plugin bundled path

* `templates/elementor-slider.php` — Swiper-compatible HTML template:
  - Accepts `$image_ids`, `$product_id`, `$settings` (via `extract`)
  - HTML structure mirrors `loop-slider.php`:
    `.wcpls-slider-wrapper` > `.wcpls-slider.swiper` > `.swiper-wrapper` > `.swiper-slide`
  - Uses `$settings['image_size']` (no hardcode); filterable via `wcpls_elementor_image_size`
  - `.swiper-pagination` rendered when `show_pagination = yes` AND `slide_count > 1`
  - `swiper-button-prev` / `swiper-button-next` rendered when `show_navigation = yes` AND `slide_count > 1`
  - All images use `loading="lazy"` + `decoding="async"` + `draggable="false"`
  - Action hooks: `wcpls_before_slider`, `wcpls_after_slider`,
    `wcpls_before_slider_inner`, `wcpls_after_slider_inner`,
    `wcpls_before_slide`, `wcpls_after_slide`
  - Unique `id` per instance: `wcpls-slider-{$product_id}`

### Fixed

* `includes/widgets/class-wcpls-widget.php` + `templates/elementor-slider.php`:
  - ลบ `Plugin Name:` ออกจาก docblock — WordPress scan ทุกไฟล์ใน plugin
    folder ทำให้ header conflict → "The plugin does not have a valid header"

* `includes/class-wcpls-elementor.php`:
  - เพิ่ม `register_widgets( \Elementor\Widgets_Manager $widgets_manager )` method
  - เพิ่ม hook `elementor/widgets/register` ใน constructor
    — widget ไม่แสดงใน Elementor panel เลยก่อนแก้

---

## [0.3.0] — 2026-04-08

### Added

* `includes/class-wcpls-elementor.php` — `WCPLS_Elementor` class with:
  + `is_elementor_active()` — static check via `ELEMENTOR_VERSION` constant
    และ `\Elementor\Plugin` class guard
  + `is_elementor_built_page()` — static check via `_elementor_edit_mode`
    post meta; lightweight, no Documents API dependency
  + `enqueue_editor_assets()` — hooks `elementor/editor/after_enqueue_scripts`;
    loads Swiper + wcpls-front CSS/JS inside Elementor editor canvas
  + `force_enqueue_assets()` — hooks `elementor/frontend/after_enqueue_scripts`;
    force-loads full asset stack on Elementor-built pages; guards
    `wp_localize_script` with `wp_script_is( 'wcpls-front', 'done' )`
  + Constructor bails silently when Elementor inactive — zero overhead

### Modified

* `includes/class-wcpls-core.php`:
  + `load_dependencies()` — เพิ่ม `class-wcpls-elementor.php` ใน file list
  + เพิ่ม `WCPLS_Elementor` instantiation block (with `class_exists` guard)
* `includes/class-wcpls-assets.php`:
  + `is_product_archive()` — เพิ่ม Elementor branch สำหรับ
    singular pages ที่ใช้ Loop Builder นอก WC archive routes
    (ใช้ `class_exists` guard ป้องกัน fatal error)

### Notes

* `wp_enqueue_*` idempotent — ปลอดภัยเมื่อ WCPLS_Assets และ
  WCPLS_Elementor เรียก handle เดียวกันบนหน้าที่เป็นทั้ง WC archive
  และ Elementor-built page
* Elementor Widget (Part 7b / v0.3.1) ยังไม่ implement

---

## [MASTER UPDATE] — 2026-04-08

### Changed (MASTER_ARCHITECTURE.md only — no plugin code changed)
- Section 1: เพิ่มอธิบาย 3 รูปแบบการแสดงผลที่รองรับ
- Section 2: เพิ่มไฟล์ใหม่ `class-wcpls-elementor.php`, `class-wcpls-widget.php`, `elementor-slider.php`
- Section 3: เพิ่ม class registry สำหรับ `WCPLS_Elementor` และ `WCPLS_Widget`
- Section 4: เพิ่ม Elementor Hook Strategy
- Section 5: อัปเดต Load Condition ให้ครอบคลุม Elementor pages
- Section 7: แยก v0.3.0 เป็น 7a (0.3.0) และ 7b (0.3.1) / เลื่อน Settings เป็น v0.4.0
- Section 9: อัปเดต Chat Splitting Guide เพิ่ม Part 7a, 7b, 8 พร้อม version
- Section 10: เพิ่ม decisions log สำหรับการตัดสินใจใหม่

---
## [0.2.4] — 2026-04-08

### Fixed
- `assets/css/wcpls-front.css` (0.2.1 → 0.2.2):
  - Fix: prev-next button styling

---
## [0.2.3] — 2026-04-08

### Fixed
- `templates/loop-slider.php` (0.2.2 → 0.2.3):
  - เพิ่ม `<div class="wcpls-slider-wrapper">` ครอบ `.wcpls-slider.swiper`
  - แก้ bug ที่ CSS selectors ทั้งหมดไม่ทำงาน เพราะ HTML ไม่มี `.wcpls-slider-wrapper`
  - ส่งผลให้ arrow hover, dot position, dot hide บน PC ทำงานถูกต้องทั้งหมด

---
## [0.2.2] — 2026-04-08

### Changed
- `assets/css/wcpls-front.css` (0.2.0 → 0.2.2):
  - ยก `--wcpls-dot-bottom` จาก `10px` เป็น `20px`
  - เพิ่ม `@media (hover: hover) and (pointer: fine)` — ซ่อน dots บน PC อัตโนมัติ
- `assets/js/wcpls-front.js`:
  - Navigation arrows เปิดเสมอเมื่อ `slideCount > 1` แทนขึ้นกับ `wcplsConfig.navigation`
- `templates/loop-slider.php` (0.1.3 → 0.2.2):
  - เพิ่ม `.swiper-button-prev` / `.swiper-button-next` (`<button>`) พร้อม `aria-label`

### UX Behaviour
- **Mobile**: dots subtle overlay บนรูป
- **PC**: dots ซ่อน, ลูกศรแสดงเมื่อ hover บน card

---
## [0.2.1] — 2026-04-07

### Added / Changed

- `assets/js/wcpls-front.js` — Full Swiper init implementation (Part 5):
  - `createSlider( el )` — factory function; initialises one Swiper instance per
    `.wcpls-slider` element; guards against double-init via `el.swiper` check
  - `loop: true` when `slideCount > 1`; `loop: false` for single-image cards
    (prevents Swiper slide-duplication artefacts)
  - Pagination dots enabled automatically when `slideCount > 1` and
    `wcplsConfig.pagination !== false`
  - `preventClicks: true` + `preventClicksPropagation: true` — blocks
    click-through to product page during a swipe gesture
  - `touchStartPreventDefault: false` — preserves native vertical page scroll
    on mobile while Swiper still handles horizontal swipe
  - Navigation arrows and autoplay wired to `wcplsConfig` (off by default;
    ready for v0.3.0 Settings page)
  - `initAllSliders()` runs on `DOMContentLoaded`
  - `reinitNewSliders()` hooked to `wc_fragments_loaded`,
    `wc_fragments_refreshed`, and custom `wcpls_reinit` jQuery event
  - `window.wcplsReinit` exposed as public API for themes/plugins

---
## [0.2.0] — 2026-04-07

### Added
- `assets/css/wcpls-front.css` — Frontend stylesheet เต็มรูปแบบ แทน skeleton จาก v0.1.2:
  - **Section 1 — CSS Custom Properties**: `--wcpls-aspect-ratio`, `--wcpls-dot-*`,
    `--wcpls-nav-*`, `--wcpls-transition` รองรับ override จาก child theme
  - **Section 2 — Slider Container**: `.wcpls-slider-wrapper` ใช้ `aspect-ratio: 1/1`
    เป็น default, `overflow: hidden`, `position: relative`, `isolation: isolate`
  - **Section 3 — Swiper Slide & Images**: `object-fit: cover`, `width/height: 100%`,
    subtle zoom on active slide เมื่อ hover
  - **Section 4 — Pagination Dots**: absolute position บนรูป, bottom-centred,
    `box-shadow` ให้มองเห็นได้ทั้ง background สว่าง/มืด
  - **Section 5 — Navigation Arrows**: ซ่อนโดย default, แสดง + slide-in เมื่อ hover บน card,
    ซ่อนอัตโนมัติเมื่อมีรูปเดียว (`swiper-button-disabled`)
  - **Section 6 — Accessibility**: `prefers-reduced-motion` และ `focus-visible`
    สำหรับ keyboard navigation

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
