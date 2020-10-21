import Review from "./components/review/item";

(new UsersReviewsDraft).init();

export default function UsersReviewsDraft() {

    let self = this;

    this.init = function () {

        self.reviews();
    };

    this.reviews = function () {

        $('.reviews')
            .find('.review')
            .each(function () {
                let $class = new Review;
                $class.init($(this));
            });
    }
}
