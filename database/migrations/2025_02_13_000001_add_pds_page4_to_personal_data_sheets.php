<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * CS Form No. 212 Revised 2025 — Page 4 of 4 (Questions 34–44, References, Govt ID, Declaration)
     */
    public function up(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $add = function (string $col, string $type, ...$args) use ($table) {
                if (! Schema::hasColumn('personal_data_sheets', $col)) {
                    $table->{$type}($col, ...$args)->nullable();
                }
            };
            // Q34–44 (Y/N + details)
            $add('admin_offense_yn', 'string', 5);
            $add('admin_offense_details', 'text');
            $add('related_third_degree_yn', 'string', 5);
            $add('related_fourth_degree_yn', 'string', 5);
            $add('indigenous_group_yn', 'string', 5);
            $add('pwd_yn', 'string', 5);
            $add('solo_parent_yn', 'string', 5);
            $add('separated_from_service_yn', 'string', 5);
            $add('separated_from_service_details', 'text');
            $add('immigrant_resident_yn', 'string', 5);
            $add('immigrant_resident_details', 'string', 255);
            $add('candidate_election_yn', 'string', 5);
            $add('candidate_election_details', 'text');
            $add('resigned_campaign_yn', 'string', 5);
            $add('resigned_campaign_details', 'text');
            $add('criminally_charged_yn', 'string', 5);
            $add('criminally_charged_date_filed', 'string', 100);
            $add('criminally_charged_status', 'string', 255);
            $add('criminally_charged_details', 'text');
            $add('convicted_yn', 'string', 5);
            $add('convicted_details', 'text');
            // References (3)
            $add('ref1_name', 'string', 255);
            $add('ref1_contact', 'string', 255);
            $add('ref1_address', 'text');
            $add('ref2_name', 'string', 255);
            $add('ref2_contact', 'string', 255);
            $add('ref2_address', 'text');
            $add('ref3_name', 'string', 255);
            $add('ref3_contact', 'string', 255);
            $add('ref3_address', 'text');
            // Government ID & date accomplished
            $add('govt_id_type', 'string', 100);
            $add('govt_id_number', 'string', 100);
            $add('govt_id_place_date_issue', 'string', 255);
            $add('date_accomplished', 'date');
        });
    }

    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $cols = [
                'admin_offense_yn', 'admin_offense_details', 'related_third_degree_yn', 'related_fourth_degree_yn',
                'indigenous_group_yn', 'pwd_yn', 'solo_parent_yn', 'separated_from_service_yn', 'separated_from_service_details',
                'immigrant_resident_yn', 'immigrant_resident_details', 'candidate_election_yn', 'candidate_election_details',
                'resigned_campaign_yn', 'resigned_campaign_details', 'criminally_charged_yn', 'criminally_charged_date_filed',
                'criminally_charged_status', 'criminally_charged_details', 'convicted_yn', 'convicted_details',
                'ref1_name', 'ref1_contact', 'ref1_address', 'ref2_name', 'ref2_contact', 'ref2_address',
                'ref3_name', 'ref3_contact', 'ref3_address', 'govt_id_type', 'govt_id_number', 'govt_id_place_date_issue', 'date_accomplished',
            ];
            $table->dropColumn($cols);
        });
    }
};
