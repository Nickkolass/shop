<div class="cd-filter">
    <form action="{{ route('api.products.filter', $data['category']['title']) }}" method="post"
          enctype="multipart/form-data">
        @csrf

        <div class="cd-filter-block">
            <h4>Поиск</h4>
            <div class="cd-filter-content">
                <input type="search" name="filter[search]" value="{{$data['filter']['search'] ?? '' }}">
            </div>
        </div>

        <div class="cd-filter-block">
            <h4>Цена</h4>
            <ul class="cd-filter-content cd-filters list">
                <li>
                    @foreach($data['filterable']['prices'] as $key => $price)
                        <input class="filter" style="width:125px; padding: 5px;" name="filter[prices][{{$key}}]"
                               type="number" min="0" max="{{$data['filterable']['prices']['max']}}"
                               value="{{ $data['filter']['prices'][$key] ?? '' }}"
                               placeholder="{{$data['filterable']['prices'][$key]}}">
                    @endforeach
                </li>
            </ul>
        </div>

        <div class="cd-filter-block">
            <h4>Отображать на странице</h4>
            <ul class="cd-filter-content cd-filters list">
                @for($i=8; $i <= 32; ($i = $i*2))
                    <li>
                        <input class="filter" type="radio" name="paginate[perPage]"
                               value={{$i}}  @checked($data['paginate']['perPage'] == $i)>
                        <label class="radio-label" for="radio">По {{$i}}</label>
                    </li>
                @endfor
            </ul>
        </div>

        <div class="cd-filter-block">
            <h4>Сортировка</h4>
            <ul class="cd-filter-content cd-filters list">
                <li>
                    <input class="filter" type="radio" name="paginate[orderBy]"
                           value='rating' @checked($data['paginate']['orderBy'] == 'rating')>
                    <label class="radio-label" for="radio">По рейтингу</label>
                </li>
                <li>
                    <input class="filter" type="radio" name="paginate[orderBy]"
                           value='latest' @checked($data['paginate']['orderBy'] == 'latest')>
                    <label class="radio-label" for="radio">По дате публикации</label>
                </li>
                <li>
                    <input class="filter" type="radio" name="paginate[orderBy]"
                           value='ASC' @checked($data['paginate']['orderBy'] == 'ASC')>
                    <label class="radio-label" for="radio">По возрастанию цены</label>
                </li>
                <li>
                    <input class="filter" type="radio" name="paginate[orderBy]"
                           value='DESC' @checked($data['paginate']['orderBy'] == 'DESC')>
                    <label class="radio-label" for="radio">По убыванию цены</label>
                </li>

            </ul>
        </div>

        <!-- Options -->
        @foreach($data['filterable']['option_values'] as $option => $option_values)
            <div class="cd-filter-block">
                <h4>{{$option}}</h4>
                <ul class="cd-filter-content cd-filters list">
                    @foreach ($option_values as $option_value)
                        <li><input class="filter" name="filter[optionValues][{{$option_value['option_id']}}][]" type="checkbox"
                                   value="{{$option_value['id']}}" @checked(in_array($option_value['id'], $data['filter']['optionValues'][$option_value['option_id']] ?? []))>
                            <label class="checkbox-label">{{$option_value['value']}}</label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        @foreach($data['filterable']['property_values'] as $property => $property_values)
            <div class="cd-filter-block">
                <h4>{{$property}}</h4>
                <ul class="cd-filter-content cd-filters list">
                    @foreach ($property_values as $property_value)
                        <li><input class="filter" name="filter[propertyValues][{{$property_value['property_id']}}][]" type="checkbox"
                                   value="{{$property_value['id']}}" @checked(in_array($property_value['id'], $data['filter']['propertyValues'][$property_value['property_id']] ?? []))>
                            <label class="checkbox-label">{{$property_value['value']}}</label>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div class="cd-filter-block">
            <h4>Теги</h4>
            <ul class="cd-filter-content cd-filters list">
                @foreach ($data['filterable']['tags'] as $tag)
                    <li>
                        @if (!empty($data['filter']['tags']))
                            <input class="filter" name="filter[tags][]" type="checkbox"
                                   value="{{$tag['id']}}" @checked(in_array($tag['id'], $data['filter']['tags']))>
                        @else
                            <input class="filter" name="filter[tags][]" type="checkbox" value="{{$tag['id']}}">
                        @endif
                        <label class="checkbox-label">{{$tag['title']}}</label>
                    </li>
                @endforeach
            </ul>
        </div>


        <div class="cd-filter-block">
            <h4>Продавцы</h4>
            <ul class="cd-filter-content cd-filters list">
                @foreach ($data['filterable']['salers'] as $saler)
                    <li>
                        @if (!empty($data['filter']['salers']))
                            <input class="filter" name="filter[salers][]" type="checkbox"
                                   value="{{$saler['id']}}" @checked(in_array($saler['id'], $data['filter']['salers']))>
                        @else
                            <input class="filter" name="filter[salers][]" type="checkbox" value="{{$saler['id']}}">
                        @endif
                        <label class="checkbox-label">{{$saler['name']}}</label>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="form-group">
            <input type="submit" class="btn-btn-primary" value="Применить">
            <a type="button" class="btn-btn-primary"
               href="{{ route('api.products.filter', $data['category']['title']) }}">Очистить</a>
        </div>
    </form>
    <a href="#0" class="cd-close">Закрыть</a>
</div>

<a href="#0" class="cd-filter-trigger">Фильтры</a>

