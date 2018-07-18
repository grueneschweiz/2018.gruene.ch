export default class OCampaignFixed {
    constructor(context) {
        this.context = context;
        this.fixed = false;
        this.image = this.context.getImage();
    }

    run() {
        if (! this.fixed) {
            this.setPosition();
            this.fixed = true;
        }

        this.setBluring();
    }

    setPosition() {
        let menu = this.context.getHeader().getMenu();

        this.image.style.position = 'fixed';
        this.image.style.top = (menu.offsetTop + menu.clientHeight) + "px";
    }

    setBluring() {
        let blur = (this.context.getScrollTop() - this.image.clientTop) / this.context.getScrollStart() * this.context.getSizedBlur();

        this.image.style.filter = "blur("+blur+"px)";
    }
}