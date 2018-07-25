export default class OCampaignFixed {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.image = this.context.getImage();
        this.imageWrapper = this.context.getImageWrapper();
    }

    run() {
        if (! this.done) {
            this.setPosition();
            this.done = true;
        }

        this.setBluring();
    }

    setPosition() {
        this.imageWrapper.style.transform = this.context.translate(this.context.getMenu().clientHeight);
    }

    setBluring() {
        let scrollFactor = (this.context.getScrollTop() - this.image.clientTop) / this.context.getScrollStart();
        let blur = this.easeInQuint( scrollFactor ) * this.context.getSizedBlur();

        this.image.style.filter = `blur(${blur}px)`;
    }

    easeInQuint(t) {
        return t * t * t * t;
    }
}