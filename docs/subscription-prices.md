# Price changes and pending payments

Annual charges are collections for a subscription year, starting in the anniversary
month of `started_on`. `period_year` names the starting year. Existing charge
amounts remain the accounting source of truth; changing a subscription default
alone does not rewrite their history.

Use **Subscriptions → Change monthly price** (also available on the edit page)
to enter a new monthly rate and the first day of its effective month. The modal
previews the adjusted annual collections and future full-year default. Apply
successive changes in date order. For an older correction after a later price
change, edit the affected charge directly instead. Existing payments are retained,
and adjustment notes are appended to each changed charge. No schema migration
or new subscription is needed.

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
