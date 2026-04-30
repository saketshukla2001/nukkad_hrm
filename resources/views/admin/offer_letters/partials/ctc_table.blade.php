<table class="ctc-table">
    <thead>
        <tr>
            <th style="text-align:left;">CTC Components</th>
            <th style="width:120px;">Monthly</th>
            <th style="width:120px;">Annual</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left">Basic Pay</td>
            <td>{{ $ctc['basic'] ?? '0' }}</td>
            <td>{{ $ctc['basic_annual'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left">HRA</td>
            <td>{{ $ctc['hra'] ?? '0' }}</td>
            <td>{{ $ctc['hra_annual'] ?? '0' }}</td>
        </tr>
        <tr>
            <td class="text-left"><strong>Total</strong></td>
            <td><strong>{{ $ctc['total_monthly'] ?? '0' }}</strong></td>
            <td><strong>{{ $ctc['total_annual'] ?? '0' }}</strong></td>
        </tr>
    </tbody>
</table>
