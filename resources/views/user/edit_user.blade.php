@extends('layouts.main_layout')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Редактировать пользователя</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('main.index_main') }}">Главная</a>
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
      <form action="{{ route('user.update_user', $user->id) }}" method="post">
        @csrf
        @method('patch')
        <div>
          <div class="row mb-3">
            <label for="surname" class="col-md-4 col-form-label text-md-end">{{ __('Фамилия') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->surname }}" name="surname" class="form-control" placeholder="Фамилия">
            </div>
          </div>
          <div class="row mb-3">
            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Имя') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->name }}" name="name" class="form-control" placeholder="Имя">
            </div>
          </div>
          <div class="row mb-3">
            <label for="patronymic" class="col-md-4 col-form-label text-md-end">{{ __('отчество') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->patronymic }}" name="patronymic" class="form-control" placeholder="Отчество">
            </div>
          </div>
          <div class="row mb-3">
            <label for="age" class="col-md-4 col-form-label text-md-end">{{ __('Возраст') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->age }}" name="age" class="form-control" placeholder="Возраст">
            </div>
          </div>
          <div class="row mb-3">
            <label for="address" class="col-md-4 col-form-label text-md-end">{{ __('Адрес') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->address }}" name="address" class="form-control" placeholder="Адрес">
            </div>
          </div>
          <div class="col-md-6">
            <input type="submit" class="btn-btn-primary" value="Редактировать">
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection