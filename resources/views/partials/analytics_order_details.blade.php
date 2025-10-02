<table class="table table-bordered table-hover mb-0 shadow-lg">
    <thead class="table-light">
        <tr>
            <th>{{__('manager.date_time')}}</th>
            <th>{{__('manager.stay_duration')}}</th>
            <th>{{__('manager.sales')}}</th>
            <th>{{__('manager.guests')}}</th>
            <th>{{__('manager.ave_spend')}}</th>
            <th>{{__('manager.order_type')}}</th>
            <th>{{__('manager.payment_method')}}</th>
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
            <td>{{ ucfirst($order->order_type) }}</td>
            <td>{{ $order->payment_method }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
