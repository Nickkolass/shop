@php use App\Models\User; @endphp
@extends(
    \Illuminate\Support\Facades\Gate::check('role', [User::class, User::ROLE_SALER])
    ? 'admin.layouts.main'
    : 'client.layouts.main'
)

@section('content')
    @cannot('role', [User::class, User::ROLE_SALER])
        <br><br><br><br><br><br>
    @endcannot
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Привязка карты</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <section class="content" style="margin-left:20px">
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

            {!! $widget !!}

        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
