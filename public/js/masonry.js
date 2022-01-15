class Masonry {
    constructor(props) {
        this.dividedBy = props.dividedBy;
        this.items = selectAll(props.items);
        this.totalItems = this.items.length;
        this.mod = this.totalItems % this.dividedBy;
        this.squaredItem = this.totalItems - this.mod;
        this.itemPerRow = this.squaredItem / this.dividedBy;
        this.container = props.container;

        this.k = 0;
        for (let i = 0; i < this.dividedBy; i++) {
            createElement({
                el: 'div',
                attributes: [
                    ['class', `bagi bagi-${this.dividedBy} row-${i}`]
                ],
                createTo: this.container
            });

            for (let j = 0; j < this.itemPerRow; j++) {
                let htmlContent = this.items[i].innerHTML;
                createElement({
                    el: 'div',
                    attributes: [['class', 'mb-2']],
                    html: this.items[this.k].innerHTML,
                    createTo: `${this.container} .row-${i}`
                });
                this.k += 1;
            }
        }

        // Render sisa
        if (this.mod != 0) {
            for (let i = 0; i < this.mod; i++) {
                let htmlContent = this.items[this.k].innerHTML;
                createElement({
                    el: 'div',
                    attributes: [['class', 'mb-2']],
                    html: htmlContent,
                    createTo: `${this.container} .row-${i}`
                });
                this.k += 1;
            }
        }

        this.items.forEach(item => item.remove());
    }
}