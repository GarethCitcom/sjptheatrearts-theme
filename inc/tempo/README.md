# Tempo Book It, public timetable feed schema

Documented from plugin source, not guessed. Moves to `inc/tempo/README.md` when
phase 5 scaffolds the client.

- **Endpoint**: `GET https://book.sjptheatrearts.co.uk/wp-json/dsbook/v1/timetable`
- **Source of truth**: `includes/Rest/TimetableController.php` in
  `GarethCitcom/tempo-book-it`. Local clone inspected:
  `c:\laragon\www\sjptheatrearts\wp-content\plugins\tempo-book-it`
  (commit `91ad5e5`, plugin version 0.9.2, the newest of the four local copies).

  The timetable feed documented below matches the live portal, verified against
  it. Nothing else in the clone has been checked against production, and its
  account area covers only bookings, credit and students, so re-pull before
  relying on it for anything beyond the feed.
- **Supporting classes read**: `Rest/RestApi.php`, `Services/SettingsService.php`,
  `Support/Time.php`, `Database/Repositories/TermRepository.php`,
  `Database/Repositories/ScheduleRepository.php`.

## Verified live state (2026-08-07)

```
HTTP 200
Cache-Control: public, max-age=300
{"timezone":"Europe\/London","term":null,"half_term":null,
 "exclusions":[],"terms":[],"classes":[]}
```

**Diagnosis, this is not "the feed is still being populated".** The 200 proves the
feed toggle is on and the licence gate is satisfied. `terms: []` comes from
`TermRepository::current_and_upcoming()`, which selects terms with
`status = 'active' AND end_date >= today`. Empty means **no active, unexpired term
exists in the portal**. `resolve_current()` then returns `null`, and because
`classes` is only populated inside `if ( null !== $term )`, the class list is
necessarily empty too.

So the feed will stay empty, no matter how many classes are entered, until
someone creates an active term with a future `end_date` in Tempo → Terms, and
attaches schedules to it. That is a client/portal action, not a build blocker,
but it must be raised before phase 5 sign-off.

## Request

| Param | Type | Default | Notes |
| --- | --- | --- | --- |
| `term` | integer ≥ 0 | `0` | `0` = resolve current term. An unknown id **falls back** to the current term rather than erroring, so stale links keep working. |

## Access gates (both can fail in production)

| Condition | Response | Source |
| --- | --- | --- |
| Feed toggle off (Tempo → Settings → Timetable) | **403** `dsb_timetable_feed_disabled` | `TimetableController::check_enabled()` |
| Site unlicensed | **402** `dsb_not_licensed` | `RestApi::block_when_unlicensed()` via `rest_pre_dispatch` |
| OK | 200 + `Cache-Control: public, max-age=300` | `get_timetable()` |

The 402 is the one to design for: **a licence lapse on the portal silently kills
the marketing site's timetable.** No auth is required and no API key exists, the
brief's "API key/auth field" should therefore be omitted, not left empty (see
CLAUDE.md conventions). Our client must treat 402/403 exactly like a network
failure: fall through to last-known-good, then to the "to confirm" state.

## Response envelope

```jsonc
{
  "timezone":   "Europe/London",   // constant Time::LOCAL_TZ, always this string
  "term":       Term|null,         // null when no active term resolves
  "half_term":  Exclusion|null,    // first exclusion of type "half_term_break"
  "exclusions": [Exclusion],       // half-term break sorted first, then by start_date
  "terms":      [TermSummary],     // current + upcoming, soonest first
  "classes":    [ClassItem]        // [] whenever term is null
}
```

### TermSummary (in `terms[]`)

| Field | Type |
| --- | --- |
| `id` | int |
| `name` | string |
| `start_date` | string `Y-m-d` |
| `end_date` | string `Y-m-d` |

### Term (in `term`), TermSummary plus display strings

| Field | Type | Notes |
| --- | --- | --- |
| `start_date_display` | string | Formatted by the **portal's** `date_format` option, London zone |
| `end_date_display` | string | Same |

### Exclusion

| Field | Type | Notes |
| --- | --- | --- |
| `type` | string | Only two values exist: `half_term_break`, `excluded_date` (validated in `Admin/TermsPage.php`) |
| `label` | string | Free text |
| `start_date` / `end_date` | string `Y-m-d` | |
| `start_date_display` / `end_date_display` | string | Portal-formatted |

### ClassItem

| Field | Type | Notes |
| --- | --- | --- |
| `schedule_id` | int | Stable key for our slug-override mapping |
| `name` | string | Class name |
| `day_of_week` | int | **ISO: 1 = Monday … 7 = Sunday** |
| `day_name` | string | Localised on the portal, do **not** parse it; key our grid off `day_of_week` |
| `start_time` | string `H:i:s` | |
| `end_time` | string `H:i:s` | Derived `start + duration_minutes` |
| `time_range` | string | e.g. `10:00 – 10:45`, joined with an **en dash**, portal `time_format` |
| `duration_minutes` | int | |
| `ages` | string | First non-empty of `age_range`, `school_year`, `grade`, `ability`. **May be `""`** |
| `class_type` | string | |
| `colour` | string | Class colour from the portal, ignore it; our palette governs |
| `description` | string | May be `""` |
| `location` | string | The `studio` column. May be `""` |
| `teachers` | string[] | Display names only. **May be `[]`** |
| `online_open` | bool | `false` = invitation-only / not bookable online. Design calls for a badge, not omission |

Ordering is `day_of_week, start_time, class_name`.

### Deliberately absent

No pricing, capacity, staff notes, teacher IDs/logins/emails. **Fees can never come
from this feed**, they stay ACF options with an awaiting-confirmation state.

## Build consequences

1. Key the week grid off `day_of_week` (int), never `day_name` (localised string).
2. Treat `ages`, `description`, `location`, `teachers` as optional, all can be empty.
3. Render `online_open: false` rows with the invitation-only badge; do not hide them.
4. Escape everything: `name`, `description`, `location`, `teachers`, `label` are
   free text authored on a different site.
5. `timezone` is a constant; honour it for any "correct as of" stamp, but there is
   no per-class timezone to reconcile.
6. Remote `Cache-Control` is 300s; our transient TTL default of 15 min sits above
   it, which is fine, the cron prewarm is what keeps visitors off the round trip.
7. `term: null` is a **normal** state, and currently the live one. It renders the
   designed "to confirm" timetable, not an error.
