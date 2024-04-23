var Survey = {
    startIndex: 2,
    endIndex: 5,
    tmp: undefined,

    init: function ($wrapper) {
        this.$wrapper = $wrapper;
        this.$editForm = $('#editModal');

        this.$wrapper.on('click', '.js-edit-render', this.editRender.bind(this));
        this.$wrapper.on('click', '.js-delete', this.delete.bind(this));
        this.$wrapper.on('submit', '#addForm, #updateForm', this.handlePush.bind(this));
    },

    editRender: function(e) {
        var target = $(e.target)
            , tr = target.closest('tr')
            , uuid = tr.data('id')
            , data = tr.find('td').slice(this.startIndex, this.endIndex)
            , form = this.$editForm.find('form')
            , self = this
            , count = {
                datetime: 0,
            }
            , tmp = undefined
            , tmpDate = undefined
        ;

        $.each(data, function (k, v) {
            tmp = self.$editForm.find('input[name="' + $(v).data('input-name') + '"],' +
                'input[type="' + $(v).data('input-name') + '"], textarea[name="'
                + $(v).data('input-name') + '"]' + ', select[name="' + $(v).data('input-name') + '"]');

            if ((['deadline', 'type']).indexOf($(v).data('input-name')) !== -1) {
                tmp.prop('value', $(v).prop('innerText').split(".").reverse().join("-"));
                return;
            } else if ($(v).data('input-name') === 'datetime-local') {
                tmpDate = $(v).prop('innerText').match(/\d+/g);

                if (!tmpDate) {
                    return;
                }

                tmpDate = new Date(tmpDate[2], tmpDate[1], tmpDate[0], tmpDate[3], tmpDate[4], tmpDate[5]);
                tmp.eq(count.datetime).prop('value', tmpDate.toISOString().slice(0, 19).replace(' ', 'T'));
                ++count.datetime;
                return;
            } else if ($(v).data('input-name') === 'is_correct') {
                tmp.prop('value', $(v).data('value').toString());
                return;
            }

            tmp.val($(v).prop('innerText'));
        });

        form.prop('action', form.prop('action').replace(/\/[^/]*$/, '/' + uuid));
        this.$editForm.modal('show');
    },

    handlePush: function (e) {
        e.preventDefault();
        var currentTarget = $(e.currentTarget)
            , action = currentTarget.attr('action')
            , method = currentTarget.attr('method')
            , serializeArray = currentTarget.serializeArray()
            , buttonSend = currentTarget.find('button[type="submit"]')
            , jsonData = this.jsonData(serializeArray)
            , self = this
        ;

        this.clearValid(currentTarget);

        $.ajax({
            url: action,
            type: method,
            dataType: 'json',
            data: jsonData,
            contentType: 'application/json',
            success: function(response, status, xhr) {
                self.showMessage(xhr.responseJSON, xhr.status, currentTarget);
                self.clearInput(currentTarget);
            },
            error: function(xhr, status, error) {
                self.showMessage(xhr.responseJSON, xhr.status, currentTarget);
            }
        });
        return false;
    },

    delete: function (e) {
        e.preventDefault();
        var currentTarget = $(e.currentTarget)
            , action = currentTarget.prop('href')
            , method = 'DELETE'
            , self = this
        ;

        $.ajax({
            url: action,
            type: method,
            dataType: 'json',
            success: function(response, status, xhr) {
                self.showMessage(xhr.responseJSON, xhr.status, currentTarget);
            },
            error: function(xhr, status, error) {
                self.showMessage(xhr.responseJSON, xhr.status, currentTarget);
            }
        });

        return false;
    },

    showMessage: function (data, code, form) {
        var alert = $('#toast')
            , tmp = undefined
            , alertClass = 'alert-danger'
        ;

        alert.removeClass(alertClass);

        switch (code) {
            case 409:
                this.errors(data, form);
                break;
            case 204:
                $(form).closest('tr').remove()
                break;
            case 400:
            case 404:
                if (!data.message) {
                    this.errors(data, form);
                    break;
                }

                alertClass = 'alert-danger';
                alert
                    .addClass(alertClass)
                    .find('.toast-body')
                    .html(data.message);
                alert.toast('show', {
                    showDuration: 5000
                });
                break;
            case 200:
            case 201:
                form.closest('div .modal').modal('hide');
                alert
                    .addClass('alert-success')
                    .find('.toast-body')
                    .html(data.message);
                alert.toast('show', {
                    showDuration: 5000
                });
                break;
            default:
                break;
        }
    },

    errors: function(data, form) {
        var self = this;

        $.each(data.errors, function (i, message) {
            self.tmp = message;

            if (typeof message.message === 'string') {
                self.tmp = message.message;
            }

            form.find('input[name="' + i + '"]')
                .addClass('is-invalid')
                .after(
                    '<div class="invalid-feedback">\n' + self.tmp +
                    '            </div>'
                );
        });
    },

    clearValid: function(form) {
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    },

    clearInput: function (form) {
        form.find('input:not([type="hidden"])').val('');
    },

    jsonData: function (serializeArray) {
        var jsonData = {}
            , tmp = undefined
        ;
        $.each(serializeArray, function(index, field) {
            tmp = field.value;

            if (!tmp) {
                tmp = null;
            }

            jsonData[field.name] = tmp;
        });
        return JSON.stringify(jsonData);
    },
};

$(function() {
    Survey.init($('body'));
});
