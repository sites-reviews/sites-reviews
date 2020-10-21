import ReviewCreateForm from "./components/review/create/form";

(new ReviewsCreate).init();

export default function ReviewsCreate() {

    let self = this;

    this.init = function () {

        self.$form = new ReviewCreateForm();
        self.$form.form = $('.review-create').first();
        self.$form.init();
    }
}
