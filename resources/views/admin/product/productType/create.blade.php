@extends('admin.layouts.main')
@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Создать тип товара</h1>
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
            <form action="{{ route('admin.productTypes.store', $product->id) }}" method="post"
                  enctype="multipart/form-data">
                @csrf
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
                                                    <option
                                                        value="{{ $value['id'] }}" @selected(in_array($value['id'], old('optionValues') ?? [] ))>{{ $value['value'] }}</option>
                                                @endforeach
                                            </select>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td>Цена</td>
                                    <td><input type="number" name="price" class="form-control"
                                               value="{{ old('price') }}"></td>
                                </tr>
                                <tr>
                                    <td>Остаток</td>
                                    <td><input type="number" name="count" class="form-control"
                                               value="{{ old('count') }}"></td>
                                </tr>
                                <tr>
                                    <td>Опубликовать</td>
                                    <td><input style="width:50px;" type="checkbox" name="is_published" value='1'
                                               class="form-control" @checked(old('is_published'))></td>
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
                                                                <input class="form-control" name="relations[productImages][]"
                                                                       type="file" multiple>
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
                    <input type="submit" class="btn-btn-primary" value="Создать">
                </div>
            </form>
        </div>
    </section>
    <!-- /.content -->

@endsection
