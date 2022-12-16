@extends('layouts.main_layout')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Добавить группу</h1>
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
      <form action="{{ route('group.store_group') }}" method="post">
        @csrf

        <div class="form-group"> 
          <input type="text" name="title" class="form-control" placeholder="Наименование">
        </div>

        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Добавить">
        </div>

      </form>

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection