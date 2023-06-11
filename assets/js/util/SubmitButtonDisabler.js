export default class SubmitButtonDisabler {
    constructor(buttonElement) {
        const that = this;

        buttonElement.addEventListener('click', (event) => {
            const button = event.target;
            const form = button.closest('form');

            if (form.checkValidity()) {
                if (button.dataset.disabledMessage) {
                    button.innerHTML = button.dataset.disabledMessage;
                }

                button.disabled = true;
                form.submit();
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const buttonList = document.querySelectorAll('button[type="submit"].disable-after-submit');

    buttonList.forEach((buttonElement) => {
        new SubmitButtonDisabler(buttonElement);
    });
});
