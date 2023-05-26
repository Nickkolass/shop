@extends('admin.layouts.main')
@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
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
    <form action="{{ route('admin.products.update', $product->id) }}" method="post" enctype="multipart/form-data">
      @method('patch')
      @csrf
      <div class="row" style="margin-left:10px">

        @include('admin.product.errors')

        <div class="col col-sm">
          <h3 class="m-2"><br>Характеристики<br><br></h3>
          @foreach($properties as $property)
          <div class="col" style="padding: 5px;">
            <label>{{$property->title}}</label>
            <input class="form-control" name="propertyValues[{{$property->id}}]"
              value="{{ $property->propertyValues->whereIn('id', $productPV_ids)->first()->value ?? '' }}"
              list="datalistOptions[{{$property->id}}]" id="exampleDataList" style="width:500px">
            <datalist id="datalistOptions[{{$property->id}}]">
              @foreach($property->propertyValues as $propertyValue)
              <option value={{$propertyValue->value}}>
                @endforeach
            </datalist>
          </div>
          @endforeach
        </div>

        <div class="col col-sm">
          <h3 class="m-2"><br>Классификаторы<br><br></h3>
          @foreach($optionValues as $option => $values)
          <div class="col" style="padding: 5px;">
            <label>{{$option}}</label><br>
            <select name="optionValues[{{$option}}][]" class="tags" multiple="multiple" style="width:500px">
              @foreach($values as $id => $value)
              <option value="{{ $id }}" @selected($productOV_ids->contains($id))>{{ $value }}</option>
              @endforeach
            </select>
          </div>
          @endforeach
        </div>

        <div class="w-100"></div>
        <div class="row" style="padding: 5px;">
          <input type="submit" class="btn-btn-primary" value="Редактировать">
        </div>
      </div>
    </form>
  </div>
</section>
<!-- /.content -->
@endsection