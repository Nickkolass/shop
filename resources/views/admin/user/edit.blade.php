@extends(
    \Illuminate\Support\Facades\Gate::check('role', [User::class, User::ROLE_SALER])
    ? 'admin.layouts.main'
    : 'client.layouts.main'
)

@section('content')
    <!-- Content Header (Page header) -->
    @can('role', [User::class, User::ROLE_CLIENT])
        <br><br><br><br><br><br>
    @else
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Редактировать пользователя</h1>
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
    @endcan


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
            <div class="row">
                <form action="{{ route('users.update', $user->id) }}" method="post">
                    @csrf
                    @method('patch')
                    <div>
                        <input type="hidden" value="{{ $user->id }}" name="id">
                        <div class="row mb-3">
                            <label for="surname" class="col-md-6 col-form-label text-md-end">{{ __('Фамилия') }}</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('surname', $user->surname) }}" name="surname"
                                       class="form-control"
                                       placeholder="Фамилия" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="name" class="col-md-6 col-form-label text-md-end">{{ __('Имя') }}</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('name', $user->name) }}" name="name"
                                       class="form-control"
                                       placeholder="Имя" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="patronymic"
                                   class="col-md-6 col-form-label text-md-end">{{ __('Отчество') }}</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('patronymic', $user->patronymic) }}"
                                       name="patronymic"
                                       class="form-control" placeholder="Отчество" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="age" class="col-md-6 col-form-label text-md-end">{{ __('Возраст') }}</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('age', $user->age) }}" name="age"
                                       class="form-control"
                                       placeholder="Возраст" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="email" class="col-md-6 col-form-label text-md-end">{{ __('Email') }}</label>
                            <div class="col-md-6">
                                <input type="text" value="{{ old('email', $user->email) }}" name="email"
                                       class="form-control"
                                       placeholder="Email" required>
                            </div>
                        </div>
                        @can('role', [User::class, User::ROLE_SALER])
                            <div class="row mb-3">
                                <label for="INN" class="col-md-6 col-form-label text-md-end">{{ __('ИНН') }}</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ old('INN', $user->INN) }}" name="INN"
                                           class="form-control"
                                           placeholder="ИНН" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="registredOffice"
                                       class="col-md-6 col-form-label text-md-end">{{ __('Юр. Адрес') }}</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ old('registredOffice', $user->registredOffice) }}"
                                           name="registredOffice"
                                           class="form-control" placeholder="Юр. Адрес" required>
                                </div>
                            </div>
                        @else
                            <div class="row mb-3">
                                <label for="postcode"
                                       class="col-md-6 col-form-label text-md-end">{{ __('Почтовый индекс') }}</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ old('postcode', $user->postcode) }}" name="postcode"
                                           class="form-control"
                                           placeholder="Почтовый индекс" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="address"
                                       class="col-md-6 col-form-label text-md-end">{{ __('Адрес') }}</label>
                                <div class="col-md-6">
                                    <input type="text" value="{{ old('address', $user->address) }}" name="address"
                                           class="form-control"
                                           placeholder="Адрес" required>
                                </div>
                            </div>
                        @endcan
                        <div class="col-md-6">
                            <input type="submit" class="btn-primary" value="Редактировать">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
