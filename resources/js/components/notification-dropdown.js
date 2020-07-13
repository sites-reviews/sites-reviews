export default function NotificationDropdown() {

    let self = this;

    this.init = function () {

        self.loading = false;
        self.loaded = false;

        self.$btn = self.dropdown.find('button');
        self.$menu = self.dropdown.find('.dropdown-menu');
        self.$content = self.$menu.find('.content');

        self.href = self.$btn.data('href');

        self.dropdown.on('show.bs.dropdown', self.onShowDropdown);
        self.dropdown.on('shown.bs.dropdown', self.onShownDropdown);

    };

    this.onShowDropdown = function () {

        if (!self.loading && !self.loaded)
        {
            $.ajax({
                url: self.href,
                beforeSend: function () {
                    self.loading = true;
                }
            }).done(self.onAjaxLoadingDone)
                .fail(function () {
                    self.loading = false;
                    self.loaded = false;
                });
        }
    };

    this.onAjaxLoadingDone = function (data) {
        self.$content.html(data);

        self.loaded = true;
        self.loading = false;
    };
}
