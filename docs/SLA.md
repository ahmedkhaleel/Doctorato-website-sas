# Service Level Agreement (SLA)

**Effective: 2026-05-30 · Tier: General Customer (default)**

This SLA covers the Doctorato SaaS platform served under the
`*.doctorato.com` domain. Enterprise customers may negotiate a
tighter SLA in their Master Subscription Agreement; in case of
conflict, the negotiated terms prevail.

---

## 1. Uptime commitment

| Service | Target | Measurement window |
|---|---|---|
| Customer Portal (`/portal/*`) | **99.5%** | Calendar month |
| Public marketing site (`/`, `/pricing`, `/blog`, etc.) | 99.5% | Calendar month |
| Webhook ingestion (`/webhooks/paymob`) | **99.9%** | Calendar month |
| Healthchecks (`/healthz`, `/status`) | 99.9% | Calendar month |

**Exclusions** (downtime that does NOT count against the target):

- Scheduled maintenance announced at least 48 hours in advance on
  https://doctorato.com/status, capped at 2 hours per month.
- Force majeure (natural disaster, government action, sustained
  Cloudflare or upstream telco outage).
- Failure caused by the customer (incorrect API usage, credential
  misuse, ToS violation).
- Beta features marked as such in the documentation.

---

## 2. Incident severity

| Severity | Definition | Response target |
|---|---|---|
| **Sev-1** | Total platform outage; admin or portal unreachable for >5 min | Ack within **30 min**, resolution target **4 hours** |
| **Sev-2** | Significant degradation; one major surface (e.g. checkout) failing for a subset of customers | Ack within **2 hours**, target **24 hours** |
| **Sev-3** | Localised bug; workaround available; one customer affected | Ack within **1 business day**, target **5 business days** |
| **Sev-4** | Cosmetic or non-functional; no business impact | Ack within **3 business days**, no commitment |

Severity is assessed by Doctorato based on the report; the
customer can request re-classification by email.

---

## 3. Service credits

If the monthly uptime falls below the target for the Customer
Portal:

| Actual uptime | Service credit |
|---|---|
| < 99.5% and ≥ 99.0% | 5% of the monthly fee |
| < 99.0% and ≥ 95.0% | 15% of the monthly fee |
| < 95.0% | 30% of the monthly fee |

- Credits are issued against the next monthly invoice (or refunded
  in cash for annual customers within 30 days).
- The customer must request the credit in writing within 30 days of
  the affected month, attaching the dates and times of suspected
  downtime.
- Maximum credit per month is capped at 30% of the monthly fee.
- Credits are the customer's sole remedy for breach of uptime.

---

## 4. Disaster recovery objectives

| | Target | Documented in |
|---|---|---|
| **RTO** (read-only service restored) | **4 hours** | `DISASTER_RECOVERY.md` §2-3 |
| **RTO** (full read/write restored) | 24 hours | DR §3 |
| **RPO** (max data loss) | **24 hours** | nightly backup cadence |

Monthly restore verification drills documented in DR §9.

---

## 5. Support channels and hours

- **Email:** `support@doctorato.com` — primary channel for non-urgent.
- **Status page:** https://doctorato.com/status — incident updates in
  real time.
- **Sev-1 escalation:** Same email, subject prefixed `[SEV-1]` — the
  on-call engineer is paged.

**Coverage hours** (local time UTC+2):
- Sev-1: 24×7
- Sev-2: 09:00 – 22:00 daily
- Sev-3/4: 09:00 – 18:00 Sunday – Thursday

---

## 6. Customer responsibilities

The customer must:

- Keep their admin credentials secret and rotate them on staff
  changes.
- Apply available security updates to their browser and OS.
- Report suspected outages with timestamps and any error messages.
- Not run automated load tests against the production environment
  without 5 business days' notice.

---

## 7. Reporting and communication

- Status page updates: at least every 30 minutes for active Sev-1.
- Post-incident report: published on the status page within 5
  business days of resolution for Sev-1 incidents. Includes
  timeline, root cause, and prevention work.
- Quarterly uptime report: published on
  https://doctorato.com/status by the 10th of the following month.

---

## 8. Changes to this SLA

Doctorato may update this SLA on 30 days' notice via email to the
customer's billing contact. Reductions in commitments do not take
effect until the customer's next renewal.
