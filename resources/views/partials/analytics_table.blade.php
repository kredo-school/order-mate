@php
    // include 時に range が渡されていない可能性があるのでデフォルトを設定
    $range = $range ?? 'daily';
@endphp
@foreach($stats as $row)
<tr class="summary-row" data-date="{{ $row->date ?? '' }}">
    <td>
        @if($range === 'daily')
            {{ $row->date ?? '-' }}
        @elseif($range === 'weekly')
            {{ $row->week_label ?? '-' }}
        @elseif($range === 'monthly')
            {{ $row->month_label ?? '-' }}
        @else
            -
        @endif
    </td>
    <td>
        @if($range === 'daily')
            {{ $row->day_of_week ?? '-' }}
        @else
            -
        @endif
    </td>
    <td>{{ number_format($row->sales ?? 0, 2) }}</td>
    <td>{{ $row->guests ?? 0 }}</td>
    <td>{{ number_format($row->avg_spend ?? 0, 2) }}</td>
    <td>{{ $row->payment_methods ?? '-' }}</td>
</tr>
<tr class="detail-row" data-date="{{ $row->date ?? '' }}" style="display:none;">
    <td colspan="6" class="p-0">
        <div class="order-details"></div>
    </td>
</tr>
@endforeach
