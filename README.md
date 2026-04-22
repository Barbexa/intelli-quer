# 🔍 Intelli-quer: Intelligence Query Engine 

A robust PHP-based backend API designed for high-performance demographic data segmentation. This engine allows users to query over 2,000+ profiles using both structured parameters and Natural Language Queries (NLQ).

## 🚀 Live Demo
**API Base URL:** `[INSERT_YOUR_DEPLOYED_URL_HERE]`

## 🛠️ Features
- **Deterministic NLQ Engine:** Converts plain English (e.g., "young males from Nigeria") into structured SQL filters.
- **Advanced Filtering:** Slice data by gender, age ranges, country ID, and probability confidence scores.
- **Optimized Pagination:** Efficient data retrieval using `LIMIT` and `OFFSET` to handle large datasets.
- **UUID v7 Integration:** Uses time-ordered, universally unique identifiers for future-proof scalability.
- **CORS Enabled:** Fully accessible from any frontend origin.

## 🧠 How the Parser Works
The Natural Language Query (NLQ) engine uses a **rule-based keyword mapping** strategy:
1. **Keyword Extraction:** Identifies core entities like `male`, `female`, and country codes (e.g., `NG`, `TZ`).
2. **Age Mapping:** - Supports specific boundaries: "above [age]", "under [age]".
   - **Business Rule:** The keyword "young" is automatically mapped to the **16-24** age range.
3. **Logic Merging:** Combines multiple identified filters into a single `AND` condition for the database.

### ⚠️ Limitations
- **Strict Syntax:** Requires specific keywords (e.g., "males" or "men" work, but "guys" might not).
- **No Complex OR Logic:** Currently optimized for intersectional (`AND`) queries only.

## 📖 API Documentation

### 1. Natural Language Search
**Endpoint:** `GET /api/profiles/search?q={query}`
- **Example:** `/api/profiles/search?q=young males from nigeria`

### 2. Structured Filtering
**Endpoint:** `GET /api/profiles`
- **Params:** `gender`, `country_id`, `min_age`, `max_age`, `sort_by`, `order`, `page`, `limit`.
- **Example:** `/api/profiles?gender=female&min_age=18&sort_by=age&order=desc`

## 🛠️ Installation & Setup
1. Clone the repository.
2. Import the database schema provided in `db.php`.
3. Run `seed.php` to populate the database from `seed_profiles.json`.
4. Ensure your Apache server has `mod_rewrite` enabled for the `.htaccess` rules.
