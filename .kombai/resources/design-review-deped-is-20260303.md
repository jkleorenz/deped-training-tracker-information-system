# Design Review Results: DepEd IS — All Major Pages

**Review Date**: 2026-03-03
**Routes Reviewed**: `/login`, `/register`, `/dashboard`, `/personnel`, `/personnel/{user}`, `/personnel/{user}/pds`, `/trainings`
**Focus Areas**: Visual Design · UX/Usability · Responsive/Mobile · Accessibility

---

## Summary

The application has a polished, consistent layout shell (sidebar + header + card-based content area) with good mobile responsiveness and clean Bootstrap usage throughout. However, a **critical CSS typo** breaks the content scroll area, the **register page is visually inconsistent** with the login page, and several accessibility gaps (missing landmarks, ARIA states, contrast edge-cases) need addressing. Modals nested inside flex wrappers and multi-select UX on mobile are the most impactful UX issues.

---

## Issues

| # | Issue | Criticality | Category | Location |
|---|-------|-------------|----------|----------|
| 1 | **CSS typo: `overflow-auto;`** — `overflow-auto;` is not valid CSS (missing colon). Should be `overflow: auto;`. This is Tailwind utility syntax in a plain CSS block — the `.content-area` div will not scroll properly. | 🔴 Critical | Visual Design | `resources/views/layouts/app.blade.php:173` |
| 2 | **Edit Personnel modal nested inside flex container** — `#modalEditPersonnel` is placed inside the `.d-flex.justify-content-between` header wrapper in the Personnel list. Bootstrap modals must be direct children of `<body>` or at least outside flex/grid ancestors to avoid stacking context / positioning bugs. | 🔴 Critical | UX/Usability | `resources/views/personnel/index.blade.php:73–127` |
| 3 | **Register page lacks visual parity with Login page** — Login has a professional split-panel card with branding, gradient panel, and polished form. Register is a plain `col-md-6` card with no branding, no decorative panel, and a tiny `h5` title. Users landing on /register via the Login link experience a jarring design shift. | 🟠 High | Visual Design | `resources/views/auth/register.blade.php:1–217` |
| 4 | **Register page: `<style>` block placed inside `@section('content')`** — The `<style>` block (`.password-input-wrapper`, etc.) is declared inside the content section instead of `@push('styles')`. This injects `<style>` into the `<body>` mid-document, causing render and validation issues. | 🟠 High | Visual Design | `resources/views/auth/register.blade.php:132–139` |
| 5 | **No `<main>` landmark for authenticated pages** — The `.content-area` div renders all page content but has no `role="main"` and is not a `<main>` element. Screen readers cannot jump to main content using landmark navigation. The guest layout's `<main>` is fine, but the authenticated shell is missing it. | 🟠 High | Accessibility | `resources/views/layouts/app.blade.php:335–338` |
| 6 | **Sidebar toggle: `aria-expanded` not toggled** — The `#sidebarToggle` button does not update `aria-expanded` when the sidebar opens/closes. Screen reader users cannot determine the current state of the sidebar. | 🟠 High | Accessibility | `resources/views/layouts/app.blade.php:295, 342–360` |
| 7 | **Sidebar backdrop not dismissible via keyboard (Escape)** — The sidebar backdrop closes on pointer click but has no `keydown` handler for the Escape key. Keyboard-only users cannot dismiss the sidebar once opened on mobile. | 🟠 High | Accessibility | `resources/views/layouts/app.blade.php:342–360` |
| 8 | **Import modal: `<select multiple>` for user selection** — Both the Trainings admin import modal (`#modalImport`) and the Assign modal (`#modalAssign`) use `<select multiple size="8">`, which requires Ctrl/Cmd + click for multi-selection. This is unusable on touch devices and confusing for non-technical users. A checkbox list would be far more usable. | 🟠 High | UX/Usability | `resources/views/trainings/index.blade.php:326–330, 354–356` |
| 9 | **Page titles use `<h4>` not `<h1>`** — Dashboard, Personnel, Trainings, and Personnel Profile pages all use `<h4 class="page-title">` as the main page heading. Semantically, the primary heading should be `<h1>`. This breaks the document outline for screen readers and SEO. | 🟠 High | Accessibility | `resources/views/dashboard/admin.blade.php:46`, `resources/views/personnel/index.blade.php:75`, `resources/views/trainings/index.blade.php:69`, `resources/views/personnel/show.blade.php:48` |
| 10 | **Login: Missing `autocomplete` attributes on email and password fields** — `autocomplete="email"` and `autocomplete="current-password"` are absent from the login inputs. Password managers and browsers may not autofill correctly. | 🟡 Medium | Accessibility | `resources/views/auth/login.blade.php:281–295` |
| 11 | **Register: Missing `autocomplete="email"` on email field** — Only password fields have `autocomplete` attributes; the email field is missing `autocomplete="email"`. | 🟡 Medium | Accessibility | `resources/views/auth/register.blade.php:47` |
| 12 | **Admin Dashboard: "Import from Excel" quick action tile links to `/trainings`, not an import workflow** — The quick action tile labeled "Import from Excel" navigates to the trainings list page, not an actual import flow. This is misleading — clicking it should open the Import modal directly, not redirect to a different page. | 🟡 Medium | UX/Usability | `resources/views/dashboard/admin.blade.php:107–111` |
| 13 | **Personnel show: Inconsistent action button sizes** — In the personnel profile header, buttons mix `btn-sm` and default size: `btn-deped btn-sm`, `btn-outline-success btn-sm`, `btn-outline-secondary btn-sm`, and then `btn-outline-secondary` (no `btn-sm`) for "STA Excel". This creates a visually uneven action bar. | 🟡 Medium | Visual Design | `resources/views/personnel/show.blade.php:51–57` |
| 14 | **Login brand title wraps at medium viewports** — At ≈768–991px the `<h1 class="brand-title">Maasin City Division</h1>` wraps to two lines ("Maasin City" / "Division") in the left brand panel, which looks accidental. A `white-space: nowrap` or slightly smaller font size at that breakpoint would fix it. | 🟡 Medium | Responsive/Mobile | `resources/views/auth/login.blade.php:71–77` (`.login-brand .brand-title`) |
| 15 | **Sidebar navigation: No `aria-current="page"` on active nav link** — The active link is styled with `.active` CSS class but lacks the `aria-current="page"` attribute required by WCAG for indicating the current page to screen readers. | 🟡 Medium | Accessibility | `resources/views/layouts/app.blade.php:263–279` |
| 16 | **Focus ring uses low-opacity shadow** — `--deped-shadow: rgba(30,53,255,0.35)` is used as the `box-shadow` focus ring on `.btn-deped:focus-visible` and form controls. A 35% opacity focus ring on a white background may not meet WCAG 3:1 contrast requirement for focus indicators. A solid or higher-opacity ring is recommended. | 🟡 Medium | Accessibility | `resources/views/layouts/app.blade.php:185, 13–16` |
| 17 | **`min-w-0` is not a Bootstrap utility class** — The class `min-w-0` is used in stat cards on the admin dashboard and personnel profile (`<div class="min-w-0">`). This is a Tailwind CSS class, not Bootstrap. It will have no effect unless custom CSS is defined. To achieve the same result, use Bootstrap's `overflow-hidden` or add a custom CSS class. | 🟡 Medium | Visual Design | `resources/views/dashboard/admin.blade.php:62`, `resources/views/personnel/show.blade.php:70, 81, 92` |
| 18 | **Register `col-md-6` leaves excessive whitespace on large screens** — At ≥1200px viewports, the register form occupies only ~600px of ~1200px available, with empty space on both sides. The login page correctly uses `col-12 col-lg-10 col-xl-9`. Register should do the same (or use a `col-md-8 col-lg-6` pattern). | 🟡 Medium | Responsive/Mobile | `resources/views/auth/register.blade.php:7` |
| 19 | **Sidebar backdrop is not a focusable/interactive element** — The backdrop `<div class="sidebar-backdrop">` is a dismiss target but is a `<div>` with no `role`, no keyboard handler, and `aria-hidden="true"` even when visible. When the sidebar is open, the backdrop should be a dismissible overlay with appropriate ARIA. | 🟡 Medium | Accessibility | `resources/views/layouts/app.blade.php:340` |
| 20 | **Personnel list: Missing "Role" column** — The personnel table renders Name, Employee ID, Position, and School but omits the user's Role (Admin/Sub-admin/Personnel). The role badges are rendered but only in the card mobile view (looking at `renderRoleBadge()` — it is actually never appended in either `renderTable` or `renderCards` in practice). Admins have no at-a-glance way to see user roles in the list. | 🟡 Medium | UX/Usability | `resources/views/personnel/index.blade.php:195–206`, `app/Http/Controllers/PersonnelController.php` |
| 21 | **Dashboard (personnel): Card header color mismatch with name** — `.card-header-green` class name implies green, but its CSS uses `var(--deped-primary)` (blue by default, red/green depending on user theme). The class name is semantically wrong and confusing for future maintenance. | ⚪ Low | Visual Design | `resources/views/dashboard/personnel.blade.php:7`, `resources/views/personnel/show.blade.php:7` |
| 22 | **Trainings table sort state bug** — In `trainings/index.blade.php`, the sort direction toggle logic reads: `(window._trainingsDirection === 'asc' && window._trainingsSort === col) ? 'desc' : 'asc'`. When clicking a *new* column (not the currently sorted one), it will always toggle to 'asc'. However, the guard condition is correct, so clicking an already-sorted column ascending gives descending. But there is no reset to page 1 when sort column changes — only per-page and filter changes reset the page. | ⚪ Low | UX/Usability | `resources/views/trainings/index.blade.php:736–741` |
| 23 | **Inline `style` used for spinner color** — Multiple blade views use `style="color: var(--deped-primary);"` inline on spinners instead of a CSS class. While functional, it makes theming harder to maintain. | ⚪ Low | Visual Design | `resources/views/dashboard/personnel.blade.php:153`, `resources/views/personnel/index.blade.php:183`, `resources/views/trainings/index.blade.php:126` |
| 24 | **Login card is narrower than viewport at xl screens** — At 1280px width, the login card (`col-12 col-lg-10 col-xl-9`) is only ~1050px wide, centered, which is fine. But there is no `max-width` on the dashboard shell at 1600px that applies to guest pages, so the background gradient extends to the full viewport while the card is constrained, creating a slight disconnect. A `max-width` on `.login-viewport` matching the dashboard shell would look more polished. | ⚪ Low | Visual Design | `resources/views/auth/login.blade.php:9–17` (`.login-viewport`) |

---

## Criticality Legend

- 🔴 **Critical**: Breaks functionality or violates accessibility standards
- 🟠 **High**: Significantly impacts user experience or design quality
- 🟡 **Medium**: Noticeable issue that should be addressed
- ⚪ **Low**: Nice-to-have improvement

---

## Next Steps

**Immediate (Critical)**
1. Fix `overflow-auto;` → `overflow: auto;` in `app.blade.php:173`
2. Move `#modalEditPersonnel` outside the flex container in `personnel/index.blade.php` to the bottom of the `@section('content')` block

**Short-term (High)**
3. Add `role="main"` (or convert to `<main>`) on `.content-area` in `app.blade.php`
4. Move register page `<style>` block to `@push('styles')`
5. Fix sidebar toggle `aria-expanded` state and add Escape key dismissal
6. Replace `<select multiple>` in import/assign modals with a searchable checkbox list
7. Change `<h4 class="page-title">` → `<h1 class="page-title">` across all page templates

**Medium-term**
8. Redesign `/register` to match `/login` visual quality (split-panel or at minimum a styled centered card with branding)
9. Add `autocomplete` attributes on all auth form fields
10. Fix `min-w-0` → use Bootstrap-compatible approach (e.g., `overflow-hidden` or a custom `.min-width-0` CSS rule)
11. Add `aria-current="page"` to the active sidebar nav link
12. Replace "Import from Excel" quick action tile with a modal trigger on the admin dashboard
13. Standardize action button sizes on the personnel profile page
