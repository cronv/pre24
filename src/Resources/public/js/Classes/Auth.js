var Auth = {
    init: function ($wrapper) {
        this.$wrapper = $wrapper;
        this.$wrapper.on('click', '.js-form-select', this.toggleForm.bind(this));
    },

    toggleForm: function(e) {
        var target = $(e.target)
            , formId = target.data('form')
        ;

        $('.js-form-select').removeClass('button-animate-static');
        target.addClass('button-animate-static');

        $('.js-form').addClass('d-none');
        $('#' + formId).closest('div').removeClass('d-none');
    },
};

$(function() {
    Auth.init($('body'));
});

