@extends('layouts.main_layout')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Продукт</h1>
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

      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex p-3">
            <div class="mr-3">
              <a href="{{ route('product.edit_product', $product->id) }}" class="btn btn-primary">Редактировать</a>

            </div>
            <form action="{{route('product.delete_product', $product->id) }}" method="post">
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
                  <td>{{ $product->id }}</td>
                </tr>

                <tr>
                  <td>Наименование</td>
                  <td>{{ $product->title }}</td>
                </tr>

                <tr>
                  <td>Описание</td>
                  <td>{{ $product->descriprion }}</td>
                </tr>

                <tr>
                  <td>Контент</td>
                  <td>{{ $product->content }}</td>
                </tr>

                <tr>
                  <td>Заставка</td>
                  <td>
                    <img src="{{ asset('/storage/'.$product->preview_image) }}" width='50' height='50' class="img img-responsive">
                  </td>
                </tr>

                <tr>
                  <td>Цена</td>
                  <td>{{ $product->price }}</td>
                </tr>

                <tr>
                  <td>Остаток</td>
                  <td>{{ $product->count }}</td>
                </tr>

                <tr>
                  <td>Категория</td>
                  <td>{{ $category }}</td>
                </tr>

                <tr>
                  <td>Группа</td>
                  <td>{{ $group }}</td>
                </tr>

                <tr>
                  <td>Теги</td>
                  <td>@foreach ($tags as $tag) {{ $tag->title }} <br> @endforeach</td>
                </tr>


                <tr>
                  <td>Цвета</td>
                  <td>@foreach ($colors as $color){{ $color->title }} <br> @endforeach</td>
                </tr>

                <tr>
                  <td>Изображения</td>
                  <td>
                    @foreach($images as $image)
                    <img src="{{ asset('/storage/'.$image->file_path) }}" width='50' height='50' class="img img-responsive">
                    @endforeach
                  </td>
                </tr>

            </table>
          </div>

        </div>

      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>
@endsection