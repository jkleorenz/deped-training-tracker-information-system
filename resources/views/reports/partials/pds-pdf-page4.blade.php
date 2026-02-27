{{-- Page 4 of CS Form 212: Q34–40, References, Declaration, Govt ID, Photo --}}
<div class="pds-page4-wrap">
    <table class="pds-table" style="margin-bottom:0;">
        <tr><td class="pds-page4-section-title">ANSWER YES OR NO. IF YES, GIVE DETAILS.</td></tr>
    </table>
    <table class="pds-table pds-page4-yn-table" style="margin-top:0;">
        <tr>
            <th class="pds-q-col pds-yn-header">QUESTION</th>
            <th class="pds-yes-col pds-yn-header">YES</th>
            <th class="pds-no-col pds-yn-header">NO</th>
        </tr>
        <tr>
            <td class="pds-q-col">34. Are you related by consanguinity or affinity to the appointing or recommending authority, or to the chief of bureau or office or to the person who has immediate supervision over you in the Office, Bureau or Department where you will be appointed,</td>
            <td class="pds-yes-col" style="text-align:center;"></td>
            <td class="pds-no-col" style="text-align:center;"></td>
        </tr>
        <tr>
            <td class="pds-q-col">a. within the third degree?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->related_third_degree_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->related_third_degree_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr>
            <td class="pds-q-col">b. within the fourth degree (for Local Government Unit - Career Employees)?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->related_fourth_degree_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->related_fourth_degree_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ $p && $p->related_authority_details ? $p->related_authority_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">35. a. Have you ever been found guilty of any administrative offense?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->admin_offense_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->admin_offense_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->admin_offense_yn === 'Y' && $p->admin_offense_details) ? $p->admin_offense_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">b. Have you been criminally charged before any court?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->criminally_charged_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->criminally_charged_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->criminally_charged_yn === 'Y') ? trim(($p->criminally_charged_date_filed ?? '') . ' ' . ($p->criminally_charged_status ?? '') . ($p->criminally_charged_details ? ' — ' . $p->criminally_charged_details : '')) : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">36. Have you ever been convicted of any crime or violation of any law, decree, ordinance or regulation by any court or tribunal?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->convicted_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->convicted_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->convicted_yn === 'Y' && $p->convicted_details) ? $p->convicted_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">37. Have you ever been separated from the service in any of the following modes: resignation, retirement, dropped from the rolls, dismissal, termination, end of term, finished contract or phased out (abolition) in the public or private sector?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->separated_from_service_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->separated_from_service_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->separated_from_service_yn === 'Y' && $p->separated_from_service_details) ? $p->separated_from_service_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">38. a. Have you ever been a candidate in a national or local election held within the last year (except Barangay election)?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->candidate_election_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->candidate_election_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->candidate_election_yn === 'Y' && $p->candidate_election_details) ? $p->candidate_election_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">b. Have you resigned from the government service during the three (3)-month period before the last election to promote/actively campaign for a national or local candidate?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->resigned_campaign_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->resigned_campaign_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details: {{ ($p && $p->resigned_campaign_yn === 'Y' && $p->resigned_campaign_details) ? $p->resigned_campaign_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">39. Have you acquired the status of an immigrant or permanent resident of another country?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->immigrant_resident_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->immigrant_resident_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, give details (country): {{ ($p && $p->immigrant_resident_yn === 'Y' && $p->immigrant_resident_details) ? $p->immigrant_resident_details : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">40. Pursuant to: (a) Indigenous People's Act (RA 8371); (b) Magna Carta for Disabled Persons (RA 7277, as amended); and (c) Expanded Solo Parents Welfare Act (RA 11861), please answer the following items:</td>
            <td class="pds-yes-col"></td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">a. Are you a member of any indigenous group?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->indigenous_group_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->indigenous_group_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, please specify: {{ ($p && $p->indigenous_group_yn === 'Y' && !empty($p->indigenous_group_specify)) ? $p->indigenous_group_specify : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">b. Are you a person with disability?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->pwd_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->pwd_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, please specify ID No.: {{ ($p && $p->pwd_yn === 'Y' && $p->pwd_id_no) ? $p->pwd_id_no : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
        <tr>
            <td class="pds-q-col">c. Are you a solo parent?</td>
            <td class="pds-yes-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->solo_parent_yn === 'Y') ? '✓' : '' }}</span></td>
            <td class="pds-no-col" style="text-align:center;"><span class="pds-page4-cb">{{ ($p && $p->solo_parent_yn === 'N') ? '✓' : '' }}</span></td>
        </tr>
        <tr class="pds-page4-details-row">
            <td class="pds-q-col"></td>
            <td class="pds-yes-col">If YES, please specify ID No.: {{ ($p && $p->solo_parent_yn === 'Y' && $p->solo_parent_id_no) ? $p->solo_parent_id_no : '__________________________' }}</td>
            <td class="pds-no-col"></td>
        </tr>
    </table>

    <table class="pds-table" style="margin-top:0;">
        <tr><td class="pds-page4-section-title">41. REFERENCES (PERSON NOT RELATED BY CONSANGUINITY OR AFFINITY TO APPLICANT/APPOINTEE)</td></tr>
    </table>
    <table class="pds-table pds-page4-ref-table" style="margin-top:0;">
        <tr>
            <th class="pds-ref-th">NAME</th>
            <th class="pds-ref-th">OFFICE ADDRESS</th>
            <th class="pds-ref-th">CONTACT NO. / EMAIL</th>
        </tr>
        @for($i = 1; $i <= 3; $i++)
        @php $name = 'ref'.$i.'_name'; $contact = 'ref'.$i.'_contact'; $addr = 'ref'.$i.'_address'; @endphp
        <tr>
            <td class="pds-ref-td">{{ $val($p?->$name) }}</td>
            <td class="pds-ref-td">{{ $val($p?->$addr) }}</td>
            <td class="pds-ref-td">{{ $val($p?->$contact) }}</td>
        </tr>
        @endfor
    </table>

    <table class="pds-page4-bottom-table" style="width:100%; margin-top:0;" cellpadding="0" cellspacing="0">
        <tr>
            <td class="pds-page4-left-col">
                <div class="pds-page4-declaration-box">
                    <div class="declaration-text">I declare under oath that I have personally accomplished this Personal Data Sheet which is a true, correct, and complete statement pursuant to the provisions of pertinent laws, rules, and regulations of the Republic of the Philippines. I authorize the agency head/authorized representative to verify/validate the contents stated herein. I agree that any misrepresentation made in this document and its attachments shall cause the filing of administrative/criminal case/s against me.</div>
                    <table class="pds-table govt-id-table" style="margin-top:1px; width:100%;">
                        <tr>
                            <td class="govt-id-label" style="width:28%;">Signature of Applicant</td>
                            <td style="width:32%;"><span class="pds-page4-sig-box"></span></td>
                            <td class="govt-id-label" style="width:18%;">Date Accomplished</td>
                            <td style="width:22%;">{{ $dateVal($p?->date_accomplished) }}</td>
                        </tr>
                    </table>
                    <table class="pds-table govt-id-table" style="margin-top:0; width:100%;">
                        <tr><td class="pds-page4-section-title" colspan="3">GOVERNMENT ISSUED ID</td></tr>
                        <tr>
                            <td class="govt-id-label">Government Issued ID</td>
                            <td class="govt-id-label">ID No.</td>
                            <td class="govt-id-label">Date/Place of Issuance</td>
                        </tr>
                        <tr>
                            <td>{{ $val($p?->govt_id_type) }}</td>
                            <td>{{ $val($p?->govt_id_number) }}</td>
                            <td>{{ $val($p?->govt_id_place_date_issue) }}</td>
                        </tr>
                    </table>
                </div>
                <table class="pds-table" style="margin-top:1px; width:100%;"><tr><td class="pds-page4-section-title">OATH SECTION</td></tr></table>
                <div class="pds-page4-oath-wrap">
                    <div class="pds-page4-oath-area">SUBSCRIBED AND SWORN to before me this __________ day of __________, affiant exhibiting his/her validly issued government ID as indicated above.</div>
                    <div class="pds-page4-oath-label">Person Administering Oath</div>
                </div>
                <table class="pds-table" style="margin-top:1px; width:100%;"><tr><td style="font-size:7pt; font-weight:bold;">Signature:</td></tr></table>
                <div class="pds-page4-oath-wrap">
                    <div class="pds-page4-oath-area">(wet signature / e-signature / digital certificate except for notary public)</div>
                    <div class="pds-page4-oath-label">Official Stamp Area</div>
                </div>
            </td>
            <td class="pds-page4-right-col">
                <div class="pds-page4-photo-box" style="{{ $photoPathAbsolute ? 'padding:0;' : '' }}">
                    @if($photoPathAbsolute)
                        <img src="{{ $photoPathAbsolute }}" alt="" class="pds-page4-photo-img" style="width:4.5cm;height:3.5cm;max-width:100%;max-height:100%;object-fit:cover;object-position:center;display:block;">
                    @else
                        Passport-sized 4.5 cm × 3.5 cm
                    @endif
                </div>
                <div class="pds-page4-photo-label">PHOTO</div>
                <div class="pds-page4-thumbmark-wrap">
                    <div class="pds-page4-thumbmark-area"></div>
                    <div class="pds-page4-thumbmark-label">Right Thumbmark</div>
                </div>
            </td>
        </tr>
    </table>
</div>
