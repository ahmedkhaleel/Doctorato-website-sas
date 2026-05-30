# Responsible Disclosure Policy

**Last updated: 2026-05-30**

## Our promise

If you've found a security vulnerability in Doctorato, we want to
hear about it before an attacker does. This document explains:

- How to report a vulnerability safely.
- What we will and won't do in response.
- What we ask of you in return.

There is **no bug bounty program** at this time — Doctorato is a
small team and we cannot commit to monetary rewards. We can offer
public acknowledgement (with your permission) and a written
thank-you for responsible reports.

---

## Reporting

Email **security@doctorato.com** with:

1. A description of the vulnerability and its impact.
2. Reproduction steps detailed enough for us to verify.
3. Any proof-of-concept code or screenshots (please don't include
   real customer data — synthetic accounts are fine for the demo).
4. Your name or handle for acknowledgement (optional).

PGP encryption is welcome. Our key fingerprint will be published
at `https://doctorato.com/.well-known/security-pgp.asc` once
generated.

We aim to:
- **Acknowledge** your report within **48 business hours**.
- **Confirm or dispute** the vulnerability within **5 business days**.
- **Ship a fix** or document a mitigation timeline within **7 days**
  of confirmation for High/Critical issues, **30 days** for Medium,
  best-effort for Low.

---

## Safe-harbour scope

We will not pursue legal or administrative action against
researchers who:

- Only test against accounts and data they own (or have explicit
  permission to test against).
- Use the minimum amount of data necessary to demonstrate the
  vulnerability — no scraping of customer lists or invoice data.
- Avoid degrading service for other users (no DoS / load testing
  without prior approval).
- Do not pivot from one finding to another customer's data.
- Give us a reasonable disclosure window (default 90 days).

This safe harbour covers:

- Our public-facing web app at `https://doctorato.com`
- Any subdomain ending in `.doctorato.com`
- Our public APIs once they ship

---

## Out of scope

Issues we do not currently treat as security vulnerabilities:

- Self-XSS that only affects the attacker's own browser session.
- SPF / DKIM / DMARC weaknesses without proof of real-world abuse.
- Outdated TLS cipher suites on the Cloudflare front-door (not
  our control).
- Missing security headers on static asset CDN URLs.
- Social-engineering attacks against our staff or customers.
- Issues requiring physical access to a victim's unlocked device.
- Vulnerabilities in third-party services we integrate with (please
  report those upstream — we'll help where we can).
- Click-jacking on pages without sensitive actions.
- Rate-limit findings on endpoints with documented throttle:
  values that match the limit on the page.

---

## What we ask

Please **do not**:

- Publicly disclose the vulnerability before we've shipped a fix
  (or before the 90-day window elapses).
- Access, modify, or delete customer data beyond what's strictly
  needed to demonstrate the bug.
- Run automated scanners against `/admin/*` or `/portal/*` without
  contacting us first — these surfaces are auth-gated and the noise
  drowns out real reports.
- Demand monetary reward as a condition of reporting. We don't run
  a bug bounty.

---

## Acknowledgements

Researchers who report valid issues will be listed here (with their
permission) after the fix ships.

*No public reports yet.*

---

## Hall of fame: how to get listed

If your report results in a fix and you opt-in, we'll list you
with:

- Name or handle
- Optional link (homepage / Twitter / GitHub)
- Date of fix
- One-line summary of the issue (sanitised — no exploitable detail)

We do NOT list bug-bounty researchers who reported on auto-scanner
output without verifying the finding manually.
