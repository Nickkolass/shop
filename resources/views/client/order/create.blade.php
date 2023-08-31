@extends('client.layouts.main')
@section('content')

    <div class="page-heading cart header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Оформление заказа</h4>
                        <h2>LUMOS</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-md">
        <div class="row justify-content-md-center">
            <div class="col-3">
                <form action="{{ route('client.orders.store') }}" method="post" style="text-align: center">
                    @csrf
                    <div class="card-body table-responsive">
                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td>Способ доставки</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="cd-filter-block">
                                        <ul class="cd-filter-content cd-filters list" required>
                                            <li>
                                                <input class="filter" type="radio" name="delivery" value="post" checked>
                                                <label class="radio-label" for="radio">Почтой России</label>
                                            </li>
                                            <li>
                                                <input class="filter" type="radio" name="delivery" value="point">
                                                <label class="radio-label" for="radio">Курьерской доставкой</label>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Способ оплаты {{$total_price }} руб.</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="cd-filter-block">
                                        <ul class="cd-filter-content cd-filters list" required>
                                            <li>
                                                <input class="filter" type="radio" name="payment" value="card" checked>
                                                <label class="radio-label" for="radio">Новой картой</label>
                                            </li>
                                            <li>
                                                <input class="filter" type="radio" name="payment" value="SBP">
                                                <label class="radio-label" for="radio">Через СБП</label>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="cd-filter-block">
                                        <ul class="cd-filter-content cd-filters list">
                                            <li>
                                                <input class="filter" type="radio" name="offer" value=true required>
                                                <label class="radio-label" for="radio">Согласен с условиями правил
                                                    пользования <br> торговой площадкой и правилами возврата</label>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <input type="number" name="total_price" value="{{ $total_price }}" hidden>
                    <input type="submit" class="btn btn-primary btn-lg" style="height: 50px; width: 200px"
                           value="Перейти к оплате">
                </form>
            </div>
        </div>
    </div>
@endsection
