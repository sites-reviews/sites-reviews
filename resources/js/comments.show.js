import Comment from "./components/comment/item";

(new CommentsShow).init();

export default function CommentsShow() {

    let self = this;

    this.init = function () {

        let $comment = new Comment();
        $comment.init($('.comment').first());
    };
}
