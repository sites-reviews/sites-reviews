import Review from "./components/review/item";

(new Home).init();

export default function Home() {

    let self = this;

    this.init = function () {

        $('.review').each(function () {
            let $review = new Review();
            $review.init($(this));
        });

    };
}
