<div class="cd-filter">
  <form action="{{ route('api.filter_api', $data['category']['title']) }}" method="post" enctype="multipart/form-data">
    @csrf
    
    <div class="cd-filter-block">
      <h4>Отображать на странице</h4>
      <ul class="cd-filter-content cd-filters list">
        <li>
          <input class="filter" type="radio" name="perPage" value=8 {{ $data['filter']['perPage'] == 8 ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По 8</label>
        </li>
        <li>
          <input class="filter" type="radio" name="perPage" value=16 {{ $data['filter']['perPage'] == 16 ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По 16</label>
        </li>
        <li>
          <input class="filter" type="radio" name="perPage" value=32 {{ $data['filter']['perPage'] == 32 ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По 32</label>
        </li>
      </ul> 
    </div>

    <div class="cd-filter-block">
      <h4>Наличие товара</h4>
      <ul class="cd-filter-content cd-filters list">
        <li>
          <input class="filter" type="radio" name="is_published" value=1 {{ $data['filter']['is_published'] == 1 ? 'checked' : '' }}>
          <label class="radio-label" for="radio">Только в наличии</label>
        </li>
        <li>
          <input class="filter" type="radio" name="is_published" value=0 {{ $data['filter']['is_published'] == 0 ? 'checked' : '' }}>
          <label class="radio-label" for="radio">Показать все</label>
        </li>
      </ul> 
    </div>

    <div class="cd-filter-block">
      <h4>Сортировка</h4>
      <ul class="cd-filter-content cd-filters list">
        <li>
          <input class="filter" type="radio" name="orderBy" value='latest' {{ $data['filter']['orderBy'] == 'latest' ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По дате публикации</label>
        </li>
        <li>
          <input class="filter" type="radio" name="orderBy" value='ASC' {{ $data['filter']['orderBy'] == 'ASC' ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По возрастанию цены</label>
        </li>
        <li>
          <input class="filter" type="radio" name="orderBy" value='DESC' {{ $data['filter']['orderBy'] == 'DESC' ? 'checked' : '' }}>
          <label class="radio-label" for="radio">По убыванию цены</label>
        </li>
      </ul> 
    </div>
    
    <div class="cd-filter-block">
      <h4>Цвета</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($data['colors'] as $color)
          <li>
            @if (!empty($data['filter']['colors']))
                <input class="filter" name="colors[]" type="checkbox" value="{{$color['id']}}" {{ in_array($color['id'], $data['filter']['colors']) ? 'checked' : '' }}>
            @else
              <input class="filter" name="colors[]" type="checkbox" value="{{$color['id']}}">
            @endif
            <label class="checkbox-label">{{$color['title']}}</label>
          </li>
        @endforeach
      </ul> 
    </div>

    <div class="cd-filter-block">
      <h4>Теги</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($data['tags'] as $tag)
          <li>
            @if (!empty($data['filter']['tags']))
                <input class="filter" name="tags[]" type="checkbox" value="{{$tag['id']}}" {{ in_array($tag['id'], $data['filter']['tags']) ? 'checked' : '' }}>
            @else
              <input class="filter" name="tags[]" type="checkbox" value="{{$tag['id']}}">
            @endif
            <label class="checkbox-label">{{$tag['title']}}</label>
          </li>
        @endforeach
      </ul> 
    </div>


    <div class="cd-filter-block">
      <h4>Продавцы</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($data['salers'] as $saler)
          <li>
            @if (!empty($data['filter']['salers']))
              <input class="filter" name="salers[]" type="checkbox" value="{{$saler['id']}}" {{ in_array($saler['id'], $data['filter']['salers']) ? 'checked' : '' }}>
            @else
              <input class="filter" name="salers[]" type="checkbox" value="{{$saler['id']}}">
            @endif
            <label class="checkbox-label">{{$saler['name']}}</label>
          </li>
        @endforeach
      </ul> 
    </div>


    <div class="cd-filter-block">
      <h4>Цена</h4>
      <ul class="cd-filter-content cd-filters list">
        <li>
          <input class="filter" style="width:125px; padding: 5px;" name="prices[minPrice]" type="number" min="{{$data['prices']['minPrice']}}" max="{{$data['prices']['maxPrice']}}" value="{{ isset($data['filter']['prices']['minPrice']) ? $data['filter']['prices']['minPrice'] : '' }}" placeholder="{{$data['prices']['minPrice']}}">
          <input class="filter" style="width:125px; padding: 5px;" name="prices[maxPrice]" type="number" min="{{$data['prices']['minPrice']}}" max="{{$data['prices']['maxPrice']}}" value="{{ isset($data['filter']['prices']['maxPrice']) ? $data['filter']['prices']['maxPrice'] : '' }}" placeholder="{{$data['prices']['maxPrice']}}">
        </li>
      </ul> 
    </div>

    <div class="cd-filter-block">
      <h4>Поиск</h4>
      <div class="cd-filter-content">
        <input type="search" placeholder="Try color-1...">
      </div> 
    </div>

    <div class="form-group">
      <input type="submit" class="btn-btn-primary" value="Применить">
      <a class="btn-btn-primary" href="{{ route('api.products_api', $data['category']['title']) }}">Очистить</a>
    </div>
  </form>
  <a href="#0" class="cd-close">Закрыть</a>
</div> 


<a href="#0" class="cd-filter-trigger">Фильтры</a>