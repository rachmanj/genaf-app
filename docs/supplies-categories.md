# Supplies Categories Recommendation

## Overview

This document outlines the recommended category structure for supplies management based on the items provided (GAC001-GAC022 and GAA001-GAA146).

## Recommended Category Structure

Based on the 168 items analyzed (22 GAC series + 146 GAA series), the following 5 main categories are recommended:

### 1. Food & Beverages

**Description**: Food items, beverages, condiments, and consumable food-related products.

**Items in this category:**

-   GAC001: Gulaku (Sugar) - 1kg
-   GAC002: Kapal Api (Coffee) - 380g
-   GAC003: Sariwangi (Tea) - 190g
-   GAC004: ABC (Sauce/Condiment) - 600ml
-   GAC005: ABC (Sauce/Condiment) - 600ml
-   GAC006: Maxcreamer (Coffee Creamer) - 500g
-   GAC007: Nice (Biscuits/Snacks) - 1000g
-   GAC008: Nice (Biscuits/Snacks) - 1000g
-   GAC015: Sos (Sauce/Condiment) - 860ml
-   GAC019: Swallow (Food Ingredient, likely agar-agar/jelly) - isi4
-   GAC022: Nescaffe Coffee - 100g

**Suggested subcategories** (optional, for future expansion):

-   Groceries
-   Condiments & Sauces
-   Snacks
-   Beverages
-   Coffee & Tea

### 2. Personal Care

**Description**: Personal hygiene and grooming products.

**Items in this category:**

-   GAC010: Lifeboy (Soap/Body Wash) - 450ml
-   GAC011: Rejoice (Shampoo) - 320ml
-   GAC012: Pepsodent (Toothpaste) - 160g

**Suggested subcategories** (optional, for future expansion):

-   Body Care
-   Hair Care
-   Oral Care

### 3. Household Cleaning

**Description**: Cleaning products for office and household maintenance.

**Items in this category:**

-   GAC009: Sunlight (Dishwashing Liquid) - 800ml
-   GAC013: Soklin (Detergent) - 1Kg
-   GAC016: Clink (Toilet Cleaner) - 425ml
-   GAC017: Vixal (Toilet Cleaner) - 800ml
-   GAC020: Pladge (Furniture Polish) - 450ml
-   GAC021: Yuri (General-purpose Cleaner, likely floor cleaner) - 1000ml

**Suggested subcategories** (optional, for future expansion):

-   Dishwashing
-   Laundry
-   Bathroom Cleaning
-   Floor Cleaning
-   Surface Cleaning

### 4. Home & Pest Control

**Description**: Pest control and air freshening products.

**Items in this category:**

-   GAC014: Vape (Mosquito Repellent) - 600g
-   GAC018: Stella (Air Freshener) - 400ml

**Suggested subcategories** (optional, for future expansion):

-   Pest Control
-   Air Fresheners

### 5. ATK (Office Supplies & Stationery)

**Description**: Office supplies, stationery, writing instruments, papers, adhesives, tapes, and office tools.

**Items in this category** (146 items from GAA001-GAA146):

-   **Writing Instruments**: Kenko, Pop 1, Joyko (various models), Snowman markers (BG-12, WP-12, V-7, G-12), Artline EPG-4
-   **Paper Products**: Data Print, Asahi, Daito, E-Print, Paperline (various sizes), Kiky, Bola Dunia, SIDU, Garda, Biola, Erica
-   **Paper Sizes**: A4, A5, A3, F4, 1/2 A4, various dimensions (108x155mm, 28x40, 90x120cm, 76x110mm)
-   **Tapes**: Eka Tape (12mm, 24mm, 48mm), Daimaru (12mm, 24mm, 48mm), 3M VHB (12mm, 24mm), Gold Tape (12mm), Electrical Tape
-   **Adhesives**: UHU (Besar & Kecil), Alteco, Epoxy, Dextone, Castol
-   **Office Tools**: Kangaro (staplers), Sellery (ruler), Eraser, Penghapus, Spon
-   **Stickers/Labels**: BIG brand stickers (various sizes: 51x38mm, 76x76mm, 76x51mm, 44x12mm, 20x50mm)
-   **Office Accessories**: Papertray, Deskpenholder, Bantex Box File, FB, 3M, Fargo, Jaya
-   **Other**: Pencil Lead 2B, Citizen CT812-BN (calculator), Papanscanner

**Suggested subcategories** (optional, for future expansion):

-   Writing Instruments (Pens, Markers, Pencils)
-   Paper & Stationery (Printer paper, notebooks, envelopes)
-   Adhesives & Tapes (Tapes, glues, adhesives)
-   Office Tools (Staplers, rulers, erasers, scissors)
-   Office Accessories (Desk organizers, file folders)
-   Labels & Stickers

### 6. IT/Electronics

**Description**: IT supplies, printer consumables, batteries, and electronic accessories.

**Items in this category:**

-   **Printer Cartridges/Toners**:
    -   GAA060: Epson T6641
    -   GAA061: Epson T6644
    -   GAA062: Epson T6642
    -   GAA063: Epson T6643
    -   GAA106: Printer HP 85A
    -   GAA107: Printer HP 78A
    -   GAA113: Epson 0031
    -   GAA114: Epson 0032
    -   GAA115: Epson 0033
    -   GAA116: Epson 0034
-   **Batteries**:
    -   GAA039: ABC Alkaline
    -   GAA040: Panasonic
    -   GAA041: Panasonic
    -   GAA080: ABC Alkaline
    -   GAA090: ABC Alkaline (1 Kotak = 12 ea)
    -   GAA134: Panasonic
    -   GAA135: Panasonic
-   **Other IT Items**:
    -   GAA035: Citizen CT812-BN (Calculator)
    -   GAA140: Papanscanner

**Suggested subcategories** (optional, for future expansion):

-   Printer Supplies (Ink cartridges, toners, printer parts)
-   Batteries (Alkaline, rechargeable)
-   IT Accessories (Calculators, scanners, cables)

## Implementation Notes

1. **Current Implementation**: The supplies table uses a simple `category` string field. All 168 items (22 GAC + 146 GAA) have been categorized accordingly.

2. **Category Flexibility**: If you need more granular categorization in the future, you can:

    - Create a separate `categories` table with parent-child relationships
    - Use a hierarchical category structure (e.g., "Food & Beverages > Condiments")
    - Add a `subcategory` field to the supplies table

3. **Units Extracted**: Units have been automatically extracted from the description:

    - `kg` for kilograms
    - `g` for grams
    - `ml` for milliliters
    - `pcs` for pieces (Swallow@isi4)

4. **Duplicate Items**: Several items have duplicate descriptions:

    - **GAC Series**: GAC004 and GAC005 both represent "ABC@600ml", GAC007 and GAC008 both represent "Nice@1000g"
    - **GAA Series**: Multiple items share the same description (e.g., multiple "Paper Pack @1 Pack = 100 Sheet", multiple "Snowman BG-12", multiple "Daimaru @48mm", multiple "Panasonic" batteries, multiple "Paper A4", etc.)

    These have been kept as separate entries with unique codes as they appear in the source data. You may want to merge these if they represent the same physical item.

5. **Category Distribution**:

    - **ATK (Office Supplies)**: 127 items from GAA series (GAA001-GAA146, minus 19 IT items)
    - **IT/Electronics**: 19 items from GAA series (printer cartridges, batteries, calculators, scanners)
    - **Food & Beverages**: 11 items from GAC series (GAC001-GAC022)
    - **Household Cleaning**: 6 items from GAC series (GAC009-GAC022)
    - **Personal Care**: 3 items from GAC series (GAC010-GAC012)
    - **Home & Pest Control**: 2 items from GAC series (GAC014, GAC018)

    **Total**: 168 items (22 GAC + 146 GAA)

## Usage

To seed the database with all supplies including the new items:

```bash
php artisan db:seed --class=SupplySeeder
```

Or if running the full database seeder:

```bash
php artisan db:seed
```

The seeder uses `updateOrCreate()` to avoid duplicates, so it's safe to run multiple times.
