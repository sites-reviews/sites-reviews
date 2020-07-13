export default function HeaderSearch() {

    let self = this;

    this.init = function () {

        console.log('HeaderSearch');

        self.$headerSearch = self.$header.find('#header-search');

        self.$input = self.$headerSearch.find('input').first();
        self.$button = self.$headerSearch.find('button').first();

        self.$rightSide = self.$header.find('.right-side').first();

        self.$closeHeaderSearch = self.$header.find('#close_header_search').first();
        self.$logo = self.$header.find('#logo').first();

        self.$button.on('click', function (event) {

            if (self.isInputShrinked())
            {
                event.preventDefault();
                self.expand();
            }
        });

        self.$closeHeaderSearch.on('click', function (event) {
            self.collapse();
        });
    };

    this.isInputShrinked = function () {

        if (self.$input.width() < 150)
            return true;
        else
            return false;
    };

    this.expand = function () {

        console.log('expand');

        //if (self.isInputShrinked())
        //{
        self.$rightSide.hide();
        self.$input.removeClass('d-none');
        self.$input.focus();
        self.$logo.hide();
        self.$closeHeaderSearch.show();
        //}
    };

    this.collapse = function () {

        console.log('collapse');

        self.$rightSide.show();
        self.$input.addClass('d-none');
        self.$logo.show();
        self.$closeHeaderSearch.hide();
    };
}
