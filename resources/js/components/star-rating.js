export default function StarRating() {

    let self = this;

    self.colors = {
        1: 'star_color_10',
        2: 'star_color_20',
        3: 'star_color_30',
        4: 'star_color_40',
        5: 'star_color_50',
    };

    this.init = function () {

        if (self.getValue())
            self.viewValue(self.getValue());

        self.stars.find('[data-value]').each(function () {
            let item = $(this);
            let value = item.data('value');

            item.css('cursor', 'pointer');

            item.on( "mouseenter", function () {

                self.viewValue(value);
            });
        });

        self.stars.find('[data-value]').each(function () {
            let item = $(this);
            let value = item.data('value');

            item.unbind("click").on( "click", function () {
                self.setValue(value);
            });
        });

        self.stars.on( "mouseleave", function () {

            if (self.stars.data('value'))
            {
                self.viewValue(self.getValue());
            }
            else
            {
                self.stars.find('[data-value]').each(function () {
                    let item = $(this);

                    self.stars.removeClass(self.getClassNames());

                    item.each(function () {
                        $(this).find('.filled').hide();
                        $(this).find('.empty').show();
                    });

                    self.setStatus('');
                });
            }
        });
    };

    this.setValue = function (value) {

        console.log(value);

        self.stars.data('value', value);

        if (self.input !== undefined)
            self.input.val(value);
    };

    this.getValue = function () {
        return self.stars.data('value');
    };

    this.viewValue = function (value) {

        let item = self.stars.find('[data-value="' + value + '"]');

        let filled_star = item.find('.filled');
        let empty_star = item.find('.empty');

        item.find('.filled').show();
        item.find('.empty').hide();
        self.stars.removeClass(self.getClassNames());
        self.stars.addClass(self.getClassByValue(value));

        item.prevAll().each(function () {
            $(this).find('.filled').show();
            $(this).find('.empty').hide();
        });

        item.nextAll().each(function () {
            $(this).find('.filled').hide();
            $(this).find('.empty').show();
        });

        if (item.data('name'))
            self.setStatus(item.data('name'));
    };

    this.setStatus = function (status) {
        self.name_container.text(status);
    };

    this.getClassByValue = function (value) {
        return self.colors[value];
    };

    this.getClassNames = function () {
        let class_names = '';

        $.each( self.colors, function( key, class_name ) {
            class_names += class_name + ' ';
        });

        return class_names;
    };
}
