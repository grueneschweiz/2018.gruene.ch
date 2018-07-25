export default class OCampaignInitial {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.image = this.context.getImage();
        this.imageWrapper = this.context.getImageWrapper();
    }

    run() {
        if (!this.done) {
            this.setBluring();
            this.done = true;
        }

        this.setPosition();
    }

    setPosition() {
        this.imageWrapper.style.transform = this.context.translate(-this.context.getScrollTop());
    }

    setBluring() {
        this.image.style.filter = 'none';
    }
}