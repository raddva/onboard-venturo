<table>
    <thead>
        <tr>
            <td style="width: 170px; vertical-align: middle;" id="no">No</td>
            <td style="width: 170px; vertical-align: middle;" id="customer">Customer</td>
            @foreach ($dates as $date)
                <td>
                    {{ date('d', strtotime($date)) }}
                </td>
            @endforeach
            <td style="width: 70px; vertical-align: middle;" id="total">Total</td>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        @foreach ($data as $customer)
            <tr class="customer">
                <td>
                    {{ $i++ }}
                </td>
                <td>
                    {{ $customer['customer_name'] }}
                </td>
                @foreach ($dates as $date)
                    <?php
                    $transaction = collect($customer['transactions'])->firstWhere('date_transaction', $date);
                    ?>
                    <td class="nominal">
                        @if ($transaction)
                            Rp {{ number_format($transaction['total_sales']) }}
                        @else
                            Rp 0
                        @endif
                    </td>
                @endforeach
                <td class="nominal">
                    Rp {{ number_format($customer['customer_total']) }}
                </td>
            </tr>
        @endforeach
        <tr class="customer">
            <td>Grand Total</td>
            <td></td>
            @foreach ($total_per_date as $total)
                <td class="nominal">
                    Rp {{ number_format($total) }}
                </td>
            @endforeach
            <td class="nominal">
                Rp {{ number_format($grand_total) }}
            </td>
        </tr>
    </tbody>
</table>
