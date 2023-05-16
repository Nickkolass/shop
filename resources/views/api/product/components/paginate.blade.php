
@isset($orders)
@php  
$productTypes = $orders;
@endphp
@endisset

@if($productTypes['next_page_url'] !== $productTypes['prev_page_url'])
<div>
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <li class="page-item @disabled($productTypes['current_page'] == 1)">
        <a class="page-link" href="{{ $productTypes['first_page_url'] }}" aria-label="First">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">В начало</span>
        </a>
      </li>
      <li class="page-item @disabled(empty($productTypes['prev_page_url']))">
        <a class="page-link" href="{{ $productTypes['prev_page_url'] }}" tabindex="-1">Назад</a>
      </li>
      <li class="page-item active disabled">
        <a class="page-link">{{$productTypes['current_page']}}</a>
      </li>
      <li class="page-item @disabled(empty($productTypes['next_page_url']))">
        <a class="page-link" href="{{ $productTypes['next_page_url'] }}">Вперед</a>
      </li>
    </ul>
  </nav>
</div>
@endif
