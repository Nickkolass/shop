@extends('admin.layouts.main')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Добавить группу</h1>
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
    @if ($errors->any())
    <div class="row">
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div><!-- /.container-fluid -->
    @endif
    <div class="row">
      <form action="{{ route('group.store_group') }}" method="post">
        @csrf

        <div class="form-group"> 
          <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="Наименование">
        </div>

        <div class="form-group">
          <select name="category_id" data-placeholder="Выберите категорию" style="width: 100%;">
            @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->title_rus }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <select name="products[]" multiple="multiple" data-placeholder="Выберите продукты" style="width: 100%;">
            @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->title }}</option>
            @endforeach
          </select>
        </div>
          
        <div class="form-group">
          <input type="submit" class="btn-btn-primary" value="Добавить">
        </div>
        

      </form>

    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection