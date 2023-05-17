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
            for (let i in inputs) {
              if (inputs.hasOwnProperty(i)) {
                inputs[i].name = `optionValues[${rowNum}][value]`;
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