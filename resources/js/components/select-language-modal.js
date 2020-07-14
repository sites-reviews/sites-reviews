let bootbox = require('bootbox');

export default function SelectLanguageModal() {

    let self = this;

    this.init = function () {

        self.$loading = false;
        self.$loaded = false;

        self.$href = self.$button.data('url');

        self.$button.unbind('click').on('click', function () {

            if (self.dialog === undefined)
            {
                self.dialog = bootbox.dialog({
                    title: ' ',
                    message: '<div class="text-center"><div class="spinner-border" role="status" style="width: 3rem; height: 3rem;">\n' +
                        '<span class="sr-only">Loading...</span>\n' +
                        '</div></div>'
                });

                self.onClick();
            }
            else
            {
                self.dialog.modal('show');
            }
        });
    };

    this.onClick = function () {

        if (!self.$loading && !self.$loaded) {

            self.dialog.init(function () {

                $.ajax({
                    url: self.$href,
                    beforeSend: function () {
                        self.$loading = true;
                    }
                }).done(self.onAjaxLoadingDone)
                    .fail(function () {
                        self.$loading = false;
                        self.$loaded = false;
                    });
            });

        }
    };

    this.onAjaxLoadingDone = function (html) {

        self.dialog.find('.bootbox-body').html(html);

        self.$loaded = true;
        self.$loading = false;
    };
}
