# Warm Creative Designer Studio – Logo Delivery Portal (Planning Spec)

> **Instruction:** **Do not write any code yet.** **Write out full codebase.**

## Goal

Design a small web app that lets design clients **download their logo files** via a **unique private link** that feels warm, crafted, and professional.

## Tech Constraints

* **Stack:** plain **PHP + HTML + CSS + vanilla JS** only (no frameworks).
* **Hosting:** **Hostinger Shared Hosting**.
* **Storage:** Per-client directories on server (not directly exposed).
* **Share links:** Tokenized, non-guessable URLs (e.g., `/dl/{token}`) that hide physical paths.

## Aesthetic Direction (2025)

* **Mood:** Friendly, handcrafted, premium yet approachable (Behance/indie studio).
* **Visuals:** Soft pastels (peach, dusty pink, baby blue, lavender) + warm neutrals (warm white, soft gray), gentle gradients, glass/frosted cards, soft shadows, rounded corners.
* **Typography:** Large, confident headers + readable body text.

  * Suggested: **Header:** “**Fraunces**” or “**Playfair Display**”
  * Body: “**Inter**” or “**Nunito Sans**”
* **Micro-interactions:** Subtle hover states, gentle easing, small content reveals (no heavy animation/parallax).

## Page Map & IA

* **Public**

  * `GET /dl/{token}` — Client delivery page (greeting, project summary, asset grid, actions).
  * `GET /dl/{token}/preview/{file}` — Safe previews for images/SVG/PDF (where applicable).
  * `GET /dl/{token}/download/{file}` — Single file download.
  * `GET /dl/{token}/download-all` — ZIP package download.
  * Error states: `expired`, `paused`, `invalid token`, `rate-limited`.
* **Admin (authenticated)**

  * `GET /admin/login` — OTP login.
  * `GET /admin` — Dashboard (deliveries table, status, actions).
  * `GET /admin/deliveries/new` — Create delivery (client meta, notes, expiry).
  * `POST /admin/uploads` — Drag-and-drop files, tag types (primary, alt, favicon, social kit).
  * Actions: copy link, pause/resume, expire, regenerate token, repackage ZIP, delete, email client.

## Component Library (Describe)

* **Header Bar:** Studio mark, minimal nav.
* **Greeting Block:** “Hey **{ClientName}**—your logo package is ready.”
* **Project Summary Card:** Project name, version/revision tag, delivery date, notes.
* **Download Grid Item:** File thumbnail/icon, name, type badge (PNG/JPG/SVG/PDF/ZIP), file size, quick preview button (when applicable), download button.
* **CTA Row:** “Download All (ZIP)”, “View Brand Notes”, “Request a Tweak”.
* **Alert/Status Banners:** Ready, Expired, Paused, Error, Success.
* **Modal:** Confirm actions, brand notes, request tweak form.
* **Uploader:** Drag-and-drop zone, file list with tags, progress states.
* **Table (Admin):** Deliveries list with filters/sort, action menu.
* **Auth (OTP):** Email field, code entry with resend + rate-limit message.
* **Footer:** Privacy note, contact email, lean nav.

## Content & Microcopy (Samples)

* **Welcome:** “Hey **{ClientName}**, your logo package is ready. Download the files below. If anything looks off, I’ll fix it fast.”
* **Notes Link:** “Read brand notes →”
* **Download-All:** “Download everything (.zip)”
* **Empty State (Admin):** “No deliveries yet. Create your first link and make a client’s day.”
* **Expired:** “This link has expired. Reach out and I’ll refresh it.”
* **Paused:** “This delivery is temporarily paused.”
* **Invalid Token:** “We can’t find that page. Check the link or contact me.”
* **Request Tweak (success):** “Thanks! I’ll respond shortly.”

## User Flows

* **Client:** Open link → skim summary → preview if needed → download all → optional tweak request → done.
* **Admin:**

  1. **Create Delivery:** Add client meta → upload files → tag assets → set expiry/passphrase (optional) → generate token → copy link → send email.
  2. **Maintain:** Pause/resume → regenerate link → update files → repackage ZIP.
  3. **Wrap-up:** Expire delivery → archive logs.

## Security, Privacy & Abuse Prevention (Plan)

* Non-guessable tokens; deny directory listing; block direct file browsing.
* Optional **expiry** (date and/or max download count).
* Optional **passphrase** per token.
* Rate-limit downloads; throttle OTP requests.
* Minimal audit log: timestamp, IP (hashed), file name, event type.
* Robots: disallow indexing of token paths; neutral Open Graph (no sensitive details).

## Accessibility & Performance

* WCAG-aligned contrast; visible focus; keyboard navigable controls.
* Mobile-first; comfortable tap targets; semantic HTML.
* Lightweight CSS/JS; defer non-critical JS; responsive images; cache headers.
* SVG icon sprites; avoid heavy fonts (subset + swap).

## Hostinger Deployment Considerations

* **PHP:** Use latest stable supported by Hostinger; enable `mbstring`, `zip`, `openssl`, `fileinfo`.
* **Uploads:** Set `upload_max_filesize` and `post_max_size` appropriately; handle chunking if needed.
* **Routing:** `.htaccess` for pretty URLs (`/dl/{token}`) and secure file gateway (no direct path exposure).
* **Permissions:** Lock down uploads/storage; no public read on raw asset directories.
* **Backups:** Nightly backup of `/storage` + DB; ZIPs can be ephemeral (rebuild on demand).

## SEO / Social

* Token pages **noindex** (robots + meta).
* Generic OG tags for brand homepage only (not per-delivery pages).

## Analytics (Privacy-Respecting)

* Server-side counts for: page views, unique downloads, file-level downloads, “download all”.
* Dashboard summaries: total deliveries, active, expiring soon, most-downloaded asset types.

## Legal

* Short privacy statement: files used solely to deliver client work; no resale of data; limited retention.
* Link to terms (simple, human-readable).

## Email Templates (Copy Only)

* **Subject:** “Your logo package is ready — {ProjectName}”
* **Body (Client):**
  “Hey {ClientName},
  Your logo package for **{ProjectName}** is ready.
  Download link: {URL}
  Notes: {Short notes}
  This link {expires on {Date}/has no expiry}.
  If you need tweaks, hit ‘Request a Tweak’ on the page.
  — {StudioName}”
* **Body (OTP to Admin):**
  “Your sign-in code: **{123456}**. Expires in 10 minutes.”

## Codebase Plan (Outline Only)

> **Do not write any code yet.** This is a **full codebase outline** you will produce in the next step.

```
/public
  /assets
    /css
    /js
    /img
  /index.php
  /.htaccess
/src
  /Controllers
    DeliveryController.php
    AdminController.php
    AuthController.php
    FileController.php
  /Services
    TokenService.php
    ZipService.php
    Mailer.php
    Logger.php
    RateLimiter.php
  /Models
    Delivery.php
    Asset.php
    User.php
  /Middlewares
    AuthMiddleware.php
    RateLimitMiddleware.php
  /Views
    /partials (header, footer, alerts, modals, file-card, table-row)
    /public (delivery.php, expired.php, invalid.php)
    /admin (login.php, dashboard.php, new-delivery.php, uploads.php)
  /Utils
    Env.php
    Response.php
    Validator.php
/storage
  /deliveries/{deliveryId}/assets
  /zips/{deliveryId}.zip (optional cache)
/config
  app.php
  mail.php
  security.php
/database
  schema.sql
  seeds.sql
```

### Key Endpoints (Design Only)

* `GET /dl/{token}` — Render delivery.
* `GET /dl/{token}/download/{file}`
* `GET /dl/{token}/download-all`
* `GET /dl/{token}/preview/{file}`
* `GET /admin/login`, `POST /admin/otp`
* `GET /admin`, `POST /admin/deliveries`, `POST /admin/uploads`
* `POST /admin/deliveries/{id}/pause|expire|regen|zip|delete`

## Open Questions (Answer Before Coding)

1. Link expiry: **date**, **download count**, **both**, or **none**?
2. Downloads: **anonymous** or require **passphrase**?
3. Primary file types (to optimize preview rules)?
4. Include **“Download All as ZIP”**?
5. Admin: **single user** or **multi-user**?
6. Email when a client downloads (yes/no)?
7. Brand assets (logo/colors/voice) to apply globally?

---

**Next step (after you answer Open Questions):** the system will **write out the full codebase** following this spec, including file structure, PHP routes/controllers, views, styles, and deployment notes—still adhering to the **plain PHP + HTML + CSS + JS** constraint.
