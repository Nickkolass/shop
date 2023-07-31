<div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
        <thead>
        <tr style="text-align:center">
            <th style="text-align:center">ID товара</th>
            <th style="text-align:center">Количество</th>
            <th style="text-align:center">Цена</th>
        </tr>
        </thead>
        <tbody>
        @foreach(json_decode($order['productTypes'], true) as $product)
            <tr style="text-align:center">
                <td style="text-align:center">{{ $product['productType_id'] }}</td>
                <td style="text-align:center">{{ $product['amount'] }}</td>
                <td style="text-align:center">{{ $product['price'] }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
