export default class Sharing {
    constructor() {
        document.addEventListener('click', (event) => {
            const that = this;
            const target = event.target;

            if (target.matches('.share-link')) {
                event.preventDefault();

                const url = target.getAttribute('href');
                that.openShareWindow(url);
            }

            if (target.parentElement.matches('.share-link')) {
                event.preventDefault();

                const url = target.parentElement.getAttribute('href');
                that.openShareWindow(url);
            }
        }, true);
    }

    openShareWindow(url) {
        const popup = window.open(url, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=500,height=400');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const shareWindowElements = document.querySelectorAll('.share-link');

    if (shareWindowElements.length > 0) {
        new Sharing();
    }
});
