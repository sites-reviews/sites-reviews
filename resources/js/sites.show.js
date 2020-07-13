(new SitesShow).init();

import StarRating from "./components/star-rating";
import Review from "./components/review/item";
import Clipboard from "clipboard/src/clipboard";

export default function SitesShow() {

    let self = this;

    this.init = function () {

        let form = $('.review-create');

        let $class = new StarRating;
        $class.stars = form.find('.btn-group');
        $class.name_container = form.find('.name_container').first();
        $class.input = form.find('[name="rate"]').first();
        $class.init();

        self.reviews();

        self.shareRatingModal = $('#shareRatingModal').first();

        self.shareRatingModal.on('shown.bs.modal', self.onShownShareRatingModal);
    };

    this.reviews = function () {

        $('.my_review')
            .find('.review')
            .each(function () {
                let $class = new Review;
                $class.init($(this));
            });

        $('.reviews')
            .find('.review')
            .each(function () {
                let $class = new Review;
                $class.init($(this));
            });
    };

    this.onShownShareRatingModal = function (event) {

        console.log('onShownShareRatingModal');

        self.collapseHtmlCode = self.shareRatingModal.find('#collapseHtmlCode');

        var clipboard = new Clipboard('[data-clipboard-target="#textareaShareRatingHtmlCode"]');

        clipboard.on('success', function(e) {
            self.collapseHtmlCode.find('.alert-success').show();
            self.collapseHtmlCode.find('.alert-danger').hide();

            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            self.collapseHtmlCode.find('.alert-success').hide();
            self.collapseHtmlCode.find('.alert-danger').show();
        });


        self.collapseBBCode = self.shareRatingModal.find('#collapseBBCode');

        var clipboard2 = new Clipboard('[data-clipboard-target="#textareaShareRatingBBCode"]');

        clipboard2.on('success', function(e) {
            self.collapseBBCode.find('.alert-success').show();
            self.collapseBBCode.find('.alert-danger').hide();

            e.clearSelection();
        });

        clipboard2.on('error', function(e) {
            self.collapseBBCode.find('.alert-success').hide();
            self.collapseBBCode.find('.alert-danger').show();
        });

    };
}
