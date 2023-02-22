@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Категории</h1>
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
    <div class="row">

      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <a href="{{ route('category.create_category') }}" class="btn btn-primary">Добавить</a>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>title</th>
                  <th>Наименование</th>
                </tr>
              </thead>
              <tbody>
                @foreach($categories as $category)
                <tr>
                  <td>{{ $category->id }}</td>
                  <td><a href="{{ route('category.show_category', $category->id) }}">{{ $category->title }}</a></td>
                  <td>{{ $category->title_rus }}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>

      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection