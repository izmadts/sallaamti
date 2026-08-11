<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Each column gets its own guarded Schema::table() call — idempotent by
    // design, since this migration has already been partially applied on at
    // least one environment without the run getting recorded (out-of-band
    // column, interrupted deploy, etc.), and re-running it must not fail on
    // whatever subset already exists.
    private function addColumnIfMissing(string $column, \Closure $definition): void
    {
        if (!Schema::hasColumn('nikah_profiles', $column)) {
            Schema::table('nikah_profiles', $definition);
        }
    }

    public function up(): void
    {
        // Suspend bug fix — a separate admin-only gate so a suspended
        // member's own "Activate My Profile" toggle can't undo it.
        $this->addColumnIfMissing('suspended_at', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('is_active');
        });
        $this->addColumnIfMissing('suspension_reason', function (Blueprint $table) {
            $table->string('suspension_reason')->nullable()->after('suspended_at');
        });

        // Deen / lifestyle compatibility fields
        $this->addColumnIfMissing('prayer_frequency', function (Blueprint $table) {
            $table->string('prayer_frequency')->nullable()->after('sect'); // always, usually, sometimes, rarely
        });
        $this->addColumnIfMissing('hijab_or_beard', function (Blueprint $table) {
            $table->string('hijab_or_beard')->nullable()->after('prayer_frequency'); // yes, no, sometimes
        });
        $this->addColumnIfMissing('smokes', function (Blueprint $table) {
            $table->string('smokes')->nullable()->after('hijab_or_beard'); // no, occasionally, yes
        });
        $this->addColumnIfMissing('diet', function (Blueprint $table) {
            $table->string('diet')->nullable()->after('smokes'); // halal_only, halal_mostly, no_restriction
        });

        // Ethnicity / language
        $this->addColumnIfMissing('ethnicity', function (Blueprint $table) {
            $table->string('ethnicity')->nullable()->after('country');
        });
        $this->addColumnIfMissing('language', function (Blueprint $table) {
            $table->string('language')->nullable()->after('ethnicity');
        });

        // Polygamy-intent (mainly relevant on female profiles browsing male
        // "married / second wife" status, but harmless on any profile)
        $this->addColumnIfMissing('open_to_polygamy', function (Blueprint $table) {
            $table->boolean('open_to_polygamy')->nullable()->after('marital_status');
        });

        // Trust badge stack — a third, independently-earned signal beyond
        // payment and CNIC: admin manually confirms the guardian contact
        // by phone and marks it here.
        $this->addColumnIfMissing('guardian_verified_at', function (Blueprint $table) {
            $table->timestamp('guardian_verified_at')->nullable()->after('verification_status');
        });

        // "Last active" — touched whenever the owner hits a Nikah route.
        $this->addColumnIfMissing('last_active_at', function (Blueprint $table) {
            $table->timestamp('last_active_at')->nullable()->after('guardian_verified_at');
        });

        // App-level fraud defense: a real person has exactly one CNIC.
        // Confirmed no existing duplicates before adding this (dev DB: 0/4).
        $indexExists = collect(DB::select(
            "SHOW INDEXES FROM nikah_profiles WHERE Key_name = 'nikah_profiles_cnic_number_unique'"
        ))->isNotEmpty();

        if (!$indexExists) {
            Schema::table('nikah_profiles', function (Blueprint $table) {
                $table->unique('cnic_number');
            });
        }
    }

    public function down(): void
    {
        $indexExists = collect(DB::select(
            "SHOW INDEXES FROM nikah_profiles WHERE Key_name = 'nikah_profiles_cnic_number_unique'"
        ))->isNotEmpty();

        if ($indexExists) {
            Schema::table('nikah_profiles', function (Blueprint $table) {
                $table->dropUnique(['cnic_number']);
            });
        }

        $columns = [
            'suspended_at',
            'suspension_reason',
            'prayer_frequency',
            'hijab_or_beard',
            'smokes',
            'diet',
            'ethnicity',
            'language',
            'open_to_polygamy',
            'guardian_verified_at',
            'last_active_at',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('nikah_profiles', $column)) {
                Schema::table('nikah_profiles', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
