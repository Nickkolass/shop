@php use App\Models\User; @endphp
@extends('admin.layouts.main')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Пользователи</h1>
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

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('users.create') }}" class="btn btn-primary">Добавить</a>
                        </div>

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Статус</th>
                                    <th>Email</th>
                                    <th>Фамилия</th>
                                    <th>Имя</th>
                                    <th>Отчество</th>
                                    <th>Возраст</th>
                                    <th>Пол</th>
                                    <th>Почтовый индекс</th>
                                    <th>Адрес</th>
                                    <th>ИНН</th>
                                    <th>Юр. адрес</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->getRoleTitleAttribute() }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->surname }}</td>
                                        <td><a href="{{ route('users.show', $user->id) }}">{{ $user->name }}</a></td>
                                        <td>{{ $user->patronymic }}</td>
                                        <td>{{ $user->age }}</td>
                                        <td>{{ $user->getGenderTitleAttribute() }}</td>
                                        <td>{{ $user->postcode }}</td>
                                        <td>{{ $user->address }}</td>
                                        <td>{{ $user->INN }}</td>
                                        <td>{{ $user->registredOffice }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
        {{ $users->links('vendor.pagination.simple-bootstrap-4') }}
    </section>

    <!-- /.content -->
@endsection
