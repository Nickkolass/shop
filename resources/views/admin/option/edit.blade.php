@extends('admin.layouts.main')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-3">
                    <h1 class="m-0">Редактировать классификатор</h1>
                </div><!-- /.col -->
                <div class="col-sm-3">
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
                <div class="col-sm-3">
                    <form action="{{ route('admin.options.update', $option->id) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="form-group">
                            <input type="submit" class="btn-primary" value="Редактировать">
                        </div>

                        <div class="form-group">
                            <input type="text" name="title" , value="{{ $option->title }}" class="form-control"
                                   placeholder="title">
                        </div>

                        <div class="form-group">
                            <h5>Значения</h5>
                            <div id="multi"
                                 data-old="{{json_encode(old('optionValues', $option->optionValues->pluck('value')->toArray()))}}">
                                <div class="js-row input-group">
                                    <input type="text" name="optionValues[0]" data-name="optionValues"
                                           class="form-control">
                                    <button type="button" id="load_old_types" class="js-add btn btn-outline-primary">+
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>

                </div>
            </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->

    <script src="{{asset('assets/js/optionRow.js')}}"></script>
@endsection
