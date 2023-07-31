<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    Часть Вашего заказа в Lumos от {{$order['created_at']}} на сумму {{$order['total_price']}} не сможет
                    быть доставлена. <br>
                    Мы уже отправили денежные средства на банковскую карту, с которой произведена оплата.
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div style="text-align:center">Отмененные товары</div>

@include('mail.products')
