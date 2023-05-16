@extends(
session('user_role') == 'client'
? 'api.layouts.main'
: 'admin.layouts.main'
)
@section('content')
<!-- Content Header (Page header) -->
@if(session('user_role') == 'client')
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
          <a href="{{ route('admin.index_admin') }}">Главная</a>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
@endif


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
      <form action="{{ route('user.update_user', $user->id) }}" method="post">
        @csrf
        @method('patch')
        <div>
          <div class="row mb-3">
            <label for="surname" class="col-md-6 col-form-label text-md-end">{{ __('Фамилия') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->surname }}" name="surname" class="form-control" placeholder="Фамилия">
            </div>
          </div>
          <div class="row mb-3">
            <label for="name" class="col-md-6 col-form-label text-md-end">{{ __('Имя') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->name }}" name="name" class="form-control" placeholder="Имя">
            </div>
          </div>
          <div class="row mb-3">
            <label for="patronymic" class="col-md-6 col-form-label text-md-end">{{ __('отчество') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->patronymic }}" name="patronymic" class="form-control" placeholder="Отчество">
            </div>
          </div>
          <div class="row mb-3">
            <label for="age" class="col-md-6 col-form-label text-md-end">{{ __('Возраст') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->age }}" name="age" class="form-control" placeholder="Возраст">
            </div>
          </div>
          <div class="row mb-3">
            <label for="postcode" class="col-md-6 col-form-label text-md-end">{{ __('Почтовый индекс') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->postcode }}" name="postcode" class="form-control" placeholder="Почтовый индекс">
            </div>
          </div>
          <div class="row mb-3">
            <label for="address" class="col-md-6 col-form-label text-md-end">{{ __('Адрес') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->address }}" name="address" class="form-control" placeholder="Адрес">
            </div>
          </div>
          @auth
          @if((session('user_role') == 'admin') || (session('user_role') == 'saler'))
          <div class="row mb-3">
            <label for="INN" class="col-md-6 col-form-label text-md-end">{{ __('ИНН') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->INN }}" name="INN" class="form-control" placeholder="ИНН">
            </div>
          </div>
          <div class="row mb-3">
            <label for="registredOffice" class="col-md-6 col-form-label text-md-end">{{ __('Юр. Адрес') }}</label>
            <div class="col-md-6">
              <input type="text" value="{{ $user->registredOffice }}" name="registredOffice" class="form-control" placeholder="Юр. Адрес">
            </div>
          </div>
          @endif
          @endauth
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