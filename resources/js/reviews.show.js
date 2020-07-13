import Review from "./components/review/item";

(new ReviewsShow).init();

export default function ReviewsShow() {

    let self = this;

    this.init = function () {

        let $review = new Review();
        $review.init($('.review').first());
    };
}
