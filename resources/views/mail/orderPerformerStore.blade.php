<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    У вас новый заказ в Lumos от {{$order['created_at']}} на сумму {{$order['total_price']}}. <br>
                    Доставьте его заказчику до {{ $order['dispatch_time'] }} по следующим реквизитам: <br>
                    {{$order['delivery']}}
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div style="text-align:center">Состав заказа</div>

@include('mail.products')
