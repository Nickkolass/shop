@php use Illuminate\Support\Facades\Storage; @endphp
@extends('admin.layouts.main')
@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Редактировать тип товара</h1>
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
            <form action="{{ route('admin.productTypes.update', $productType->id) }}" method="post"
                  enctype="multipart/form-data">
                @csrf
                @method('patch')
                <div class="row align-items-start">
                    <div class="col">

                        @include('admin.product.errors')

                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <tbody>
                                <tr>
                                    <td>Классификаторы</td>
                                    <td>
                                        @foreach($optionValues as $option => $values)
                                            <select name="relations[optionValues][]" class="tags" style="width:200px">
                                                <option value=0 selected>{{ $option }}</option>
                                                @foreach($values as $value)
                                                    <option value="{{ $value['id'] }}"
                                                        @selected(
                                                            session()->has('_old_input')
                                                            ? in_array($value['id'], old('relations.optionValues'))
                                                            : $productType->optionValues->contains($value['id'])
                                                        )>{{ $value['value'] }}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Цена</td>
                                    <td><input type="number" name="price" class="form-control"
                                               value="{{ old('price') ?? $productType->price }}" required></td>
                                </tr>
                                <tr>
                                    <td>Остаток</td>
                                    <td><input type="number" name="count" class="form-control"
                                               value="{{ old('count') ?? $productType->count }}" required></td>
                                </tr>
                                <tr>
                                    <td>Опубликовать</td>
                                    <td><input style="width:50px;" type="checkbox" name="is_published" value='1'
                                               class="form-control" @checked(session()->has('_old_input') ? old('is_published') : $productType->is_published)>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Заставка</td>
                                    <td>
                                        <div class="card">
                                            <div class="card-body card-block">
                                                <div class="row form-group">
                                                    <div class="control-group" id="exampleInputFile">
                                                        <div class="controls">
                                                            <div class="entry input-group upload-input-group">
                                                                <input name="preview_image" type="file"
                                                                       class="form-control">
                                                                <img
                                                                    src="{{ Storage::url($productType->preview_image) }}"
                                                                    width='70' height='70' class="img img-responsive"
                                                                    style="margin-left: 10px">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Изображения</td>
                                    <td>
                                        <div class="card">
                                            <div class="card-body card-block">
                                                <div class="row form-group">
                                                    <div class="control-group" id="fields">
                                                        <div class="controls">
                                                            <div class="entry input-group upload-input-group">
                                                                <input class="form-control"
                                                                       name="relations[productImages][]"
                                                                       type="file" multiple>
                                                                @foreach($productType->productImages as $img)
                                                                    <img
                                                                        src="{{Storage::url( $img->file_path) }}"
                                                                        width='70' height='70'
                                                                        class="img img-responsive"
                                                                        style="margin-left: 10px">
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                        </div>
                    </div><!-- /.container-fluid -->
                </div>
                <div class="col" style="padding: 5px;">
                    <input type="submit" class="btn-btn-primary" value="Редактировать">
                </div>
            </form>
            <form action="{{route('admin.productTypes.destroy', $productType->id) }}" method="post">
                @csrf
                @method('delete')
                <input type="submit" class="btn btn-danger" value="Удалить">
            </form>
        </div>
    </section>
    <!-- /.content -->

@endsection
