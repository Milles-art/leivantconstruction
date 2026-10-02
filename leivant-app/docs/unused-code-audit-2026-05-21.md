# Leivant Unused Code Audit - 2026-05-21

This is a safe-removal report, not an automatic deletion list. The live site is a Laravel app with public pages, provider dashboard pages, checkout/cart, and the new super-admin modules. The items below should be removed only after a backup and one final route/content check.

## Low-Risk Candidates

- `resources/views/seo/about.blade.php`
- `resources/views/seo/blog.blade.php`
- `resources/views/seo/careers.blade.php`
- `resources/views/seo/contact.blade.php`
- `resources/views/seo/faq.blade.php`
- `resources/views/seo/projects.blade.php`
- `resources/views/seo/quote.blade.php`
- `resources/views/seo/safety.blade.php`
- `resources/views/seo/services.blade.php`
- `resources/views/seo/solution.blade.php`

Reason: the current routes use `FrontendController` views and several legacy SEO paths now redirect to active pages. These files appear to be old page experiments or legacy SEO templates.

## Keep For Now

- `resources/views/seo/home.blade.php`
- `resources/views/seo/sitemap.blade.php`
- `resources/views/seo/partials/inquiry-form.blade.php`
- `resources/views/react-site.blade.php`
- `resources/js/live-navigation.js`

Reason: these may still be referenced by public controllers, old cached routes, or future front-end work. They should not be deleted without checking controller references and built assets.

## Do Not Remove

- Admin views under `resources/views/admin/**`
- Public views under `resources/views/products`, `services`, `discovery`, `contact`, `checkout`, `cart`, `orders`, `profile`, `auth`, and `layouts`
- Models/controllers/migrations tied to orders, payments, carts, products, providers, inquiries, project requests, and reviews

Reason: these are active app surfaces or linked database domains.

## Safer Cleanup Plan

1. Back up the candidate files into a dated server backup directory.
2. Temporarily move only the low-risk candidate SEO views.
3. Clear Laravel view/cache files.
4. Smoke test public routes, client flows, provider dashboard, and all admin pages.
5. Permanently delete only if no route, include, or controller reference breaks.

No live files were deleted during this audit pass.
