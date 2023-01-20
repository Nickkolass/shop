<div class="cd-filter">
  <form action="{{ route('client.filter_client', $category->title) }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="cd-filter-block" hidden>
      <h4>Категория</h4>
      <div class="cd-filter-content">
        <div class="cd-select cd-filters">
          <select class="filter" name="category" id="selectThis">
            <option value="{{$category->id}}" selected disabled>{{$category->title_rus}}</option>
          </select>
        </div> <!-- cd-select -->
      </div> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->

    <div class="cd-filter-block">
      <h4>Цвета</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($colors as $color)
        <li>
          @if(empty($data['colors']))
            <input class="filter" name="colors[]" type="checkbox" value="{{$color['id']}}">
          @else
            @foreach ($data['colors'] as $checked)
              @if($color['id'] == $checked)
                <input class="filter" name="colors[]" type="checkbox" value="{{$color['id']}}" checked>
              @break
            @break
          @break
              @else
                <input class="filter" name="colors[]" type="checkbox" value="{{$color['id']}}">
              @endif
            @endforeach
          @endif
          <label class="checkbox-label">{{$color['title']}}</label>
        </li>
        @endforeach
      </ul> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->

    <div class="cd-filter-block">
      <h4>Теги</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($tags as $tag)
        <li>
          @if(empty($data['tags']))
          <input class="filter" name="tags[]" type="checkbox" id="checkbox1" value="{{$tag['id']}}">
          @else
          @foreach ($data['tags'] as $checked)
              @if($tag['id'] == $checked)
                <input class="filter" name="tags[]" type="checkbox" value="{{$tag['id']}}" checked>
              @break
            @break
          @break
              @else
                <input class="filter" name="tags[]" type="checkbox" value="{{$tag['id']}}">
              @endif
            @endforeach
          @endif
          <label class="checkbox-label" for="checkbox1">{{$tag['title']}}</label>
        </li>
        @endforeach
      </ul> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->


    <div class="cd-filter-block">
      <h4>Продавцы</h4>
      <ul class="cd-filter-content cd-filters list">
        @foreach ($salers as $saler)
        <li>
          @if(empty($data['salers']))
          <input class="filter" name="salers[]" type="checkbox" id="checkbox1" value="{{$saler['id']}}">
          @else
          @foreach ($data['salers'] as $checked)
              @if($saler['id'] == $checked)
                <input class="filter" name="salers[]" type="checkbox" value="{{$saler['id']}}" checked>
              @break
            @break
          @break
              @else
                <input class="filter" name="salers[]" type="checkbox" value="{{$saler['id']}}">
              @endif
            @endforeach
          @endif
          <label class="checkbox-label" for="checkbox1">{{$saler['name']}}</label>
        </li>
        @endforeach
      </ul> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->


    <div class="cd-filter-block">
      <h4>Цена</h4>
      <ul class="cd-filter-content cd-filters list">
        <li>
          @if(isset($data['prices']['minPrice']))
          <input class="filter" name="prices[minPrice]" data-filter=".check1" type="text" id="checkbox1" value="{{$data['prices']['minPrice']}}" placeholder="{{$prices['minPrice']}}">
          @else
          <input class="filter" name="prices[minPrice]" data-filter=".check1" type="text" id="checkbox1" placeholder="{{$prices['minPrice']}}">
          @endif
          @if(isset($data['prices']['maxPrice']))
          <input class="filter" name="prices[maxPrice]" data-filter=".check1" type="text" id="checkbox1" value="{{$data['prices']['maxPrice']}}" placeholder="{{$prices['maxPrice']}}">
          @else
          <input class="filter" name="prices[maxPrice]" data-filter=".check1" type="text" id="checkbox1" placeholder="{{$prices['maxPrice']}}">
          @endif
        </li>
      </ul> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->

    <div class="cd-filter-block">
      <h4>Поиск</h4>
      <div class="cd-filter-content">
        <input type="search" placeholder="Try color-1...">
      </div> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->


    <div class="cd-filter-block">
      <h4>Radio buttons</h4>

      <ul class="cd-filter-content cd-filters list">
        <li>
          <input class="filter" data-filter="" type="radio" name="radioButton" id="radio1" checked>
          <label class="radio-label" for="radio1">All</label>
        </li>

        <li>
          <input class="filter" data-filter=".radio2" type="radio" name="radioButton" id="radio2">
          <label class="radio-label" for="radio2">Choice 2</label>
        </li>

        <li>
          <input class="filter" data-filter=".radio3" type="radio" name="radioButton" id="radio3">
          <label class="radio-label" for="radio3">Choice 3</label>
        </li>
      </ul> <!-- cd-filter-content -->
    </div> <!-- cd-filter-block -->

    <div class="form-group">
      <input type="submit" class="btn-btn-primary" value="Применить">
    </div>


  </form>

  <a href="#0" class="cd-close">Закрыть</a>
</div> <!-- cd-filter -->

<a href="#0" class="cd-filter-trigger">Фильтры</a>