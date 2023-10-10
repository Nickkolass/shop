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
                        <div class="card-header">
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Добавить продукт</a>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Продукты</th>
                                    <th>Категория</th>
                                    @if (session('user.role') == \App\Models\User::ROLE_ADMIN)
                                        <th>Продавец</th>
                                    @endif
                                    <th>Виды</th>
                                    <th>Рейтинг</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.products.show', $product->id) }}">{{ $product->title }}</a>
                                        </td>
                                        <td>{{ $product->category->title_rus }}</td>
                                        @if (session('user.role') == \App\Models\User::ROLE_ADMIN)
                                            <td>
                                                <a href="{{ route('users.show', $product->saler_id) }}">{{$product->saler_id}}</a>
                                            </td>
                                        @endif
                                        <td>@foreach($product->productTypes as $productType)
                                                <img
                                                    src="{{Storage::url($productType->preview_image) }}"
                                                    width='50'
                                                    height='50'
                                                    class="img img-responsive">
                                            @endforeach
                                        </td>
                                        <td>
                                            @for($i=1; $i<=5; $i++)
                                                <i class="fa fa-star{{$i-1<$product['rating'] & $product['rating']<$i ? '-half' : ''}}{{$product['rating']<$i ? '-o' : ''}}"></i>
                                            @endfor
                                        ({{ $product['count_rating'] }})
                                        </td>
                                    </tr>
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
