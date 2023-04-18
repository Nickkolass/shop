
@isset($orders)
@php  
$data['products'] = $orders;
@endphp
@endisset

@if($data['products']['next_page_url'] !== $data['products']['prev_page_url'])
<div>
  <nav aria-label="Page navigation example">
    <ul class="pagination justify-content-center">
      <li class="page-item @disabled($data['products']['current_page'] == 1)">
        <a class="page-link" href="{{ $data['products']['first_page_url'] }}" aria-label="First">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">В начало</span>
        </a>
      </li>
      <li class="page-item @disabled(empty($data['products']['prev_page_url']))">
        <a class="page-link" href="{{ $data['products']['prev_page_url'] }}" tabindex="-1">Назад</a>
      </li>
      <li class="page-item active disabled">
        <a class="page-link">{{$data['products']['current_page']}}</a>
      </li>
      <li class="page-item @disabled(empty($data['products']['next_page_url']))">
        <a class="page-link" href="{{ $data['products']['next_page_url'] }}">Вперед</a>
      </li>
    </ul>
  </nav>
</div>
@endif
