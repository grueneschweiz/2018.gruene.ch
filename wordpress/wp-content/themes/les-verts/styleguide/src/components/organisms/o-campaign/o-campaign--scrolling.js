export default class OCampaignScrolling {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.image = this.context.getImage();
        this.imageWrapper = this.context.getImageWrapper();

        this.scrollStart = this.context.getScrollStart();
    }

    run() {
        if (!this.done) {
            this.setBluring();
            this.done = true;
        }

        this.setPosition();
    }

    setPosition() {
        this.imageWrapper.style.top = this.getTopPosition();
    }

    setBluring() {
        let blur = this.context.getSizedBlur();

        this.image.style.filter = "blur(" + blur + "px)";
    }

    getTopPosition() {
        return ((-(this.context.getScrollTop() - this.scrollStart) * 0.5) + this.context.getHeader().getMenu().clientHeight) + 'px';
    }
}