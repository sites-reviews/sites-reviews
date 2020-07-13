(new ReviewsEdit).init();

import StarRating from "./components/star-rating";

export default function ReviewsEdit() {

    let self = this;

    this.init = function () {

        let form = $('.reviews-edit');

        let $class = new StarRating;
        $class.stars = form.find('.btn-group');
        $class.name_container = form.find('.name_container').first();
        $class.input = form.find('[name="rate"]').first();
        $class.init();
    };
}
