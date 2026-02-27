{{-- Page 3 of CS Form 212: VI. Voluntary Work, VII. L&D, VIII. Other Information --}}
@php
    $voluntaryRows = $p ? $p->voluntaryWorks->sortBy('sort_order')->values() : collect();
    $ldRows = $p ? $p->learningDevelopments->sortBy('sort_order')->values() : collect();
@endphp

<table class="pds-table" style="margin-top:0;">
    <tr>
        <td colspan="5" class="section-header">29. VI. VOLUNTARY WORK OR INVOLVEMENT IN CIVIC / NON-GOVERNMENT / PEOPLE / VOLUNTARY ORGANIZATION/S</td>
    </tr>
    <tr>
        <td class="label-cell" style="width:30%;">NAME &amp; ADDRESS OF ORGANIZATION</td>
        <td class="label-cell" style="width:14%;">INCLUSIVE DATES (From)</td>
        <td class="label-cell" style="width:14%;">(To)</td>
        <td class="label-cell" style="width:10%;">NUMBER OF HOURS</td>
        <td class="label-cell" style="width:32%;">POSITION / NATURE OF WORK</td>
    </tr>
    @forelse($voluntaryRows as $v)
    <tr>
        <td class="value-cell">{{ $val($v->conducted_sponsored_by) }}</td>
        <td class="value-cell">{{ $v->inclusive_dates_from ? $dateVal($v->inclusive_dates_from) : $na }}</td>
        <td class="value-cell">{{ $v->inclusive_dates_to ? $dateVal($v->inclusive_dates_to) : $na }}</td>
        <td class="value-cell">{{ $v->number_of_hours !== null && $v->number_of_hours !== '' ? $v->number_of_hours : $na }}</td>
        <td class="value-cell">{{ $val($v->position_nature_of_work) }}</td>
    </tr>
    @empty
    <tr>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
    </tr>
    @endforelse
</table>
<p style="font-size:6px;margin-top:0;">(Continue on separate sheet if necessary)</p>

<table class="pds-table" style="margin-top:2px;">
    <tr>
        <td colspan="6" class="section-header">30–33. VII. LEARNING AND DEVELOPMENT (L&amp;D) INTERVENTIONS/TRAINING PROGRAMS ATTENDED</td>
    </tr>
    <tr>
        <td class="label-cell" style="width:28%;">TITLE OF L&amp;D (Write in full)</td>
        <td class="label-cell" style="width:18%;">TYPE OF L&amp;D</td>
        <td class="label-cell" style="width:10%;">NUMBER OF HOURS</td>
        <td class="label-cell" style="width:14%;">INCLUSIVE DATES (From)</td>
        <td class="label-cell" style="width:14%;">(To)</td>
        <td class="label-cell" style="width:16%;">CONDUCTED/ SPONSORED BY</td>
    </tr>
    @forelse($ldRows as $ld)
    <tr>
        <td class="value-cell">{{ $val($ld->title_of_ld) }}</td>
        <td class="value-cell">{{ $ld->type_of_ld ? $val(ucfirst($ld->type_of_ld)) . ($ld->type_of_ld_specify ? ' (' . $ld->type_of_ld_specify . ')' : '') : $na }}</td>
        <td class="value-cell">{{ $ld->number_of_hours !== null && $ld->number_of_hours !== '' ? $ld->number_of_hours : $na }}</td>
        <td class="value-cell">{{ $ld->inclusive_dates_from ? $dateVal($ld->inclusive_dates_from) : $na }}</td>
        <td class="value-cell">{{ $ld->inclusive_dates_to ? $dateVal($ld->inclusive_dates_to) : $na }}</td>
        <td class="value-cell">{{ $val($ld->organization_name_address) }}</td>
    </tr>
    @empty
    <tr>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
        <td class="value-cell">{{ $na }}</td>
    </tr>
    @endforelse
</table>
<p style="font-size:6px;margin-top:0;">(Continue on separate sheet if necessary)</p>

@php
    $toList = function($raw) use ($na) {
        if ($raw === null || trim((string)$raw) === '') return [];
        $text = preg_replace('/\r\n|\r/', "\n", (string)$raw);
        $items = preg_split('/\n+|,\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_map('trim', array_filter($items));
    };
    $otherSkills = $toList($p?->special_skills_hobbies ?? '');
    $otherDistinctions = $toList($p?->non_academic_distinctions ?? '');
    $otherMemberships = $toList($p?->membership_in_associations ?? '');
@endphp
<table class="pds-table" style="margin-top:2px;">
    <tr>
        <td colspan="2" class="section-header">VIII. OTHER INFORMATION</td>
    </tr>
    <tr>
        <td class="label-cell" style="width:22%;">33. SPECIAL SKILLS AND HOBBIES</td>
        <td class="value-cell">
            @if(count($otherSkills) > 0)
                <ul class="pds-other-info-list">
                    @foreach($otherSkills as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                {{ $na }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="label-cell">34. NON-ACADEMIC DISTINCTIONS / RECOGNITION</td>
        <td class="value-cell">
            @if(count($otherDistinctions) > 0)
                <ul class="pds-other-info-list">
                    @foreach($otherDistinctions as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                {{ $na }}
            @endif
        </td>
    </tr>
    <tr>
        <td class="label-cell">35. MEMBERSHIP IN ASSOCIATION/ORGANIZATION</td>
        <td class="value-cell">
            @if(count($otherMemberships) > 0)
                <ul class="pds-other-info-list">
                    @foreach($otherMemberships as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            @else
                {{ $na }}
            @endif
        </td>
    </tr>
</table>

<div class="footer-block" style="margin-top:2px;">
    <div class="footer-sig">
        <table class="footer-sig-cols">
            <tr>
                <td>
                    <div class="footer-sig-label">SIGNATURE</div>
                    <div class="footer-sig-value"><span class="sig-line"></span></div>
                </td>
                <td>
                    <div class="footer-sig-label">DATE</div>
                    <div class="footer-sig-value"><span class="date-line"></span></div>
                </td>
            </tr>
        </table>
    </div>
</div>
