<table class="table mb-0">
    <thead class="table-light">
        <tr>
            <th>Date (Created)</th>
            <th>Stay Duration</th>
            <th>Sales</th>
            <th>Guests</th>
            <th>Avg. Spend</th>
            <th>Payment Methods</th>
        </tr>
    </thead>
    <tbody id="order-table-body">
        @foreach($orders as $order)
        <tr class="cursor-pointer">
            <td>{{ $order->created_at->format('Y/m/d H:i') }}</td>
            <td>{{ $order->duration }}</td>
            <td>{{ number_format($order->total_price, 2) }}</td>
            <td>{{ $order->guest_count }}</td>
            <td>{{ number_format($order->avg_spend, 2) }}</td>
            <td>{{ $order->payment_method }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
