@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-3">
        <h1 class="m-0">Добавить опцию</h1>
      </div><!-- /.col -->
      <div class="col-sm-3">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('admin.index_admin') }}">Главная</a>
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
    <div class="row">
    <div class="col-sm-3">

      <form action="{{ route('option.store_option') }}" method="post">
        @csrf

        <div class="form-group">
          <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Название">
        </div>
        
        <div class="form-group">
          <h5>Значения</h5>
          @for($i=1; $i<=10; $i++)
          <input type="text" name="optionValues[]" value="{{ old('optionValues') }}" class="form-control">
          @endfor
        </div>

        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Добавить">
        </div>

      </form>

    </div>
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection