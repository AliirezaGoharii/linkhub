# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2026-11-09

### Added

- **Bio field** — 280-character description with live counter, saved with appearance
  settings and rendered on the public page (also used as the meta description).
- **Page-view & click analytics** — guest page views (owner excluded) and per-link
  click counts, throttled and incremented atomically; dashboard stat cards with a
  "Top Link" highlight.
- **14-day analytics chart** — new `daily_stats` table (one row per user per day,
  race-safe atomic upserts) and a dependency-free SVG area chart with hover
  crosshair, tooltip, 14-day totals and a zero-traffic empty state.
- **Link show/hide toggle** — `is_active` column plus a policy-authorized
  `PATCH /links/{link}/toggle` endpoint; dashboard rows get an eye toggle with
  optimistic UI and a "Hidden" badge; hidden links leave the live preview and the
  public page but keep their click history.
- **QR-code sharing** — client-side QR of the public URL with PNG download.
- **Copy public URL** button with clipboard feedback.
- **Per-link click badges** in the dashboard link list.
- **Delete confirmation modal** replacing `window.confirm()`.
- **Social-media-ready public pages** — server-rendered Open Graph, Twitter Card,
  canonical link and JSON-LD `Person` structured data.
- **Demo seeder** — `demo@linkhub.test` with five links, bio and two weeks of
  seeded analytics.
- **GitHub Actions CI** — dependency install → production Vite build → Laravel
  Pint → full test suite on every push/PR to `main`.
- **Test suite expanded from 25 to 62 tests** (268 assertions) covering links,
  public pages, analytics, usernames, settings and authorization.

### Changed

- three.js / Vanta backgrounds are now npm-pinned, code-split and lazily fetched
  only on pages that use them, and disabled under `prefers-reduced-motion` with a
  static gradient fallback (previously loaded from a CDN on every page).
- Fonts are self-hosted via Fontsource — zero external font requests (previously
  referenced in CSS but never actually loaded).
- Link reordering persists with a single CASE-based `UPDATE` backed by a composite
  `(user_id, ordering)` index (previously one query per link).
- The `auth.user` Inertia payload no longer eager-loads the user's links on every
  request; the dashboard receives them as an explicit prop and hydrates the Pinia
  store.
- Dashboard stats extracted from a closure route into `DashboardController`; new
  dedicated `LinkClickController`.
- Avatar/banner upload validation tightened to `jpg,jpeg,png,webp`, max 2 MB.
- Public page images use cache-busting parameters; external links carry
  `rel="nofollow"`.
- README, `composer.json` and `.env.example` rewritten to describe LinkHub
  instead of the Laravel skeleton.

### Fixed

- **Fatal 500 on link edit/delete** — the base controller lacked
  `AuthorizesRequests` and no `LinkPolicy` existed.
- **Fatal 500 on profile save** — `UserService::updateProfile()` was called but
  never implemented.
- **Drag & drop reordering never saved** — `PUT /links/order` was shadowed by
  `PUT /links/{link}`; routes reordered.
- **Fatal 500 on password reset** — the `password_reset_tokens` table was lost;
  restored by a new (deployment-safe) migration.
- **Email verification never enforced** — `User` now implements `MustVerifyEmail`.
- **Live preview broke after link edit/delete** — the Pinia `pageStore` was
  missing `updateLink` / `removeLink`.
- **Username changes silently dropped** — `ProfileUpdateRequest` now validates
  uniqueness, `alpha_dash`, lowercase and max 24 characters.
- **Test suite was broken** — `UserFactory` never generated the required
  `username` column (24 of 25 default tests failed).
- `.dark-input` had no padding, making forms look cramped.
- Theme color contrast calculation broke on 3-digit hex values; now restricted
  to 6-digit hex.

### Security

- **Public pages no longer leak the owner's e-mail and verification status** —
  the Inertia payload is built from an explicit allow-list
  (`UserService::publicPagePayload()`); regression tests assert the e-mail never
  appears.
- **Reserved username blocklist** (`login`, `register`, `dashboard`, `admin`,
  `api`, …) enforced at registration and profile update, plus a `[a-z0-9_-]+`
  route constraint.
- Link reordering validates ownership of every id; link mutations enforced by
  `LinkPolicy` (foreign edit/delete → 403, tested).
- Account deletion now removes uploaded photos and banners (no orphan files).
- Baseline security headers: `X-Content-Type-Options`, `X-Frame-Options`,
  `Referrer-Policy`, `Permissions-Policy`.

## [1.0.0] - 2026-08-14

- Initial public state of the repository — link-in-bio app built with Laravel 12,
  Inertia.js, Vue 3, Pinia and Tailwind CSS.
