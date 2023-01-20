@extends('client.layouts.main')
@section('content')
<!-- Page Content -->


<div class="section-heading">
  <div class="banner header-text">
    <div class="owl-banner owl-carousel">
      @foreach ($categories as $category)
      <div>
        <a class="nav-link" href="{{ route('client.product_client', $category->title) }}"> <img src="{{asset('/storage/view/'.$category->title.'.jpg')}}" alt="">
          <div class="text-content">
            <p><strong>
                <h2>{{$category->title_rus}}</h2>
              </strong></p>
          </div>
        </a>
      </div>
      @endforeach
    </div>
  </div>
</div>

@endsection