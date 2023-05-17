@extends('admin.layouts.main')
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
      @if(str_contains(url()->previous(), '/edit/properties') & $product->productTypes->count() != 0)
      <div class="card" style="background:red">
        <h4>После редактирования продукта, проверьте соответствие его разновидностей новым классификаторам. <br>
          Несоответствующие сняты с публикации.</h4>
      </div>
      @endif


      <div class="col-12">
        <div class="card">
          <div class="card-header d-flex p-3">
            <div class="mr-3">
              <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">Редактировать</a>
              @if($product->productTypes->count() != $product->optionValues->flatten()->count())
              <a href="{{ route('admin.productTypes.create', $product->id) }}" style="margin-left:12px" class="btn btn-primary">Добавить вид</a>
              @endif
            </div>
            <form action="{{route('admin.products.destroy', $product->id) }}" method="post">
              @csrf
              @method('delete')
              <input type="submit" class="btn btn-danger" value="Удалить">
            </form>
          </div>

          <div class="card-body table-responsive p-0">
            <table class="table-sm table-hover text-nowrap">
              <tbody>
                <tr>
                  <td>ID</td>
                  <td>{{ $product->id }}</td>
                </tr>

                @if (session('user_role') == 'admin')
                <tr>
                  <td>Продавец</td>
                  <td>{{ $product->saler_id }}</td>
                </tr>
                @endif

                <tr>
                  <td>Наименование</td>
                  <td>{{ $product->title }}</td>
                </tr>
                <tr>
                  <td>Описание</td>
                  <td>{{ $product->description }}</td>
                </tr>

                <tr>
                  <td>Категория</td>
                  <td>{{ $product->category->title_rus }}</td>
                </tr>

                <tr>
                  <td>Теги</td>
                  <td>@foreach ($product->tags as $tag) {{ $tag->title }} <br> @endforeach</td>
                </tr>

                <tr>
                  <td>Характеристики</td>
                  <td>
                    @foreach ($product->propertyValues as $property => $value)
                    {{$property . ': ' . $value}}<br>
                    @endforeach
                  </td>
                </tr>
                <tr>
                  <td>Классификаторы</td>
                  <td>
                    @foreach ($product->optionValues as $option => $values)
                    {{$option . ': '}}
                    @foreach ($values as $value)
                    {{$value->value . ', '}}
                    @endforeach
                    <br>
                    @endforeach
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <h4 style="text-align:center"><br>Разновидности<br></h4>

          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr style="text-align:center">
                  <th>ID</th>
                  <th>Классификаторы</th>
                  <th>Заставка</th>
                  <th>Изображения</th>
                  <th>Цена</th>
                  <th>Остаток</th>
                  <th>Публикация</th>
                  <th>Редакция</th>
                  <th>Удаление</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($product->productTypes as $productType)
                <tr style="text-align:center">
                  <td>{{$productType->id}}</td>
                  <td>
                    @foreach ($productType->optionValues as $option => $value)
                    {{$option . ': ' . $value}}<br>
                    @endforeach
                  </td>
                  <td><img src="{{ asset('/storage/'.$productType->preview_image) }}" width='50' height='50' class="img img-responsive"></td>
                  <td>
                    @foreach($productType->productImages as $img)
                    <img src="{{ asset('/storage/'.$img->file_path) }}" width='50' height='50' class="img img-responsive">
                    @endforeach
                  </td>
                  <td>{{ $productType->price }}</td>
                  <td>{{ $productType->count }}</td>
                  <td>
                    <form action="{{route('admin.productTypes.published', $productType->id) }}" method="post">
                      @csrf
                      @method('patch')
                      <input type="submit" class="btn btn-primary" value="{{ $productType->is_published == 0 ? 'Опубликовать' : 'Снять с публикации' }}" @disabled($productType->count <= 0)>
                    </form>
                  </td>
                  <td><a href="{{ route('admin.productTypes.edit', $productType) }}" class="btn btn-primary">Редактировать</a></td>
                  <td>
                    <form action="{{route('admin.productTypes.destroy', $productType->id) }}" method="post">
                      @csrf
                      @method('delete')
                      <input type="submit" class="btn btn-danger" value="Удалить">
                    </form>
                  </td>
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
@endsection