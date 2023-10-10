@extends(
in_array(session('user.role'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_SALER])
? 'admin.layouts.main'
: 'client.layouts.main'
)
@section('content')
    <!-- Content Header (Page header) -->
    @if(in_array(session('user.role'), [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_SALER]))
        <br><br><br><br><br><br>
    @endif
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Смена пароля</h1>
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
            <div class="row">
                <form action="{{ route('users.password.update', session('user.id')) }}"
                      method="post">
                    @csrf
                    @method('patch')
                    <div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control"
                                   placeholder="Введите старый пароль" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_password" class="form-control"
                                   placeholder="Введите новый пароль" required>
                        </div>
                        <div class="form-group">
                            <input type="password" name="new_password_confirmation"
                                   class="form-control" placeholder="Повторите новый пароль" required>
                        </div>
                        <div class="col-md-6">
                            <input type="submit" class="btn-btn-primary" value="Редактировать">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
@endsection
