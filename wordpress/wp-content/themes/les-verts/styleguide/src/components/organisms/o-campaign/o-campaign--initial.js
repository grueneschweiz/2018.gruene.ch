export default class OCampaignInitial {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.image = this.context.getImage();
        this.imageWrapper = this.context.getImageWrapper();

        let menu = this.context.getMenu();
        this.menuBottom = menu.offsetTop + menu.clientHeight;
    }

    run() {
        if (!this.done) {
            this.setBluring();
            this.done = true;
        }

        this.setPosition();
    }

    setPosition() {
        this.imageWrapper.style.transform = this.context.translate(-this.context.getScrollTop() + this.menuBottom);
    }

    setBluring() {
        this.image.style.filter = 'none';
    }
}