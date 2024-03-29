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

            @include('admin.product.errors')


            <form action="{{ route('admin.products.create.types') }}" method="get" enctype="multipart/form-data">
                @csrf
                <div class="row" style="margin-left:10px">
                    <div class="col col-sm">
                        <h3 class="m-2">Теги<br></h3>
                        <select name="tags[]" class="tags" multiple="multiple" style="width: 82%;" required>
                            @foreach($data['tags'] as $tag_id =>$tag_title)
                                <option
                                    value="{{ $tag_id }}" @selected(in_array($tag_id, old('tags', [])))>{{ $tag_title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row" style="margin-left:10px">
                    <div class="col col-sm">
                        <h3 class="m-2"><br>Характеристики<br><br></h3>
                        @foreach($data['propertyValues'] as $property => $values)
                            <div class="col" style="padding: 5px;">
                                <label>{{$property}}</label>
                                <input class="form-control" name="propertyValues[{{$values->first()->property_id}}]"
                                       value="{{ old('propertyValues.' . $values->first()->property_id) }}"
                                       list="datalistOptions[{{$property}}]" id="exampleDataList"
                                       style="width:500px">
                                <datalist id="datalistOptions[{{$property}}]">
                                    @foreach($values as $propertyValue)
                                        <option value={{$propertyValue->value}}>
                                    @endforeach
                                </datalist>
                            </div>
                        @endforeach
                    </div>

                    <div class="col col-sm">
                        <h3 class="m-2"><br>Классификаторы<br><br></h3>
                        @foreach($data['optionValues'] as $option_title => $values)
                            <div class="col" style="padding: 5px;">
                                <label>{{$option_title}}</label><br>
                                <select name="optionValues[{{$option_title}}][]" class="tags" multiple="multiple"
                                        style="width:500px">
                                    @foreach($values as $value)
                                        <option
                                            value="{{ $value->id }}" @selected(in_array($value->id, old('optionValues.' . $option_title, [])))>{{ $value->value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>

                    <div class="w-100"></div>
                    <div class="row" style="padding: 5px;">
                        <input type="submit" class="btn-primary" value="Продолжить">
                    </div>
                </div>
            </form>
        </div>
    </section>
    <!-- /.content -->

@endsection
