@extends('auth.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Регистрация') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Имя') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="surname"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Фамилия') }}</label>

                                <div class="col-md-6">
                                    <input id="surname" type="text"
                                           class="form-control @error('surname') is-invalid @enderror" name="surname"
                                           value="{{ old('surname') }}" required autocomplete="surname" autofocus>

                                    @error('surname')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="patronymic"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Отчество') }}</label>

                                <div class="col-md-6">
                                    <input id="patronymic" type="text"
                                           class="form-control @error('patronymic') is-invalid @enderror"
                                           name="patronymic" value="{{ old('patronymic') }}" required
                                           autocomplete="patronymic" autofocus>

                                    @error('patronymic')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="age" class="col-md-4 col-form-label text-md-end">{{ __('Возраст') }}</label>

                                <div class="col-md-6">
                                    <input id="age" type="text" class="form-control @error('age') is-invalid @enderror"
                                           name="age" value="{{ old('age') }}" required autocomplete="age" autofocus>

                                    @error('age')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="address"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Адрес') }}</label>

                                <div class="col-md-6">
                                    <input id="address" type="text"
                                           class="form-control @error('address') is-invalid @enderror" name="address"
                                           value="{{ old('address') }}" required autocomplete="address" autofocus>

                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="postcode"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Почтовый индекс') }}</label>

                                <div class="col-md-6">
                                    <input id="postcode" type="text"
                                           class="form-control @error('postcode') is-invalid @enderror" name="postcode"
                                           value="{{ old('postcode') }}" required autocomplete="postcode" autofocus>

                                    @error('postcode')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="gender" class="col-md-4 col-form-label text-md-end">{{ __('Пол') }}</label>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select name="gender" class="custom-select form-control"
                                                id="exampleSelectBorder">
                                            <option disabled selected></option>
                                            <option @selected(old('gender') == 1) value="1">Мужской</option>
                                            <option @selected(old('gender') == 2) value="2">Женский</option>
                                        </select>
                                    </div>
                                    @error('gender')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Пароль') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-end">{{ __('Повторите пароль') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="row mb-3" style="text-align:center">
                                <div class="col-md-4 col-form-label text-md-end">
                                    <input type="radio" style="width:20px" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="radio-label" for="radio">
                                        <a href="{{$policy}}" target="_blank">Согласен с условиями правил пользования
                                            торговой площадкой</a>
                                    </label>
                                </div>
                            </div>

                            <input type="hidden" name="role" value="3">

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Зарегистрироваться') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
