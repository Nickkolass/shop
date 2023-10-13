@extends('admin.layouts.main')
@section('content')

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Добавить продукт</h1>
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
            <form action="{{ route('admin.products.create.relations') }}" method="get" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-start">
                    <div class="col">

                        <!-- Small boxes (Stat box) -->
                        @include('admin.product.errors')

                        <div class="row" style="margin:5px">
                            <div class="col" style="padding: 5px;">
                                <label>Наименование</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                       required>
                            </div>
                            <div class="w-100"></div>

                            <div class="col" style="padding: 5px;">
                                <label>Описание</label>
                                <textarea name="description" class="form-control" rows="5"
                                          required>{{ old('description') }}</textarea>
                            </div>
                            <div class="w-100"></div>

                            <div class="col" style="padding: 5px;">
                                <label>Категория</label>
                                <select name="category_id" id="category_id" class="categories" style="width: 100%;"
                                        required>
                                    <option selected disabled>Категория</option>
                                    @foreach($data['categories'] as $category_id => $category_title_rus)
                                        <option
                                            value="{{ $category_id }}" @selected(old('category_id')==$category_id)>{{ $category_title_rus }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-100"></div>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
                <input type="hidden" name="saler_id" value="{{session('user.id')}}">
                <div class="col" style="padding: 5px;">
                    <input type="submit" class="btn-primary" value="Продолжить">
                </div>
            </form>
        </div>
    </section>
    <!-- /.content -->

@endsection
