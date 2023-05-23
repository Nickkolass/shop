<script type="text/javascript">
  var Multi = function(id) {
    var container = document.getElementById(id);
    var that = this;
    old = JSON.parse(container.getAttribute('data-old'));

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
                inputs[i].name = `${inputs[i].getAttribute('data-name')}[${rowNum}][value]`;
                if(old != null) {
                  if (old[rowNum] != null) inputs[i].setAttribute('value', old[rowNum]['value']);
                  else if(inputs[i].value == inputs[0].value) inputs[i].setAttribute('value', '');
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