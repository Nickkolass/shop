@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Группы</h1>
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
            <a href="{{ route('group.create_group') }}" class="btn btn-primary">Добавить</a>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Наименование</th>
                  <th>Категория</th>
                  <th>Продукты</th>
                </tr>
              </thead>
              <tbody>
                @foreach($groups as $group)
                <tr>
                  <td>{{ $group->id }}</td>
                  <td><a href="{{ route('group.show_group', $group->id) }}">{{ $group->title }}</a></td>
                  <td>{{ $group->category->title_rus }}</td>
                  <td>@foreach($group->products as $product)
                    <a href="{{ route('product.show_product', $product->id) }}"><img src="{{ asset('/storage/'.$product->preview_image) }}" width='50' height='50' class="img img-responsive"></a>
                  @endforeach</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

        </div>
        {{ $groups->links('vendor.pagination.bootstrap-4') }}
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection