export default class OCampaignInitial {
    constructor(context) {
        this.context = context;
        this.done = false;
        this.imgStyle = this.context.getImage().style;
    }

    run() {
        if (! this.done) {
            this.setPosition();
            this.setBluring();
            this.done = true;
        }
    }

    setPosition() {
        this.imgStyle.position = 'absolute';
        this.imgStyle.top = 0;
    }

    setBluring() {
        this.imgStyle.filter = 'none';
    }
}