# MASTER ARCHITECTURE
# Plugin: WC Product Loop Slider
# Slug: wc-product-loop-slider
# Version: 0.2.4
# Last Updated: 2026-04-08
# Author: webwecreate.com

---

## ⚠️ IMPORTANT RULES — อ่านก่อนทำทุกครั้ง!

### กฎสำหรับการพัฒนา
1. ✅ **อ่าน Master นี้ก่อนเริ่มทำงานทุกครั้ง**
2. ✅ **ใช้ชื่อไฟล์ / class / function ตาม Master เท่านั้น**
3. ✅ **เพิ่ม version header ทุกครั้งที่แก้ไฟล์**
4. ✅ **สรุป changelog หลังแก้เสร็จ และอัปเดตไฟล์ CHANGELOG.md (ห้ามเขียนทับ)**
5. ✅ **ถาม user ถ้าไม่แน่ใจเรื่อง version**
6. ✅ **🔴 กฎ Version Control (สำคัญมาก):**
   - **ก่อนแก้ไขไฟล์ใดๆ** → บอก user ว่าต้องการไฟล์ไหน → รอ user ส่งเวอร์ชันล่าสุดมาก่อน
   - **ห้ามอ้างอิงไฟล์จาก context/memory** ของ Claude เพราะอาจเป็นเวอร์ชันเก่า
   - **ถ้าสร้างไฟล์ใหม่ทั้งหมด** → ไม่ต้องขอ (ไม่มี version conflict)
   - **ถ้าแก้ไขไฟล์ที่มีอยู่** → ต้องขอเวอร์ชันล่าสุดจาก user ก่อนเสมอ
   - เหตุผล: หลาย Chat ทำงานแยกกัน → ไฟล์อาจถูกแก้ใน Chat อื่นแล้ว → Claude ไม่รู้

### เมื่อเริ่ม Chat ใหม่
```
1. บอก Claude: "อ่าน Master Architecture ก่อน"
2. fetch ไฟล์เหล่านี้ก่อนเริ่มงานทุกครั้ง:
   https://api.github.com/repos/webwecreate/wc-product-loop-slider/contents/MASTER_ARCHITECTURE.md
   https://api.github.com/repos/webwecreate/wc-product-loop-slider/contents/CHANGELOG.md
3. ระบุว่าจะทำงานไฟล์ไหน (ดู Section 9: Chat Splitting Guide)
4. ตรวจสอบ version ปัจจุบันจาก Master
5. 🔴 ถ้าจะแก้ไขไฟล์ที่มีอยู่ → บอก user ว่าต้องการไฟล์ไหน → รอรับก่อนเริ่ม
6. จบ Chat → fetch Master + CHANGELOG อีกครั้งก่อนสรุป → สรุป changelog entry
```

---

## Section 1: Project Overview

| Key | Value |
|---|---|
| Plugin Name | WC Product Loop Slider |
| Plugin Slug | `wc-product-loop-slider` |
| Text Domain | `wc-product-loop-slider` |
| Requires WordPress | 6.0+ |
| Requires WooCommerce | 7.0+ |
| Requires PHP | 8.0+ |
| License | GPL-2.0-or-later |
| GitHub Repo | `github.com/webwecreate/wc-product-loop-slider` |

### วัตถุประสงค์
แสดง image slider ภายใน product card แต่ละใบในหน้า Shop / Archive / Category
โดยดึง Featured Image + Gallery Images ของสินค้าแต่ละชิ้นมาแสดงเป็น swiper slider

รองรับ 3 รูปแบบการแสดงผล:
1. **WooCommerce Archive/Shop ปกติ** — hook แทน thumbnail อัตโนมัติ
2. **Elementor Archive Products Widget** — hook แทน thumbnail อัตโนมัติ
3. **Elementor Loop Grid + Custom Loop Item** — ใช้ WCPLS Widget ลากแทน Featured Image

---

## Section 2: File Structure

```
wc-product-loop-slider/
│
├── wc-product-loop-slider.php          ← Main plugin file (bootstrap)
│
├── includes/
│   ├── class-wcpls-core.php            ← Core class (init hooks)
│   ├── class-wcpls-assets.php          ← Enqueue scripts/styles
│   ├── class-wcpls-slider.php          ← Slider HTML output logic
│   ├── class-wcpls-elementor.php       ← Elementor compatibility + Widget registration (v0.3.0)
│   ├── widgets/
│   │   └── class-wcpls-widget.php      ← Elementor Widget class (v0.3.1)
│   └── class-wcpls-settings.php        ← Settings/options page (v0.4.0)
│
├── templates/
│   ├── loop-slider.php                 ← Slider HTML template (WC loop)
│   └── elementor-slider.php            ← Slider HTML template (Elementor widget) (v0.3.1)
│
├── assets/
│   ├── css/
│   │   └── wcpls-front.css             ← Frontend styles
│   ├── js/
│   │   └── wcpls-front.js              ← Frontend JS (Swiper init)
│   └── vendor/
│       └── swiper/
│           ├── swiper-bundle.min.css
│           └── swiper-bundle.min.js
│
├── languages/
│   └── wc-product-loop-slider.pot
│
├── .github/
│   └── workflows/
│       └── deploy.yml                  ← GitHub Actions (future)
│
├── .gitignore
├── README.md
├── CHANGELOG.md
└── MASTER_ARCHITECTURE.md              ← ไฟล์นี้
```

---

## Section 3: Class & Function Registry

### `wc-product-loop-slider.php` (Main File)
| Item | Type | Description |
|---|---|---|
| `WCPLS_VERSION` | constant | Plugin version string |
| `WCPLS_PATH` | constant | Absolute path to plugin dir |
| `WCPLS_URL` | constant | URL to plugin dir |
| `WCPLS_FILE` | constant | Main plugin file path |
| `wcpls_init()` | function | Bootstrap — instantiate Core |

### `class-wcpls-core.php`
| Item | Type | Description |
|---|---|---|
| `WCPLS_Core` | class | Main controller (singleton) |
| `__construct()` | method | Load dependencies, register hooks |
| `load_dependencies()` | method | Require all includes files |
| `check_woocommerce()` | method | Admin notice if WC missing |

### `class-wcpls-assets.php`
| Item | Type | Description |
|---|---|---|
| `WCPLS_Assets` | class | Asset management |
| `__construct()` | method | Hook into `wp_enqueue_scripts` |
| `enqueue()` | method | Enqueue swiper + plugin CSS/JS on shop/archive/elementor pages |
| `is_product_archive()` | method | (private) Returns bool — checks shop/category/tag/taxonomy |
| `get_js_config()` | method | (private) Returns config array for wp_localize_script |

### `class-wcpls-slider.php`
| Item | Type | Description |
|---|---|---|
| `WCPLS_Slider` | class | Slider output |
| `__construct()` | method | Hook into WooCommerce loop hooks |
| `get_image_ids( $product_id )` | method | Return array of [featured + gallery] image IDs |
| `render_slider()` | method | Output slider HTML |
| `hook_into_loop()` | method | Replace/wrap default product thumbnail hook |
| `locate_template( $file )` | method | Theme override support |

### `class-wcpls-elementor.php` ← NEW (v0.3.0)
| Item | Type | Description |
|---|---|---|
| `WCPLS_Elementor` | class | Elementor compatibility controller |
| `__construct()` | method | Check Elementor active, register hooks |
| `register_widgets( $widgets_manager )` | method | Register WCPLS_Widget กับ Elementor |
| `enqueue_editor_assets()` | method | โหลด Swiper ใน Elementor Editor |
| `is_elementor_active()` | method | (static) ตรวจสอบว่า Elementor ติดตั้งอยู่ |
| `force_enqueue_assets()` | method | โหลด assets ในหน้าที่มี Elementor Loop |

### `class-wcpls-widget.php` ← NEW (v0.3.1)
| Item | Type | Description |
|---|---|---|
| `WCPLS_Widget` | class | extends `\Elementor\Widget_Base` |
| `get_name()` | method | `wcpls-product-slider` |
| `get_title()` | method | `Product Slider` |
| `get_icon()` | method | `eicon-media-carousel` |
| `get_categories()` | method | `['woocommerce-elements']` |
| `register_controls()` | method | image_size, show_pagination, show_navigation |
| `render()` | method | ดึง product ID → get_image_ids() → load elementor-slider.php |

### `loop-slider.php` (Template)
| Item | Description |
|---|---|
| `$image_ids` | Array of image IDs passed from `render_slider()` |
| `$product_id` | Current product ID |

### `elementor-slider.php` (Template) ← NEW (v0.3.1)
| Item | Description |
|---|---|
| `$image_ids` | Array of image IDs passed from `WCPLS_Widget::render()` |
| `$product_id` | Current product ID |
| `$settings` | Elementor widget settings array |

---

## Section 4: WooCommerce Hook Strategy

```
ลบ hook เดิม:
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

เพิ่ม hook ใหม่:
add_action('woocommerce_before_shop_loop_item_title', [$this, 'render_slider'], 10);
```

### Elementor Hook Strategy (v0.3.0)
```
add_action('elementor/widgets/register', [$this, 'register_widgets']);
add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_assets']);
add_action('wp_enqueue_scripts', [$this, 'force_enqueue_assets']);
```

---

## Section 5: Assets / Dependencies

| Asset | Source | Version | Load Condition |
|---|---|---|---|
| Swiper CSS | Bundled vendor | 11.x | `is_product_archive()` OR Elementor page with loop |
| Swiper JS | Bundled vendor | 11.x | Same as above |
| `wcpls-front.css` | Plugin | per WCPLS_VERSION | Same as above |
| `wcpls-front.js` | Plugin | per WCPLS_VERSION | Same as above |

**หมายเหตุ:** Bundle Swiper ไว้ใน `/assets/vendor/` เพื่อไม่ต้องพึ่ง CDN

---

## Section 6: Settings (Planned — v0.4.0)

| Option Key | Type | Default | Description |
|---|---|---|---|
| `wcpls_show_pagination` | bool | `true` | แสดง dot pagination |
| `wcpls_show_navigation` | bool | `false` | แสดงปุ่ม prev/next |
| `wcpls_autoplay` | bool | `false` | Auto slide |
| `wcpls_autoplay_delay` | int | `3000` | Autoplay delay (ms) |
| `wcpls_image_size` | string | `woocommerce_thumbnail` | WordPress image size |
| `wcpls_aspect_ratio` | string | `1/1` | CSS aspect-ratio |

---

## Section 7: Version History (Summary)

| Version | Status | Description |
|---|---|---|
| `0.1.0` | ✅ Done | Master Architecture + CHANGELOG init |
| `0.1.1` | ✅ Done | Bootstrap — main plugin file + WCPLS_Core singleton (Part 1) |
| `0.1.2` | ✅ Done | Assets enqueue — WCPLS_Assets, CSS/JS skeletons (Part 2) |
| `0.1.3` | ✅ Done | Slider class + loop template (Part 3) |
| `0.2.0` | ✅ Done | Frontend CSS — wcpls-front.css (Part 4) |
| `0.2.1` | ✅ Done | Frontend JS — Full Swiper init, loop logic, swipe protection (Part 5) |
| `0.2.2` | ✅ Done | PC hover arrows + mobile dots UX + dot position fix (Part 6) |
| `0.2.3` | ✅ Done | Fix missing wrapper div in template (Part 6) |
| `0.2.4` | ✅ Done | Fix prev-next button styling (Part 6) |
| `0.3.0` | ✅ Done | Elementor assets + hook — `class-wcpls-elementor.php` |
| `0.3.1` | ✅ Done| Elementor Widget — `class-wcpls-widget.php` + `elementor-slider.php` |
| `0.3.2` | ✅ Done | Fix Swiper width overflow on Elementor Flexbox — CSS position:absolute |
| `0.4.0` | 📋 Part 8 | Settings page (WP Admin) — `class-wcpls-settings.php` |
| `1.0.0` | 📋 Planned | Stable release |

---

## Section 8: GitHub Workflow

### Branch Strategy
```
main        ← stable, production-ready
develop     ← integration branch
feature/*   ← feature branches (e.g., feature/elementor-widget)
fix/*       ← bug fix branches
```

### Commit Message Format
```
[type] short description

type: feat | fix | style | refactor | docs | chore
ตัวอย่าง:
  feat: add Elementor widget registration
  fix: load assets on Elementor loop pages
  docs: update MASTER_ARCHITECTURE version table
```

### VSCode Extensions แนะนำ
- **PHP Intelephense** — PHP autocomplete
- **GitLens** — Git history inline
- **GitHub Pull Requests** — PR management
- **PHPCS** — PHP coding standards (WordPress standard)

---

## Section 9: Chat Splitting Guide

| Part | งาน | ไฟล์ที่เกี่ยวข้อง | Version |
|---|---|---|---|
| 1 | Bootstrap + Core | `wc-product-loop-slider.php`, `class-wcpls-core.php` | 0.1.1 |
| 2 | Assets Enqueue | `class-wcpls-assets.php` | 0.1.2 |
| 3 | Slider Logic | `class-wcpls-slider.php`, `loop-slider.php` | 0.1.3 |
| 4 | Frontend CSS | `assets/css/wcpls-front.css` | 0.2.0 |
| 5 | Frontend JS | `assets/js/wcpls-front.js` | 0.2.1 |
| 6 | Test & Debug | ทุกไฟล์ที่มีอยู่ | 0.2.2–0.2.4 |
| **7a** | **Elementor Assets + Hook** | **`class-wcpls-elementor.php`** (ใหม่) + แก้ `class-wcpls-core.php`, `class-wcpls-assets.php` | **0.3.0** |
| **7b** | **Elementor Widget** | **`class-wcpls-widget.php`**, **`elementor-slider.php`** (ใหม่ทั้งคู่) | **0.3.1** |
| 8 | Settings Page | `class-wcpls-settings.php` (ใหม่) | 0.4.0 |

---

## Section 10: Notes & Decisions Log

| Date | Decision | Reason |
|---|---|---|
| 2026-04-09 | `.swiper` ใช้ `position: absolute` แก้ Flexbox overflow | Flexbox parent ขยายตาม .swiper-wrapper flex content → circular dependency → แก้โดยเอา .swiper ออกจาก normal flow |
| 2026-04-08 | แบ่ง Elementor compatibility เป็น Part 7a + 7b | 7a = assets/hook (ง่าย), 7b = widget (ซับซ้อนกว่า) แยกเพื่อ debug ง่าย |
| 2026-04-08 | WCPLS_Widget reuse `get_image_ids()` จาก WCPLS_Slider | ไม่ duplicate code |
| 2026-04-08 | Settings page เลื่อนเป็น v0.4.0 | Elementor compatibility สำคัญกว่าและควรทำก่อน |
| 2026-04-08 | ซ่อน dots บน PC ด้วย `@media (hover: hover)` | ไม่ต้องใช้ JS detect device |
| 2026-04-08 | Navigation arrows เปิดเสมอ (CSS controls visibility) | ลด config complexity |
| 2026-04-07 | ใช้ `aspect-ratio` แทน `padding-top` hack | CSS modern, readable, รองรับ block themes |
| 2026-04-07 | ซ่อน nav arrows ด้วย `opacity` + `pointer-events` | ให้ transition ทำงานได้ (display:none ทำไม่ได้) |
| 2026-04-07 | `isolation: isolate` บน `.wcpls-slider-wrapper` | ป้องกัน z-index leak กับ theme อื่น |
| 2026-04-07 | `WCPLS_Core` ใช้ singleton pattern | ป้องกัน instantiate ซ้ำ และปลอดภัยกับ `plugins_loaded` hook |
| 2026-04-07 | `load_dependencies()` ใช้ `class_exists()` guard | ให้ทำงานได้แบบ incremental โดยไม่ fatal error |
| 2026-04-07 | `is_woocommerce_active()` รองรับ Multisite | ครอบคลุม setup แบบ Multisite ด้วย `active_sitewide_plugins` |
| 2025-04-07 | ใช้ Swiper.js 11.x | เบา, touch-friendly, ไม่ต้องพึ่ง jQuery |
| 2025-04-07 | Bundle vendor แทน CDN | ไม่พึ่ง internet ขณะ load, GDPR-friendly |
| 2025-04-07 | ใช้ hook `woocommerce_before_shop_loop_item_title` | Standard WC hook, ทำงานกับ Elementor Loop ได้ |
| 2025-04-07 | PHP 8.0+ minimum | ใช้ named arguments + match expression ได้ |
