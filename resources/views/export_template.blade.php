<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Claims Date</th>
        <th>Customer Id</th>
        <th>Customer Name</th>
        <th>Customer Class</th>
        <th>Total Covered</th>
        <th>Total Customer Payment</th>
        <th>Product Code</th>
        <th>Product Detail</th>
        <th>Product Price</th>
        <th>Product Qty</th>
        <th>Product Total Price</th>
        <th>Product Covered</th>
        <th>Product Customer Payment</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transactions as $a => $transaction)
        @foreach($transaction->transaction_detail as $b => $detail)
        <tr>
            @if(count($transaction->transaction_detail) > 1 && $b == 0)
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $a + 1 }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->created_at }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->customer->customer_no }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->customer->name }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->customer->product->product_name }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->total_covered }}</td>
            <td rowspan="{{ count($transaction->transaction_detail) }}">{{ $transaction->total_customer_pay }}</td>
            @elseif(count($transaction->transaction_detail) == 1)
            <td>{{ $a + 1 }}</td>
            <td>{{ $transaction->created_at }}</td>
            <td>{{ $transaction->customer->customer_no }}</td>
            <td>{{ $transaction->customer->name }}</td>
            <td>{{ $transaction->customer->product->product_name }}</td>
            <td>{{ $transaction->total_covered }}</td>
            <td>{{ $transaction->total_customer_pay }}</td>
            @endif

            <td>{{ $detail->product_detail->code }}</td>
            <td>{{ $detail->product_detail->item }}</td>
            <td>{{ $detail->payment_amount }}</td>
            <td>{{ $detail->quantity != null ? $detail->quantity : 1 }}</td>
            <td>{{ $detail->total_payment }}</td>
            <td>{{ $detail->covered }}</td>
            <td>{{ $detail->customer_pay }}</td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>