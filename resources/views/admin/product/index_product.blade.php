@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Продукты</h1>
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
            <a href="{{ route('product.create_product') }}" class="btn btn-primary">Добавить продукт</a>
            <a href="{{ route('group.create_group') }}" class="btn btn-primary">Создать группу продуктов</a>
            <a href="{{ route('group.index_group') }}" class="btn btn-primary">Добавить продукты в группу</a>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Продукты</th>
                  <th>Группа</th>
                  <th>Категория</th>
                  @if (auth()->user()->role == 'admin')
                  <th>Продавец</th>
                  @endif
                  <th>Виды</th>
                </tr>
              </thead>
              <tbody>
                @foreach($products as $product)
                @if(isset($product->group_id))
                <tr>
                  <td>{{ $product->id }}</td>
                  <td><a href="{{ route('product.show_product', $product->id) }}">{{ $product->title }}</a></td>
                  <td><a href="{{ route('group.show_group', $product->group->id) }}">{{ $product->group->title }}</a></td>
                  <td>{{ $product->category->title_rus }}</td>
                  @if (auth()->user()->role == 'admin')
                  <td><a href="{{ route('user.show_user', $product->saler->id) }}">{{ $product->saler->name }}</a></td>
                  @endif
                  <td>@foreach($product->group->products as $prod)
                    <a href="{{ route('product.show_product', $prod->id) }}"><img src="{{ asset('/storage/'.$prod->preview_image) }}" width='50' height='50' class="img img-responsive"></a>
                    @endforeach
                  </td>
                </tr>
                @else
                <tr>
                  <td>{{ $product->id }}</td>
                  <td><a href="{{ route('product.show_product', $product->id) }}">{{ $product->title }}</a></td>
                  <td>{{ __('Нет') }}</td>
                  <td>{{ $product->category->title_rus }}</td>
                  @if (auth()->user()->role == 'admin')
                  <td><a href="{{ route('user.show_user', $product->saler->id) }}">{{ $product->saler->name }}</a></td>
                  @endif
                  <td><a href="{{ route('product.show_product', $product->id) }}"><img src="{{ asset('/storage/'.$product->preview_image) }}" width='50' height='50' class="img img-responsive"></a></td>
                </tr>
                @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
        {{ $products->links('vendor.pagination.simple-bootstrap-4') }}
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- /.content -->
@endsection