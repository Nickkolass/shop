@php if(isset($orders)) $product_types = $orders; @endphp

@if($product_types['next_page_url'] !== $product_types['prev_page_url'])
    <div>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <li class="page-item @disabled($product_types['current_page'] == 1)">
                    <a class="page-link" href="{{ $product_types['first_page_url'] }}" aria-label="First">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">В начало</span>
                    </a>
                </li>
                <li class="page-item @disabled(empty($product_types['prev_page_url']))">
                    <a class="page-link" href="{{ $product_types['prev_page_url'] }}" tabindex="-1">Назад</a>
                </li>
                <li class="page-item active disabled">
                    <a class="page-link">{{$product_types['current_page']}}</a>
                </li>
                <li class="page-item @disabled(empty($product_types['next_page_url']))">
                    <a class="page-link" href="{{ $product_types['next_page_url'] }}">Вперед</a>
                </li>
            </ul>
        </nav>
    </div>
@endif
