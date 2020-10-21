import StarRating from "../../star-rating";

export default function ReviewCreateForm() {

    let self = this;

    this.init = function () {

        self.form = $('.review-create').first();

        self.$starRating = new StarRating;
        self.$starRating.stars = self.form.find('.btn-group');
        self.$starRating.name_container = self.form.find('.name_container').first();
        self.$starRating.input = self.form.find('[name="rate"]').first();
        self.$starRating.init();

        self.$advantages = self.form.find('#advantages');
        self.$disadvantages = self.form.find('#disadvantages');
        self.$comment = self.form.find('#comment');

        self.$email = self.form.find('#email');
        self.$captcha = self.form.find('#captcha');
        self.$btn_publish = self.form.find('#btn-publish');

        self.$starRating.stars
            .unbind('click')
            .bind('click', self.onStarsClick);

        self.$advantages.unbind('change keydown').bind('change keydown', self.onTextAreaChange);
        self.$disadvantages.unbind('change keydown').bind('change keydown', self.onTextAreaChange);
        self.$comment.unbind('change keydown').bind('change keydown', self.onTextAreaChange);
    };

    this.onStarsClick = function () {
        self.$advantages.parent('div').show();
        self.$disadvantages.parent('div').show();
        self.$comment.parent('div').show();
    };

    this.onTextAreaChange = function () {
        self.$email.parent('div').show();
        self.$btn_publish.show();
        self.$captcha.show();
    };
}
