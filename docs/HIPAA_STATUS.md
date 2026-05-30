# HIPAA Compliance Status

**Current status: NOT HIPAA-compliant. Do not upload PHI.**

**Last reviewed: 2026-05-30**

---

## Where we stand

Doctorato handles healthcare-related data (appointments, patient
demographics, clinic records) but is **not** currently configured
or contracted to meet the requirements of the US Health Insurance
Portability and Accountability Act (HIPAA).

Customers regulated by HIPAA — covered entities, business
associates of covered entities, or subcontractors thereof — must
NOT upload **Protected Health Information (PHI)** to Doctorato
until the conditions in **§ Roadmap** below are met.

We will refuse to sign a Business Associate Agreement (BAA) until
the technical and contractual prerequisites are in place. Signing
a BAA we cannot meet is a regulatory hazard for both parties.

---

## Why the gap exists

1. **Hosting location.** Production runs on Egyptian cPanel
   infrastructure. HIPAA's Security Rule does not require US-only
   hosting, but the data residency and breach-notification regime
   we have today is built around GDPR and the Egyptian PDPL, not
   the HIPAA Breach Notification Rule.
2. **Encryption.** TLS in transit is in place. Field-level
   encryption at rest for PHI is NOT — sensitive columns are
   stored in plaintext MySQL.
3. **Audit logging.** Our activity log covers admin actions and
   security events, but does NOT yet log every read of a patient
   record (HIPAA § 164.312(b) requires audit controls for record
   access, not just modification).
4. **Vendor agreements.** Cloudflare, Paymob, and other sub-
   processors are not under BAAs with Doctorato today. Each
   requires negotiation with the vendor before BAA-equivalent
   protections apply.
5. **Workforce training.** Our team has not completed formal
   HIPAA awareness training. Required under the Administrative
   Safeguards § 164.308.

---

## What we DO have today (overlap with HIPAA Security Rule)

The work shipped in the operational-hardening roadmap (Phases
1-30) covers a substantial portion of the HIPAA Technical
Safeguards already:

| HIPAA citation | Covered by |
|---|---|
| § 164.312(a)(1) Access control | RBAC permissions, magic-link auth, MFA |
| § 164.312(a)(2)(iv) Encryption (transmission) | TLS 1.2+ via Cloudflare |
| § 164.312(b) Audit controls | activity_logs with 365-day retention + permanent flags |
| § 164.312(c) Integrity | DB constraints, idempotent migrations, webhook event store |
| § 164.312(d) Person/entity authentication | Magic link + 2FA + LoginAttemptTracker lockout |
| § 164.312(e)(1) Transmission security | HSTS, secure cookies, CSP with nonce |
| § 164.308(a)(1)(ii)(D) Information system activity review | activity log UI + email log + webhook event UI |
| § 164.308(a)(7) Contingency plan | DISASTER_RECOVERY.md |

These are **necessary but not sufficient** for HIPAA compliance.

---

## Roadmap to HIPAA-readiness

A full HIPAA readiness program would take an estimated **3 to 6
months** of engineering plus legal and audit work. The high-level
phases:

### Phase A — Infrastructure (8 weeks)

1. Move production to a HIPAA-eligible hosting provider with a
   signed BAA (AWS, Google Cloud, Azure all offer this).
2. Implement field-level encryption (AES-256 with KMS-managed
   keys) for any column flagged as PHI.
3. Configure the WAF to block uploads matching known PHI
   patterns from non-BAA customers.

### Phase B — Application (6 weeks)

1. Add read-side audit logging for every PHI access (currently
   we only log writes).
2. Implement automatic logout after 15 min of inactivity for
   PHI surfaces.
3. Build a per-customer data export that includes the audit
   log of accesses (patient right of access).
4. Add break-the-glass override for emergency clinical access
   with explicit logging.

### Phase C — Process and contract (8 weeks)

1. Negotiate BAAs with Cloudflare, Paymob, the email provider,
   and any other sub-processor that may touch PHI.
2. Author the HIPAA Privacy Policy and Notice of Privacy
   Practices.
3. Complete workforce HIPAA awareness training; document
   completion.
4. Run a third-party HIPAA gap assessment.
5. Engage outside counsel to draft our standard BAA template.

### Phase D — Ongoing (post-launch)

1. Annual risk assessment per § 164.308(a)(1)(ii)(A).
2. Quarterly access reviews.
3. Breach response runbook drill twice a year.

---

## Customer obligation

Until the roadmap above is complete and we explicitly publish
"Doctorato is HIPAA-compliant" on this page:

1. **Do not upload PHI**, including patient names, addresses,
   dates of birth, medical record numbers, or any other identifier
   listed in § 164.514(b)(2)(i).
2. **Do not sign a BAA with Doctorato.** We will refuse.
3. **Do not represent to your patients or auditors** that
   Doctorato is HIPAA-compliant.

If you have already uploaded PHI under a misunderstanding, contact
`security@doctorato.com` immediately so we can coordinate
remediation under your Breach Notification obligations.

---

## Questions

Email `compliance@doctorato.com` with any HIPAA-related question.
We will respond within 5 business days.
