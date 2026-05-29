<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Performance indexes pass.
 *
 * Adds covering / composite indexes on the columns the admin dashboard
 * and public blog routes filter and order by on every request. None
 * of these tables had an explicit index strategy when they were
 * created — every filter was a full scan, which is fine at 50 rows
 * and a death sentence at 50,000.
 *
 * Each index is wrapped in a try/catch so the migration is idempotent
 * across re-runs and tolerates existing indexes from earlier ad-hoc
 * DBA work.
 */
return new class extends Migration
{
    public function up(): void
    {
        // contact_messages — admin filters by status (read/unread)
        // and sorts by created_at descending on the dashboard. Also
        // searched by email for "find a lead" workflows.
        $this->addIndex('contact_messages', ['is_read', 'created_at'], 'idx_contact_unread_recent');
        $this->addIndex('contact_messages', ['email'], 'idx_contact_email');
        $this->addIndex('contact_messages', ['created_at'], 'idx_contact_created');

        // demo_requests — dashboard filters by status + is_instant_trial,
        // sorts by created_at. Trial expiry job filters by trial_ends_at.
        $this->addIndex('demo_requests', ['status', 'created_at'], 'idx_demo_status_recent');
        $this->addIndex('demo_requests', ['email'], 'idx_demo_email');
        $this->addIndex('demo_requests', ['created_at'], 'idx_demo_created');
        // Only add the trial-expiry index if the column exists (added
        // by a later migration; safety net for fresh installs).
        if (Schema::hasColumn('demo_requests', 'trial_ends_at')) {
            $this->addIndex('demo_requests', ['trial_ends_at'], 'idx_demo_trial_ends');
        }

        // blog_posts — public /blog query filters by status+published_at,
        // orders by published_at desc. The single-column index on
        // `slug` is auto-created by the schema's unique constraint, so
        // we focus on the published-list path.
        $this->addIndex('blog_posts', ['status', 'published_at'], 'idx_blog_published_recent');
        $this->addIndex('blog_posts', ['category_id', 'status', 'published_at'], 'idx_blog_category_recent');
        $this->addIndex('blog_posts', ['is_featured', 'published_at'], 'idx_blog_featured_recent');

        // case_studies — same shape as blog
        $this->addIndex('case_studies', ['status', 'published_at'], 'idx_case_published_recent');

        // subscriptions — dashboard MRR query filters by status and
        // joins payments. The status+plan_id composite covers the
        // "active by plan" cohort query the analytics page runs.
        $this->addIndex('subscriptions', ['status'], 'idx_sub_status');
        $this->addIndex('subscriptions', ['status', 'plan_id'], 'idx_sub_status_plan');
        $this->addIndex('subscriptions', ['demo_request_id'], 'idx_sub_demo');
        // Renewal cron groups by next_billing_date — add only if column
        // exists in this schema version.
        if (Schema::hasColumn('subscriptions', 'next_billing_date')) {
            $this->addIndex('subscriptions', ['status', 'next_billing_date'], 'idx_sub_billing_due');
        }

        // invoices — invoice list filters by paid/unpaid, sorts by date
        $this->addIndex('invoices', ['status', 'created_at'], 'idx_inv_status_recent');
        $this->addIndex('invoices', ['subscription_id'], 'idx_inv_sub');

        // payments — webhook lookup is by paymob_transaction_id (or
        // equivalent), refund path filters by status
        if (Schema::hasColumn('payments', 'paymob_transaction_id')) {
            $this->addIndex('payments', ['paymob_transaction_id'], 'idx_pay_paymob_txn');
        }
        $this->addIndex('payments', ['invoice_id'], 'idx_pay_invoice');
        $this->addIndex('payments', ['status', 'created_at'], 'idx_pay_status_recent');

        // newsletter_subscribers — bulk send filters by is_active
        if (Schema::hasTable('newsletter_subscribers')) {
            $this->addIndex('newsletter_subscribers', ['email'], 'idx_news_email');
            $this->addIndex('newsletter_subscribers', ['is_active', 'created_at'], 'idx_news_active_recent');
        }

        // activity_logs — admin audit view filters by user + recent
        if (Schema::hasTable('activity_logs')) {
            $this->addIndex('activity_logs', ['user_id', 'created_at'], 'idx_log_user_recent');
            $this->addIndex('activity_logs', ['created_at'], 'idx_log_created');
        }
    }

    public function down(): void
    {
        // No-op. Dropping indexes is rare and risky in a rollback —
        // leave them in place. A real teardown would belong in a
        // separate migration with explicit names.
    }

    /**
     * Add a single index, swallowing "already exists" errors so the
     * migration is safe to re-run on databases that have been
     * manually patched.
     */
    protected function addIndex(string $table, array $columns, string $name): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }
        // Guard against any missing column in the array.
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return;
            }
        }
        try {
            Schema::table($table, function (Blueprint $tbl) use ($columns, $name) {
                $tbl->index($columns, $name);
            });
        } catch (\Throwable $e) {
            // Most likely: "Duplicate key name" — index already exists.
            // Safe to ignore; we just want to make sure it's present.
        }
    }
};
