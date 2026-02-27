<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Page 4: "If YES, give/please specify" fields per official PDS layout.
     */
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $add = function (string $col, string $type, ...$args) use ($table) {
                if (! Schema::hasColumn('personal_data_sheets', $col)) {
                    $table->{$type}($col, ...$args)->nullable();
                }
            };
            $add('related_authority_details', 'text');      // 34. If YES, give details
            $add('indigenous_group_specify', 'text');       // 39. If YES, please specify (TEXT to avoid row size limit)
            $add('pwd_id_no', 'text');                      // 40.a If YES, please specify ID No
            $add('solo_parent_id_no', 'text');              // 40.b If YES, please specify ID No
        });
    }

    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'related_authority_details',
                'indigenous_group_specify',
                'pwd_id_no',
                'solo_parent_id_no',
            ]);
        });
    }
};
