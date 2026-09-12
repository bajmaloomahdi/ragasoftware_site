<?php

use Database\Seeders\Support\SeedMedia;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The site's design tokens moved from Indigo/Violet to Teal/Cyan, but
     * the seeded placeholder SVGs (product mockups, customer/team logos,
     * OG images) had the old colors baked directly into their file bytes.
     * Rewrites each one in place — same Media row, same id/path, so nothing
     * that references it (products, customers, team members, …) breaks —
     * just refreshed colors. Safe to run more than once: it always
     * regenerates from the row's own stored title/size, never duplicates
     * anything.
     */
    public function up(): void
    {
        SeedMedia::regenerateAll();
    }

    public function down(): void
    {
        // Colors are baked into file bytes, not stored separately — nothing
        // meaningful to revert to without the old constants.
    }
};
