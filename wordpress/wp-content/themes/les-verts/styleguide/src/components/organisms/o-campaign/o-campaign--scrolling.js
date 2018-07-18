export default class OCampaignScrolling {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.image = this.context.getImage();
    }

    run() {
        if (! this.done) {
            this.setPosition();
            this.setBluring();
            this.done = true;
        }
    }

    setPosition() {
        this.image.style.position = 'absolute';
        this.image.style.top = this.getTopPosition();
    }

    setBluring() {
        let blur = this.context.getSizedBlur();

        this.image.style.filter = "blur("+blur+"px)";
    }

    getTopPosition() {
        let header = this.context.getHeader();
        let brandingHeight = header.getBranding().clientHeight;
        let menuHeight = header.getMenu().clientHeight;
        let imageHeight = this.context.getImage().clientHeight;

        let scroll = this.context.getScrollTop() - brandingHeight;
        let max =  imageHeight - brandingHeight - menuHeight;

        return Math.min(scroll, max) + 'px';
    }
}