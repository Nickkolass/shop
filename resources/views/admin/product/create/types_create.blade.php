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
          <a href="{{ route('admin.index') }}">Главная</a>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">
      @csrf
      <div class="row align-items-start">
        <div class="col">
          @include('admin.product.errors')
          <div id="multi" data-old="{{json_encode(old('types'))}}">
            <div class="js-row input-group">
              <div class="mr-auto" style="text-align:center;">
                <label>Классификаторы</label><br>
                @foreach($optionValues as $option => $values)
                <select name="types[0][optionValues][]" data-name="optionValues" style="width:200px">
                  <option value="">{{ $option }}</option>
                  @foreach($values as $value)
                  <option value="{{ $value->id }}" @selected(in_array($value->id, old('types.0.optionValues') ?? []) )
                    >{{ $value->value }}</option>
                  @endforeach
                </select>
                <div class="w-100"></div>
                @endforeach
              </div>

              <div class="mr-auto" style="text-align:center;">
                <label>Цена</label>
                <input type="number" name="types[0][price]" value="{{ old('types.0.price') }}" class="form-control"
                  data-name="price" required>

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Остаток</label>
                <input type="number" name="types[0][count]" value="{{ old('types.0.count') }}" class="form-control"
                  data-name="count" required>

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Публикация</label>
                <input type="checkbox" value="1" name="types[0][is_published]" @checked(old('types.0.is_published'))
                  class="form-control" data-name="is_published">

              </div>
              <div class="mr-auto" style="text-align:center;">
                <label>Заставка</label>
                <div class="card">
                  <div class="card-body card-block">
                    <div class="row form-group">
                      <div class="control-group">
                        <div class="controls">
                          <div class="entry input-group upload-input-group">
                            <input name="types[0][preview_image]" type="file" class="form-control"
                              data-name="preview_image">
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
                            <input class="form-control" data-name="productImages" name="types[0][productImages][]"
                              type="file" multiple>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <button type="button" id="load_old_types" class="js-add btn btn-outline-primary">+</button>
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
  var Multi = function (id) {
  var container = document.getElementById(id);
  var that = this;
  old = JSON.parse(container.getAttribute('data-old'));
  this.start = function (id) {
    container.querySelector('.js-add').addEventListener('click', function () {
      let newElement = container.querySelector('.js-row').cloneNode(true);
      newElement.querySelector('.js-add').remove();
      let removeButton = document.createElement('button');
      removeButton.classList.add('js-remove');
      removeButton.classList.add('btn');
      removeButton.classList.add('btn-outline-danger');
      removeButton.setAttribute('data-id', 1);
      removeButton.append(document.createTextNode(" - "));
      removeButton.addEventListener('click', function () {
        this.parentElement.remove();
      });

      newElement.append(removeButton);
      container.append(newElement);
      that.setNames();
    });
  },

    this.setNames = function () {
      let rows = container.querySelectorAll('.js-row');
      let rowNum = 0;
      for (let key in rows) {
        if (rows.hasOwnProperty(key)) {
          let inputs = rows[key].querySelectorAll('input');
          let selects = rows[key].querySelectorAll('select');
          for (let i in inputs) {
            if (inputs.hasOwnProperty(i)) {
              var name = inputs[i].getAttribute('data-name');
              if (name == 'productImages') {
                inputs[i].name = `types[${rowNum}][${name}][]`;
              } else {
                inputs[i].name = `types[${rowNum}][${name}]`;
                if (old != null) {
                  if (old[rowNum] != null) {
                    if (old[rowNum][name] != null) {
                      if (name == 'price' || name == 'count') inputs[i].setAttribute('value', (old[rowNum][name]));
                      if (name == 'is_published') inputs[i].checked = true;
                    }
                  }
                }
              }
            }
          }

          for (let i in selects) {
            if (selects.hasOwnProperty(i)) {
              var name = selects[i].getAttribute('data-name');
              selects[i].name = `types[${rowNum}][${name}][]`;
              if (old != null) {
                if (old[rowNum] != null) {
                  if (old[rowNum][name] != null) {
                    for (let j in selects[i].options) {
                      if (inputs.hasOwnProperty(j)) {
                        if (old[rowNum][name][i] != null) {
                          if (selects[i].options[j].value == old[rowNum][name][i]) {
                            selects[i].options[j].selected = true;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          rowNum++;
        }
      }
    }
}
var o = new Multi('multi');
o.start();

if (old != null) {
  $(document).ready(function () {
    let i = 1;
    let j = old.length;
    while (i < j) {
      $("#load_old_types").click();
      i++;
    }
  });
}
</script>



@endsection