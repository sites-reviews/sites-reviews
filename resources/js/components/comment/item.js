export default function Comment() {

    let self = this;

    this.init = function (comment) {

        self.comment = comment;

        self.buttons = self.comment.find('.buttons').first();

        self.descendants = self.comment.find('.descendants').first();

        self.rate_up = self.buttons.find('.rate_up').first();
        self.rate_down = self.buttons.find('.rate_down').first();

        self.$btn_delete = self.buttons.find('.delete').first();
        self.$btn_restore = self.buttons.find('.restore').first();

        self.$btn_delete.unbind('click').on('click', self.onDeleteClick);
        self.$btn_restore.unbind('click').on('click', self.onRestoreClick);

        self.toggle_children = self.buttons.find('.toggle_children').first();
        self.show_children = self.toggle_children.find('.show_children').first();
        self.hide_children = self.toggle_children.find('.hide_children').first();
        self.children_count = self.toggle_children.find('.count').first();

        self.reply = self.buttons.find('.reply').first();

        self.rate_up.unbind('click').on('click', self.onRateUpClick);
        self.rate_down.unbind('click').on('click', self.onRateDownClick);
        self.reply.unbind('click').on('click', self.onReplyClick);
        self.toggle_children.unbind('click').on('click', self.toggleChildren);

        self.rating = self.buttons.find('.rating');
    };

    this.onRateUpClick = function (event) {

        console.log('onRateUpClick');

        event.preventDefault();

        self.rate_up.addClass('disabled');
        self.rate_down.addClass('disabled');

        $.ajax({
            url: self.rate_up.attr('href')
        }).done(self.onRateUpOrDown)
            .fail(function () {
                self.rate_up.removeClass('disabled');
                self.rate_down.removeClass('disabled');
            });
    };

    this.onRateDownClick = function (event) {

        console.log('onRateDownClick');

        event.preventDefault();

        self.rate_up.addClass('disabled');
        self.rate_down.addClass('disabled');

        $.ajax({
            url: self.rate_down.attr('href')
        }).done(self.onRateUpOrDown)
            .fail(function () {
                self.rate_up.removeClass('disabled');
                self.rate_down.removeClass('disabled');
            });
    };

    this.onRateUpOrDown = function (data) {

        self.rating.show();
        self.rating.text(data.rateable.rating);

        self.rate_up.removeClass('disabled');
        self.rate_down.removeClass('disabled');

        if (data.rateable.rating > 0)
        {
            self.rate_up.addClass('active');
            self.rate_down.removeClass('active');

        }
        else if (data.rateable.rating < 0)
        {
            self.rate_up.removeClass('active');
            self.rate_down.addClass('active');
        }
        else
        {
            self.rate_up.removeClass('active');
            self.rate_down.removeClass('active');
        }
    };

    this.onReplyClick = function (event) {

        event.preventDefault();

        self.reply.addClass('disabled');
        self.descendants.html('Загрузка');

        $.ajax({
            url: self.reply.attr('href')
        }).done(function (data) {

            self.descendants.html(data);

            self.reply.removeClass('disabled');

            let form = self.descendants.find('form');

            form.on('submit', function(e) {

                console.log('submitted');

                e.preventDefault();

                $(this).ajaxSubmit({
                    dataType: 'json',
                    success: self.onReplySubmitSuccess
                })
            });

        }).fail(function () {

        });
    };

    this.onReplySubmitSuccess = function (data, textStatus) {
        console.log('success');

        console.log(data);

        self.hideChildren();
        self.openChildren(function () {
            $.scrollTo($('.comment[data-id="' + data.id + '"]'), 800);
        });
    };

    this.openChildren = function (onChildrenOpened = function () {}) {

        console.log('openChildren');

        self.toggle_children.addClass('disabled');

        self.descendants.html('Загрузка');

        $.ajax({
            url: self.toggle_children.attr('href')
        }).done(function (data) {

            self.show_children.hide();
            self.hide_children.show();

            self.toggle_children.removeClass('disabled');

            self.descendants.html(data);

            self.descendants.find('.comment').each(function () {
                let $comment = new Comment();
                $comment.init($(this));
            });

            onChildrenOpened();

        }).fail(function () {

            self.toggle_children.removeClass('disabled');

            self.descendants.html('');
        });
    };

    this.hideChildren = function () {
        self.toggle_children.addClass('disabled');
        self.descendants.html('');
        self.toggle_children.removeClass('disabled');
        self.show_children.show();
        self.hide_children.hide();
    };

    this.isChildrenShowed = function () {
        return (self.descendants.find('.comment').length > 0);
    };

    this.toggleChildren = function (event) {

        event.preventDefault();

        if (self.isChildrenShowed()) {
            self.hideChildren();
        } else {
            self.openChildren();
        }
    };

    this.updateChildsCount = function () {
        self.children_count.text(self.descendants.find('.comment').length);
    };

    this.onDeleteClick = function (event) {

        event.preventDefault();

        self.$btn_delete.addClass('disabled');

        $.ajax({
            method: "delete",
            url: self.$btn_delete.attr('href')
        }).done(function (data) {

            self.$btn_delete.removeClass('disabled');

            if (data.deleted_at)
            {
                self.$btn_delete.hide();
                self.$btn_restore.show();
            }
            else
            {
                self.$btn_delete.show();
                self.$btn_restore.hide();
            }

        }).fail(function () {
            self.$btn_delete.removeClass('disabled');
        });
    };

    this.onRestoreClick = function (event) {

        event.preventDefault();

        self.$btn_restore.addClass('disabled');

        $.ajax({
            method: "delete",
            url: self.$btn_restore.attr('href')
        }).done(function (data) {

            self.$btn_restore.removeClass('disabled');

            if (data.deleted_at)
            {
                self.$btn_delete.hide();
                self.$btn_restore.show();
            }
            else
            {
                self.$btn_delete.show();
                self.$btn_restore.hide();
            }

        }).fail(function () {
            self.$btn_restore.removeClass('disabled');
        });
    };
}
