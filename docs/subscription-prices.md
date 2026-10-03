# Price changes and pending payments

Charges have optional **Covered from / Covered through** dates. Set both to use
calendar years or shorter transition periods, starting on the first day of a month
and ending on the last day of a month. `period_year` names the starting year.
Older charges without these dates retain their original anniversary-based year,
using the month of `started_on`. Price changes use each charge’s actual coverage. Existing charge
amounts remain the accounting source of truth; changing a subscription default
alone does not rewrite their history.

Use **Subscriptions → Change monthly price** (also available on the edit page)
to enter a new monthly rate and the first day of its effective month. The modal
previews the adjusted annual collections and future full-year default. Apply
successive changes in date order. For an older correction after a later price
change, edit the affected charge directly instead. Existing payments are retained,
and adjustment notes are appended to each changed charge. No new subscription is needed. Explicit coverage dates were added with a
nullable, additive migration; existing charges and payments are preserved.

For YouTube's November 2025–October 2026 collection: ten months at €20 plus two
months at €22 = **€244**, with **€264** as the next full-year default. The original
€240 amount already rounded the previous rate to €20/month.

**Pending Payments** groups remaining shares by person and lists the subscription
and collection year. The owner is treated as having funded their own share.
Partial payments reduce debt; overpayment on another charge or by another person
does not hide it. Shares use integer cents and distribute leftover cents in person
ID order, so €244 across six people totals exactly €244 (four shares of €40.67 and
two of €40.66). The payment form suggests the selected person's remaining amount.
Historical membership changes are still outside this model: keep each collection's
membership stable once payments exist.

**Recent Payments** starts collapsed, remembers its state in the browser and shows
10 transactions per page, newest first. Expand it to search or page through history.

## Moving collections to January–December

The October 2026 transition preserves all existing payment entries and Spotify's
past charges. Its existing May 2025–April 2026 charge remains €180. New coverage:

| Subscription | Covered period | Total |
| --- | --- | ---: |
| Spotify | May–December 2026 | €120 |
| Spotify | January–December 2027 | €180 |
| YouTube | November–December 2025 | €40 |
| YouTube | January–August 2026 | €160 |
| YouTube | September–December 2026 | €88 |
| YouTube | January–December 2027 | €264 |

YouTube's original unpaid November 2025–October 2026 record becomes its
November–December 2025 record; the remaining pieces are separate charges.
The extra November–December 2026 coverage adds €44, avoiding a gap before the
January 2027 calendar year. Do not shorten an already paid charge to move its
covered months into a new charge: that would make previously paid coverage look
unpaid again. Keep it intact and add the uncovered bridge instead.

## Planned charges

Future collections can be marked **Planned** on their charge edit/create form.
Planned charges remain visible with their coverage and price in the charges list,
but are excluded from unpaid balances, pending member payments, annual cost totals
and subscription cost comparisons. Price adjustments can still update planned
prices. Use **Activate** on a planned row when you want collection to begin; this
is a manual choice, not an automatic January date switch.

The Spotify and YouTube January–December 2027 charges are planned. Existing
payment entries are preserved. Charges with recorded payments cannot be changed
to planned, and payments cannot be recorded against a planned charge until it is
activated.
