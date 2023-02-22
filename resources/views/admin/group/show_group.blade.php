@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Группа</h1>
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
          <div class="card-header d-flex p-3">
            <div class="mr-3">
              <a href="{{ route('group.edit_group', $group->id) }}" class="btn btn-primary">Редактировать</a>

            </div>
            <form action="{{route('group.delete_group', $group->id) }}" method="post">
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
                  <td>{{ $group->id }}</td>
                </tr>

                <tr>
                  <td>Наименование</td>
                  <td>{{ $group->title }}</td>
                </tr>

                <tr>
                  <td>Категория</td>
                  <td>{{ $group->category->title_rus }}</td>
                </tr>

                <tr>
                  <td>Продукты</td>
                  @foreach($group->products as $product)
                  <td><a href="{{ route('product.show_product', $product->id) }}"><img src="{{ asset('/storage/'.$product->preview_image) }}" width='50' height='50' class="img img-responsive"></a></td>
                  @endforeach
                </tr>

            </table>
          </div>

        </div>

      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
@endsection