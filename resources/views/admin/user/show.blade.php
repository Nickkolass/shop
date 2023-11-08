@php use App\Models\User; @endphp
@extends(
    \Illuminate\Support\Facades\Gate::check('role', [User::class, User::ROLE_SALER])
    ? 'admin.layouts.main'
    : 'client.layouts.main'
)
@section('content')
    <!-- Content Header (Page header) -->
    @cannot('role', [User::class, User::ROLE_SALER])
        <br><br><br><br><br><br>
    @else
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Пользователь</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <a href="{{ route('admin.index') }}">Главная</a>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
    @endcannot

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">

                                <tbody>
                                <tr>
                                    <td>ID</td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                @can('role', [User::class, User::ROLE_ADMIN])
                                    <tr>
                                        <td>Статус</td>
                                        <td>{{ $user->getRoleTitleAttribute() }}</td>
                                    </tr>
                                @endcan
                                <tr>
                                    <td>Email</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td>Фамилия</td>
                                    <td>{{ $user->surname }}</td>
                                </tr>
                                <tr>
                                    <td>Имя</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Отчество</td>
                                    <td>{{ $user->patronymic }}</td>
                                </tr>
                                <tr>
                                    <td>Возраст</td>
                                    <td>{{ $user->age }}</td>
                                </tr>
                                <tr>
                                    <td>Пол</td>
                                    <td>{{ $user->getGenderTitleAttribute() }}</td>
                                </tr>
                                @can('role', [User::class, User::ROLE_SALER])
                                    <tr>
                                        <td>ИНН</td>
                                        <td>{{ $user->INN }}</td>
                                    </tr>
                                    <tr>
                                        <td>Юр. Адрес</td>
                                        <td>{{ $user->registredOffice }}</td>
                                    </tr>
                                    <tr>
                                        <td>Карта для выплат</td>
                                        <td>{{ $user->card['first6'] . '******' . $user->card['last4'] }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>Почтовый индекс</td>
                                        <td>{{ $user->postcode }}</td>
                                    </tr>
                                    <tr>
                                        <td>Адрес</td>
                                        <td>{{ $user->address }}</td>
                                    </tr>
                                @endcan
                            </table>
                        </div>
                    </div>
                    <div class="card-header d-flex p-3">
                        <div class="mr-3">
                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">Редактировать</a>
                        </div>
                        <div class="mr-3">
                            <a href="{{ route('users.password.edit', $user->id) }}" class="btn btn-primary">Сменить
                                пароль</a>
                        </div>
                        <div class="mr-3">
                            <a href="{{ route('users.card.edit', $user->id) }}" class="btn btn-primary">Привязать
                                карту</a>
                        </div>
                        <div class="mr-3">
                            <form action="{{route('users.destroy', $user->id) }}" method="post">
                                @csrf
                                @method('delete')
                                <input type="submit" class="btn btn-danger" value="Удалить">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
@endsection
