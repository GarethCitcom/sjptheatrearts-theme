# 301 redirect map

Built from the old site's own sitemap index on 2026-08-11:
`https://sjptheatrearts.co.uk/sitemaps.xml` (post, page and category sitemaps).

Implemented in [`inc/redirects.php`](../inc/redirects.php). It runs **only on a
404**, so a real page always wins and publishing a page at one of these paths
later retires its rule with no code change. Valid URLs pay nothing.

## Unchanged, no rule needed

The best outcome available: same address on both sites, so the existing search
ranking and any inbound links carry straight over.

| Path |
| --- |
| `/` |
| `/about/` |
| `/classes/` |
| `/contact/` |
| `/privacy-policy/` (exists, still a draft) |

## Moved

| Old | New | Why |
| --- | --- | --- |
| `/timetable/` | `/timetable-and-fees/` | Renamed; fees now live on the same page |
| `/gallery/` | `/performances/#gallery` | The gallery is a section of Performances |
| `/my-account/` | the member portal | Bookings, invoices and payment moved there |

## No equivalent: judgement calls

**These four are guesses at intent and worth a second opinion.** Each goes to the
nearest page that answers the same question rather than to the homepage, because
a visitor who clicked "room hire" is better served by Contact than by being
dropped at the front door.

| Old | New | Reasoning |
| --- | --- | --- |
| `/news/` | `/performances/` | No news section on the new site; Performances is where "what is happening" lives |
| `/room-hire/` | `/contact/` | Not offered on the new site as far as this build knows |
| `/alumni/` | `/about/` | The only page that covers the school's history |
| `/shop/`, `/cart/`, `/checkout/` | `/` | WooCommerce ran on the old install and does not here |

If room hire is still a service, it needs a page rather than a redirect.

## Retired content

Placeholder posts and plugin leftovers, none of which anybody linked to on
purpose. All go to the homepage.

| Old |
| --- |
| `/title-of-the-blog-post/`, `/title-of-the-blog-post-2/` |
| `/front-user-submit-form/`, `/fe-fs-user-admin/` |
| `/alton-towers-scarefest/`, `/summer-school-2022/` → `/performances/` (both subjects are still on that page) |

## Categories

The old site carried about sixty categories, every one a stock "Uncategorized"
in a different language, left behind by a translation plugin. `/category/*` is
matched by prefix and sent to the homepage; none is worth its own rule.

## Moving to the server

`inc/redirects.php` is portable and version-controlled with the theme, which is
why it lives there. If the host would rather serve these from Apache, the same
table translates directly:

```apache
RedirectMatch 301 ^/timetable/?$        /timetable-and-fees/
RedirectMatch 301 ^/gallery/?$          /performances/#gallery
RedirectMatch 301 ^/news/?$             /performances/
RedirectMatch 301 ^/room-hire/?$        /contact/
RedirectMatch 301 ^/alumni/?$           /about/
RedirectMatch 301 ^/(shop|cart|checkout)/?$ /
RedirectMatch 301 ^/category/.*         /
RedirectMatch 301 ^/my-account/?$       https://book.sjptheatrearts.co.uk/
```

Delete the PHP if you do, so the rules are not maintained in two places.

## At launch

1. Verify each rule against the live site once DNS has moved.
2. Submit the new sitemap (SiteSEO Pro's) in Search Console.
3. Watch Search Console's coverage report for 404s the sitemap did not list:
   the old sitemap only knows what it indexed, and inbound links may point at
   URLs that were never in it.
