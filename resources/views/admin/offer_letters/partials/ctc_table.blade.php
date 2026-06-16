<table class="ctc-table" style="margin-bottom:16px;">
    <thead>
        <tr>
            <th style="text-align:left;">Salary Component</th>
            <th style="width:120px;">Monthly</th>
            <th style="width:120px;">Annual</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left">Basic Pay (50% of CTC)</td>
            <td>{{ $ctc['basic'] ?? '0' }}</td>
            <td>{{ $ctc['basic_annual'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left">HRA (40% of Basic)</td>
            <td>{{ $ctc['hra'] ?? '0' }}</td>
            <td>{{ $ctc['hra_annual'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left">Special Allowance</td>
            <td>{{ $ctc['special_allowance'] ?? '0' }}</td>
            <td>{{ $ctc['special_allowance_annual'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left"><strong>Gross Salary</strong></td>
            <td><strong>{{ $ctc['gross'] ?? '0' }}</strong></td>
            <td><strong>{{ $ctc['gross_annual'] ?? '0' }}</strong></td>
        </tr>
    </tbody>
</table>

<table class="ctc-table">
    <thead>
        <tr>
            <th style="text-align:left;">Component</th>
            <th style="width:140px;">Employee Contribution</th>
            <th style="width:140px;">Employer Contribution</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left">EPF + NPS (12% of Basic)</td>
            <td>{{ $ctc['epf_employee'] ?? '0' }}</td>
            <td>{{ $ctc['epf_employer'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left">ESIC (0.75% / 3.25% of Gross)</td>
            <td>{{ $ctc['esic_employee'] ?? '0' }}</td>
            <td>{{ $ctc['esic_employer'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left">Gratuity (After 5 yr, 4.81% of Basic)</td>
            <td>{{ $ctc['gratuity_employee'] ?? '0' }}</td>
            <td>{{ $ctc['gratuity_employer'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left"><strong>Total CTC</strong></td>
            <td colspan="2"><strong>{{ $ctc['ctc_monthly'] ?? '0' }} (Monthly) / {{ $ctc['ctc_annual'] ?? '0' }} (Annual)</strong></td>
        </tr>
        <tr>
            <td class="text-left"><strong>In-hand Salary (After Deductions)</strong></td>
            <td colspan="2"><strong>{{ $ctc['in_hand_monthly'] ?? '0' }} (Monthly) / {{ $ctc['in_hand_annual'] ?? '0' }} (Annual)</strong></td>
        </tr>
    </tbody>
</table>
