@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Редактировать категорию</h1>
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
      <form action="{{ route('category.update_category', $category->id) }}" method="post">
        @csrf
        @method('patch')
        <div class="form-group">
          <input type="text" name="title" , value="{{ $category->title }}" class="form-control" placeholder="title">
        </div>
        
        <div class="form-group">
          <input type="text" name="title_rus" , value="{{ $category->title_rus }}" class="form-control" placeholder="Наименование">
        </div>

        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Редактировать">
        </div>

      </form>

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection