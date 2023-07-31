@extends('admin.layouts.main')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавить пользователя</h1>
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
            <div class="row" style="margin-left: 10px">
                <form action="{{ route('users.store') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <input type="text" value="{{ old('surname') }}" name="surname" class="form-control"
                               placeholder="Фамилия" required>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ old('name') }}" name="name" class="form-control" placeholder="Имя"
                               required>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ old('patronymic') }}" name="patronymic" class="form-control"
                               placeholder="Отчество" required>
                    </div>
                    <div class="form-group">
                        <input type="number" value="{{ old('age') }}" name="age" class="form-control"
                               placeholder="Возраст" required>
                    </div>
                    <div class="form-group">
                        <select name="gender" class="custom-select form-control" id="exampleSelectBorder" required>
                            <option disabled>Пол</option>
                            <option @selected (old('gender') == 1) value="1">Мужской</option>
                            <option @selected (old('gender') == 2) value="2">Женский</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ old('INN') }}" name="INN" class="form-control" placeholder="ИНН"
                               required>
                    </div>
                    <div class="form-group">
                        <input type="text" value="{{ old('registredOffice') }}" name="registredOffice"
                               class="form-control" placeholder="Юр. Адрес" required>
                    </div>
                    <div class="form-group">
                        <input type="email" value="{{ old('email') }}" name="email" class="form-control"
                               placeholder="email" required>
                    </div>
                    <input type="number" name="role" value="2" hidden>
                    <div class="form-group">
                        <input type="submit" class="btn-btn-primary" value="Добавить">
                    </div>
                </form>
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
