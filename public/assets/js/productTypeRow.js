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
    }

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
                            inputs[i].name = `types[${rowNum}][relations][${name}][]`;
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
                        selects[i].name = `types[${rowNum}][relations][${name}][]`;
                        if (old != null) {
                            if (old[rowNum] != null) {
                                if (old[rowNum]['relations'] != null) {
                                    if (old[rowNum]['relations'][name] != null) {
                                        for (let j in selects[i].options) {
                                            if (inputs.hasOwnProperty(j)) {
                                                if (old[rowNum]['relations'][name][i] != null) {
                                                    if (selects[i].options[j].value == old[rowNum]['relations'][name][i]) {
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
