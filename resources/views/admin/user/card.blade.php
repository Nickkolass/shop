@php use App\Components\Payment\AbstractPaymentClient;use App\Models\User; @endphp
@extends(
    \Illuminate\Support\Facades\Gate::check('role', [User::class, User::ROLE_SALER])
    ? 'admin.layouts.main'
    : 'client.layouts.main'
)

@section('content')
    @cannot('role', [User::class, User::ROLE_SALER])
        <br><br><br><br><br><br>
    @endcannot
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Привязка карты</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content" style="margin-left:20px">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            @if ($errors->any())
                <div class="row">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div><!-- /.container-fluid -->
            @endif

            <div id="payout-form"></div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
    <script src="https://yookassa.ru/payouts-data/3.0.0/widget.js"></script>
    <script type="text/javascript">
        //Инициализация виджета. Все параметры обязательные.
        const payoutsData = new window.PayoutsData({
            type: 'payout',
            account_id: {!! json_encode(config('payment.connections.' . AbstractPaymentClient::getConnection() . '.agent.login')) !!}, //Идентификатор шлюза (agentId в личном кабинете)
            success_callback(data) {
                //Обработка ответа с токеном карты
                var form = document.createElement('form');
                form.method = 'post';
                form.action = {!! json_encode(route('users.card.update', $user->id)) !!};
                document.body.appendChild(form);
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_token';
                input.value = {!! json_encode(csrf_token()) !!};
                form.appendChild(input);
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'patch';
                form.appendChild(input);
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'data';
                input.value = JSON.stringify(data);
                form.appendChild(input);
                form.submit();
            },
            error_callback(error) {
                //Обработка ошибок инициализации
            }
        });

        //Отображение формы в контейнере
        payoutsData.render('payout-form')
            //Метод возвращает Promise, исполнение которого говорит о полной загрузке формы сбора данных (можно не использовать).
            .then(() => {
                //Код, который нужно выполнить после отображения формы.
            });

    </script>
@endsection
