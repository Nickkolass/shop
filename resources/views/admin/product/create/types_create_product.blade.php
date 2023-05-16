@extends('admin.layouts.main')
@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Виды товара</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <a href="{{ route('admin.index_admin') }}">Главная</a>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<h4 hidden>{{$i = 0}}</h4>

<section class="content">
  <div class="container-fluid">
    <form action="{{ route('product.store_product') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row align-items-start">
        <div class="col">
          @include('admin.product.errors')
          <div id="multi">
            <div class="js-row input-group">
              <div class="mr-auto" style="text-align:center;">
                <label>Классификаторы</label><br>
                @foreach($optionValues as $option => $values)
                <select name="types[0][optionValues][]" data-name="optionValues" style="width:200px">
                  <option disabled>{{ $option }}</option>
                  @foreach($values as $value)
                  <option value="{{ $value->id }}" @selected($value->value==($productType->optionValues[$option] ?? '' )) >{{ $value->value }}</option>
                  @endforeach
                </select>
                <div class="w-100"></div>
                @endforeach
              </div>

              <div class="mr-auto" style="text-align:center;">
                <label>Цена</label>
                <input type="number" name="types[0][price]" class="form-control" data-name="price" required>

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Остаток</label>
                <input type="number" name="types[0][count]" class="form-control" data-name="count" required>

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Публикация</label>
                <input type="checkbox" value="1" name="types[0][is_published]" class="form-control" data-name="is_published">

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Заставка</label>
                <div class="card">
                  <div class="card-body card-block">
                    <div class="row form-group">
                      <div class="control-group">
                        <div class="controls">
                          <div class="entry input-group upload-input-group">
                            <input name="types[0][preview_image]" type="file" class="form-control" data-name="preview_image" required>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Изображения</label>
                <div class="card">
                  <div class="card-body card-block">
                    <div class="row form-group">
                      <div class="control-group">
                        <div class="controls">
                          <div class="entry input-group upload-input-group">
                            <input class="form-control" data-name="productImages" name="types[0][productImages][]" type="file" multiple required>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" class="js-add btn btn-outline-primary">+</button>
            </div>
          </div>
        </div>
      </div><!-- /.container-fluid -->
      <div class="col" style="padding: 5px;">
        <input type="submit" class="btn-btn-primary" value="Добавить продукт">
      </div>
    </form>
  </div>
</section>
<!-- /.content -->

<script type="text/javascript">
  var Multi = function(id) {
    var container = document.getElementById(id);
    var that = this;

    this.start = function(id) {
        container.querySelector('.js-add').addEventListener('click', function() {
          let newElement = container.querySelector('.js-row').cloneNode(true);
          newElement.querySelector('.js-add').remove();
          let removeButton = document.createElement('button');
          removeButton.classList.add('js-remove');
          removeButton.classList.add('btn');
          removeButton.classList.add('btn-outline-danger');
          removeButton.setAttribute('data-id', 1);
          removeButton.append(document.createTextNode(" - "));
          removeButton.addEventListener('click', function() {
            this.parentElement.remove();
            that.setNames();
          });

          newElement.append(removeButton);
          container.append(newElement);
          that.setNames();
        });
      },

      this.setNames = function() {
        let rows = container.querySelectorAll('.js-row');
        let rowNum = 0;
        for (let key in rows) {
          if (rows.hasOwnProperty(key)) {
            let inputs = rows[key].querySelectorAll('input');
            let selects = rows[key].querySelectorAll('select');
            for (let i in inputs) {
              if (inputs.hasOwnProperty(i)) {
                if (inputs[i].getAttribute('data-name') == 'productImages') {
                  inputs[i].name = `types[${rowNum}][${inputs[i].getAttribute('data-name')}][]`;
                } else {
                  inputs[i].name = `types[${rowNum}][${inputs[i].getAttribute('data-name')}]`;
                }
              }
            }
            for (let i in selects) {
              if (selects.hasOwnProperty(i)) {
                selects[i].name = `types[${rowNum}][${selects[i].getAttribute('data-name')}][]`;
              }
            }
            rowNum++;
          }
        }
      }
  }
  var o = new Multi('multi');

  o.start();
</script>

@endsection