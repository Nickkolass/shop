@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Категория</h1>
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
          <div class="card-header d-flex p-3">
            <div class="mr-3">
              <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">Редактировать</a>

            </div>
            <form action="{{route('admin.categories.destroy', $category->id) }}" method="post">
              @csrf
              @method('delete')
              <input type="submit" class="btn btn-danger" value="Удалить">
            </form>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">


              <tbody>
                <tr>
                  <td>ID</td>
                  <td>{{ $category->id }}</td>
                </tr>

                <tr>
                  <td>title</td>
                  <td>{{ $category->title }}</td>
                </tr>

                <tr>
                  <td>Наименование</td>
                  <td>{{ $category->title_rus }}</td>
                </tr>

            </table>
          </div>

        </div>

      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
@endsection