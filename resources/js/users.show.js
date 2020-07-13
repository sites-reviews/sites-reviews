(new UsersShow).init();

import Review from "./components/review/item";

export default function UsersShow() {

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
