# MASTER ARCHITECTURE
# Plugin: WC Product Loop Slider
# Slug: wc-product-loop-slider
# Version: 0.1.0
# Last Updated: 2025-04-07
# Author: [Your Name]

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

### เมื่อเริ่ม Chat ใหม่
```
1. บอก Claude: "อ่าน Master Architecture ก่อน"
2. ระบุว่าจะทำงานไฟล์ไหน (ดู Section 9: Chat Splitting Guide)
3. ตรวจสอบ version ปัจจุบันจาก Master
4. 🔴 ถ้าจะแก้ไขไฟล์ที่มีอยู่ → บอก user ว่าต้องการไฟล์ไหน → รอรับก่อนเริ่ม
5. จบ Chat → สรุป changelog สำหรับอัปเดต Master
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
| GitHub Repo | `github.com/[username]/wc-product-loop-slider` |

### วัตถุประสงค์
แสดง image slider ภายใน product card แต่ละใบในหน้า Shop / Archive / Category
โดยดึง Featured Image + Gallery Images ของสินค้าแต่ละชิ้นมาแสดงเป็น swiper slider

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
│   └── class-wcpls-settings.php        ← Settings/options page (future)
│
├── templates/
│   └── loop-slider.php                 ← Slider HTML template
│
├── assets/
│   ├── css/
│   │   └── wcpls-front.css             ← Frontend styles
│   ├── js/
│   │   └── wcpls-front.js              ← Frontend JS (Swiper init)
│   └── vendor/
│       ├── swiper/
│       │   ├── swiper-bundle.min.css
│       │   └── swiper-bundle.min.js
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
| `WCPLS_Core` | class | Main controller |
| `__construct()` | method | Load dependencies, register hooks |
| `load_dependencies()` | method | Require all includes files |
| `check_woocommerce()` | method | Admin notice if WC missing |

### `class-wcpls-assets.php`
| Item | Type | Description |
|---|---|---|
| `WCPLS_Assets` | class | Asset management |
| `__construct()` | method | Hook into `wp_enqueue_scripts` |
| `enqueue()` | method | Enqueue swiper + plugin CSS/JS on shop/archive pages |

### `class-wcpls-slider.php`
| Item | Type | Description |
|---|---|---|
| `WCPLS_Slider` | class | Slider output |
| `__construct()` | method | Hook into WooCommerce loop hooks |
| `get_image_ids( $product_id )` | method | Return array of [featured + gallery] image IDs |
| `render_slider( $product_id )` | method | Output slider HTML |
| `hook_into_loop()` | method | Replace/wrap default product thumbnail hook |

### `loop-slider.php` (Template)
| Item | Description |
|---|---|
| `$image_ids` | Array of image IDs passed from `render_slider()` |
| `$product_id` | Current product ID |

---

## Section 4: WooCommerce Hook Strategy

```
ลบ hook เดิม:
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

เพิ่ม hook ใหม่:
add_action('woocommerce_before_shop_loop_item_title', [$this, 'render_slider'], 10);
```

---

## Section 5: Assets / Dependencies

| Asset | Source | Version | Load Condition |
|---|---|---|---|
| Swiper CSS | Bundled vendor | 11.x | `is_shop()` / `is_product_category()` / `is_product_taxonomy()` |
| Swiper JS | Bundled vendor | 11.x | Same as above |
| `wcpls-front.css` | Plugin | per WCPLS_VERSION | Same as above |
| `wcpls-front.js` | Plugin | per WCPLS_VERSION | Same as above |

**หมายเหตุ:** Bundle Swiper ไว้ใน `/assets/vendor/` เพื่อไม่ต้องพึ่ง CDN

---

## Section 6: Settings (Planned — v0.3.0)

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
| `0.1.0` | 🔄 In Progress | Bootstrap + file structure + core slider |
| `0.2.0` | 📋 Planned | CSS refinement + touch/swipe optimization |
| `0.3.0` | 📋 Planned | Settings page (WP Admin) |
| `0.4.0` | 📋 Planned | Elementor Loop Template compatibility |
| `1.0.0` | 📋 Planned | Stable release |

---

## Section 8: GitHub Workflow

### Branch Strategy
```
main        ← stable, production-ready
develop     ← integration branch
feature/*   ← feature branches (e.g., feature/settings-page)
fix/*       ← bug fix branches
```

### Commit Message Format
```
[type] short description

type: feat | fix | style | refactor | docs | chore
ตัวอย่าง:
  feat: add swiper init for product loop
  fix: remove duplicate thumbnail on archive page
  docs: update MASTER_ARCHITECTURE version table
```

### VSCode Extensions แนะนำ
- **PHP Intelephense** — PHP autocomplete
- **GitLens** — Git history inline
- **GitHub Pull Requests** — PR management
- **PHPCS** — PHP coding standards (WordPress standard)

---

## Section 9: Chat Splitting Guide

| งาน | ไฟล์ที่เกี่ยวข้อง |
|---|---|
| Bootstrap / Main file | `wc-product-loop-slider.php`, `class-wcpls-core.php` |
| Assets enqueue | `class-wcpls-assets.php` |
| Slider logic | `class-wcpls-slider.php`, `loop-slider.php` |
| Frontend CSS | `assets/css/wcpls-front.css` |
| Frontend JS | `assets/js/wcpls-front.js` |
| Settings page | `class-wcpls-settings.php` |
| GitHub Actions | `.github/workflows/deploy.yml` |

---

## Section 10: Notes & Decisions Log

| Date | Decision | Reason |
|---|---|---|
| 2025-04-07 | ใช้ Swiper.js 11.x | เบา, touch-friendly, ไม่ต้องพึ่ง jQuery |
| 2025-04-07 | Bundle vendor แทน CDN | ไม่พึ่ง internet ขณะ load, GDPR-friendly |
| 2025-04-07 | ใช้ hook `woocommerce_before_shop_loop_item_title` | Standard WC hook, ทำงานกับ Elementor Loop ได้ |
| 2025-04-07 | PHP 8.0+ minimum | ใช้ named arguments + match expression ได้ |
