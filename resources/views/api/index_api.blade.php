@extends('api.layouts.main')
@section('content')
<!-- Page Content -->
<div class="section-heading">
  <div class="banner header-text">
    <div class="owl-banner owl-carousel">
      @foreach ($categories as $category)
      <div>
        <a class="nav-link" href="{{ route('api.products_api', $category['title']) }}"> <img src="{{asset('/storage/view/'.$category['title'].'.jpg')}}" alt="">
          <div class="text-content">
            <p><strong>
                <h2>{{$category['title_rus']}}</h2>
              </strong></p>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</div>

@if(!empty($data['products']))
<div style="text-align: center">
  <h4>Просмотренные товары</h4>
</div>
@include('api.components.products')
@endif
@endsection