# Data Processing Addendum (DPA)

**Template version: 2026-05-30**

This template is signed alongside the Master Subscription Agreement
for any customer subject to GDPR, the Egyptian PDPL, or another
data-protection regime requiring a processor agreement. It is
designed to be country-neutral; jurisdiction-specific clauses
appear in **Section 9**.

> Markdown-style placeholders `{{ customer_legal_name }}` etc. are
> replaced by the legal team before signing — do not ship a copy
> with placeholders still present.

---

## 1. Parties

This Data Processing Addendum (the "**DPA**") is entered into
between:

- **{{ customer_legal_name }}**, {{ customer_address }} (the
  "**Controller**"); and
- **Doctorato**, {{ doctorato_address }} (the "**Processor**").

Together, the "**Parties**".

The DPA forms part of the Master Subscription Agreement dated
{{ msa_date }} (the "**Agreement**") and applies to any Processing
of Personal Data carried out by Doctorato on behalf of the
Controller in connection with the Agreement.

---

## 2. Definitions

Terms defined in the GDPR (Regulation (EU) 2016/679) carry the
same meaning in this DPA. "Personal Data", "Processing",
"Controller", "Processor", "Data Subject", and "Supervisory
Authority" are used as defined there.

"**Sub-processor**" means any third party engaged by the Processor
to Process Personal Data on behalf of the Controller.

"**Personal Data Breach**" has the meaning given in GDPR Art. 4(12).

---

## 3. Subject matter and duration

- **Subject matter:** Clinic management SaaS provided by Doctorato
  to the Controller, including subscription billing, appointment
  scheduling, patient records, and analytics.
- **Duration:** Effective for the term of the Agreement and any
  retention period required by Section 8.
- **Nature and purpose of Processing:** Providing the SaaS
  product, supporting customers, complying with billing and tax
  obligations, and improving service quality.
- **Types of Personal Data:** Names, email addresses, phone
  numbers, billing details, clinic/practice information,
  appointment metadata, and any patient data uploaded by the
  Controller's authorised users.
- **Categories of Data Subjects:** Controller's employees,
  contractors, and patients.

---

## 4. Processor obligations

The Processor will:

1. Process Personal Data only on documented instructions from the
   Controller (the Agreement and any written instruction).
2. Ensure persons authorised to Process Personal Data are bound
   by confidentiality.
3. Implement appropriate technical and organisational measures
   to protect Personal Data (Section 6).
4. Engage Sub-processors only as permitted by Section 5.
5. Assist the Controller in responding to Data Subject requests
   under GDPR Chapter III (within 10 business days of receipt).
6. Notify the Controller of any Personal Data Breach without
   undue delay and in any event within 72 hours of becoming aware.
7. At the Controller's choice, delete or return Personal Data at
   the end of provision of services, subject to Section 8.
8. Make available all information necessary to demonstrate
   compliance with Art. 28 GDPR.

---

## 5. Sub-processors

The Controller authorises Doctorato to engage the Sub-processors
listed at **https://doctorato.com/sub-processors**. Doctorato will
update that list at least 30 days before adding or replacing any
Sub-processor. The Controller may object in writing within the
30-day window; if no resolution is reached, the Controller may
terminate the affected Services without penalty for the unused
portion of any prepaid fees.

All Sub-processors are bound by data-protection terms at least
as protective as this DPA.

---

## 6. Security measures

Doctorato implements (at minimum):

- **Access control:** Role-based permissions, MFA on admin
  accounts, magic-link auth on the customer portal, sliding-
  window lockout after failed login attempts.
- **Encryption:** TLS 1.2+ in transit (via Cloudflare).
  Session payloads encrypted at rest. Production database
  credentials rotated annually.
- **Logging and audit:** Activity log retained for 365 days with
  permanent retention for security-relevant actions. Outbound
  email logged with hashed recipient. PII scrubber redacts
  emails/phones/cards from runtime logs.
- **Backups:** Nightly database backups retained 14 days, monthly
  restore verification.
- **Vulnerability management:** Dependency audit in CI on every
  PR. Security patches applied within 7 days for Critical, 30
  days for High.
- **Personnel:** All staff sign confidentiality agreements; access
  to production data limited to engineering on-call rotation.

Full technical detail is in `OPERATIONS.md` and `DISASTER_RECOVERY.md`,
available to the Controller's information security team upon
request under NDA.

---

## 7. International transfers

Personal Data is hosted in {{ hosting_region }} (currently Egypt
on shared cPanel infrastructure). Where transfer outside the
European Economic Area is required, Doctorato relies on the
European Commission's Standard Contractual Clauses (Commission
Decision 2021/914) or an alternative legal mechanism documented
in the Agreement.

---

## 8. Retention and deletion

- **During the term:** Personal Data is retained for the duration
  of the Agreement plus the periods stated in `CHANGELOG.md`
  (activity log 365d, email logs 90d, webhook events 180d, etc.).
- **Tax-mandated retention:** Financial records (invoices,
  payments, subscriptions) are retained for the period required
  by applicable tax law (5 years in Egypt). PII fields within
  those records are anonymised on Controller request.
- **Termination:** Within 30 days of termination, Doctorato will
  delete or return all Personal Data not subject to mandatory
  retention. A certificate of deletion is provided on request.

---

## 9. Jurisdiction-specific addenda

### 9.1 GDPR (EU/EEA Controllers)

- The Controller appoints Doctorato as a Processor.
- Standard Contractual Clauses (2021/914) Module 2 apply where
  Personal Data leaves the EEA.
- Audit rights: The Controller may audit Doctorato's compliance
  no more than once per year and on 30 days' written notice. Audit
  scope is limited to records relevant to this DPA and excludes
  other customers' data.

### 9.2 Egyptian PDPL (Law 151/2020)

- Doctorato registers as a Data Processor with the Personal Data
  Protection Centre where the Controller is established in Egypt.
- Cross-border transfers comply with PDPL Art. 14.

### 9.3 HIPAA (USA Controllers)

- This DPA does NOT constitute a Business Associate Agreement.
  HIPAA-regulated Controllers must execute the separate BAA
  available on request. Until a BAA is executed, the Controller
  will NOT upload Protected Health Information into Doctorato.

---

## 10. Liability

This DPA is subject to the limitation of liability provisions in
the Agreement.

---

## 11. Signatures

| **For the Controller** | **For Doctorato** |
|---|---|
| Name: {{ controller_signer }} | Name: |
| Title: | Title: |
| Date: | Date: |
| Signature: | Signature: |
