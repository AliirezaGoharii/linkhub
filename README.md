# LinkHub — A Micro-SaaS "Link-in-Bio" Platform 🚀

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blueviolet)](https://www.php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-orange)](https://laravel.com)
[![Vue.js Version](https://img.shields.io/badge/Vue.js-3.x-green)](https://vuejs.org)
[![Tests](https://img.shields.io/badge/tests-62%20passing-brightgreen)](#-running-the-tests)
[![CI](https://github.com/aliirezagoharii/linkhub/actions/workflows/ci.yml/badge.svg)](https://github.com/aliirezagoharii/linkhub/actions/workflows/ci.yml)

A complete Software-as-a-Service (SaaS) platform for building beautiful "link-in-bio" pages, architected with a modern stack using Laravel and Vue.js (via Inertia.js). This project serves as a full-stack portfolio piece, showcasing the ability to develop a complete product from architectural design and database schema to a modern, reactive user interface.

---

## 📍 About The Project

LinkHub is a simple, fast, and elegant tool for users who want to centralize their digital identity into a single link — a self-hosted alternative to Linktree.

## 🌟 Key Features

### User-facing
* **🔗 Link management** — add, edit, delete and drag & drop reorder links from a reactive dashboard.
* **👁️ Show/hide toggle** — hide a link from your public page without losing its click history.
* **🎨 Full customization** — profile photo, banner with smart client-side cropping, a bio, and a custom theme color.
* **📊 Lightweight analytics** — page views and per-link click counts in dashboard stat cards with a "top link" highlight, plus a 14-day views/clicks trend chart (dependency-free SVG) with hover tooltips.
* **🔍 Social-media-ready pages** — every public page ships server-rendered Open Graph, Twitter Card and canonical tags plus JSON-LD `Person` structured data, so shared links unfurl correctly in chat apps and social feeds.
* **📱 QR-code sharing** — download a QR code for your public page straight from the dashboard.
* **💎 World-class UX** — glassmorphism effects, an interactive 3D "Cosmic Fog" background, and smooth entry animations.
* **🖼️ Favicon** — the public page's favicon is automatically set from the user's profile photo.

### Engineering
* **🏗️ Service layer architecture** — controllers stay thin; business logic lives in `App\Services`.
* **🛡️ Route-model policies** — every link mutation is authorized through a `LinkPolicy`.
* **🔒 Public-page privacy allow-list** — the public Inertia payload is built from an explicit field allow-list (`UserService::publicPagePayload`): the owner's e-mail address, verification status and internal counters are never shipped to anonymous visitors.
* **✅ Strict validation** — reserved usernames (`/login`, `/admin`, …) are rejected, uploads are checked by type and size, and reordering only accepts link ids the user owns.
* **📈 Fixed-size daily analytics** — a `daily_stats` table (unique per user per day, race-safe upserts) aggregates views and clicks without row explosion; missing days are zero-filled server-side.
* **⚡ Performance-minded frontend** — three.js/Vanta are code-split and lazy-loaded (only on pages that show them, and never for users who prefer reduced motion); fonts are self-hosted; no third-party CDN scripts; links are passed as explicit page props instead of being serialized into the shared `auth.user` payload on every request.
* **🗂️ Index discipline** — composite `(user_id, ordering)` index backs the ordered link listing and the scoped reorder update.
* **🤖 Continuous integration** — GitHub Actions runs Laravel Pint, the full test suite and a production frontend build on every push and pull request.
* **🧪 Test suite** — 62 feature tests covering auth, profile, links, page settings, public pages, SEO tags, privacy, toggling, and analytics.

## 🛠️ Tech Stack

* **Backend:** Laravel 12 (PHP 8.2+)
* **Frontend:** Vue.js 3 (Composition API) + Pinia
* **Architecture:** Inertia.js
* **Database:** SQLite (default) or MySQL
* **Styling:** Tailwind CSS
* **Key Libraries:** Vanta.js + three.js (lazy 3D background), VueUse Motion, Vue.Draggable, qrcode

## 🚀 Getting Started

### Prerequisites

* PHP >= 8.2 (with `pdo_sqlite`, `mbstring`, `gd` etc. — the standard Laravel requirements)
* Composer
* Node.js & NPM

### Installation

1. **Clone the repo:**
    ```sh
    git clone https://github.com/aliirezagoharii/linkhub.git
    cd linkhub
    ```
2. **Install PHP dependencies:**
    ```sh
    composer install
    ```
3. **Install JavaScript dependencies:**
    ```sh
    npm install
    ```
4. **Set up your environment file:**
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```
    > The default `.env.example` uses an SQLite database — zero configuration needed.
    > To use MySQL instead, set `DB_CONNECTION=mysql` and the `DB_*` credentials, and comment out the `DB_CONNECTION=sqlite` line.
5. **Run the database migrations (with demo data):**
    ```sh
    touch database/database.sqlite
    php artisan migrate --seed
    ```
    This creates a demo account you can log in with:
    | | |
    |---|---|
    | **URL** | `/demo` |
    | **Email** | `demo@linkhub.test` |
    | **Password** | `password` |
6. **Build the assets and run the app:**
    ```sh
    npm run build
    php artisan serve
    ```
    Then open http://localhost:8000. For development with hot-module reloading, run `npm run dev` in a second terminal.

## 🧪 Running the Tests

```sh
composer test
```

The suite uses an in-memory SQLite database and covers authentication, profile management, link CRUD, reordering, visibility toggling (including authorization checks), username validation, page settings, public pages, SEO/social meta tags, public-payload privacy, and click/view analytics (both the lifetime counters and the daily series).

## 🤖 CI

GitHub Actions (`.github/workflows/ci.yml`) runs on every push and pull request: dependency installation, a production frontend build (the feature tests need the Vite manifest), a Laravel Pint code-style check, and the full test suite.

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📬 Contact

Telegram - [@EliteSaiko](https://t.me/EliteSaiko)

Project Link: [https://github.com/aliirezagoharii/linkhub](https://github.com/aliirezagoharii/linkhub)
