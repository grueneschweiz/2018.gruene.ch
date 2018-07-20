export default class OCampaignFixed {
    constructor(context) {
        this.context = context;
        this.fixed = false;
        this.image = this.context.getImage();
        this.imageWrapper = this.context.getImageWrapper();
    }

    run() {
        if (! this.fixed) {
            this.setPosition();
            this.fixed = true;
        }

        this.setBluring();
    }

    setPosition() {
        this.imageWrapper.style.top = this.context.getHeader().getMenu().clientHeight + 'px';
    }

    setBluring() {
        let blur = (this.context.getScrollTop() - this.image.clientTop) / this.context.getScrollStart();

        this.image.style.filter = "blur(" + this.easeInQuint(blur) * this.context.getSizedBlur() + "px)";
    }

    easeInQuint(t) {
        return t * t * t * t;
    }
}