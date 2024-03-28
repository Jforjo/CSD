//Define the class
class CreateQuizModal {
    //Constructor to initialise the properties of the class
    constructor(modalId, buttonId, closeClass, formId, ajaxUrl, errorMessageId, errorModalId, successModalId, successButtonId, errorButtonId) {
        this.modal = document.getElementById(modalId);
        this.button = document.getElementById(buttonId);
        this.closeElement = document.getElementsByClassName(closeClass)[0];
        this.form = $(formId);
        this.ajaxUrl = ajaxUrl;
        this.errorMessageElement = document.getElementById(errorMessageId);
        this.errorModal = document.getElementById(errorModalId);
        this.successModal = document.getElementById(successModalId);
        this.successButton = document.getElementById(successButtonId);
        this.errorButton = document.getElementById(errorButtonId);
    }

    //Set up the event listeners
    init() {
        this.button.onclick = () => this.openModal();
        this.closeElement.onclick = () => this.closeModal();
        window.onclick = (event) => {
            if (event.target == this.modal) {
                this.closeModal();
            }
        }
        this.form.on("submit", (event) => this.submitForm(event));
        this.successButton.addEventListener('click', () => location.reload());
        this.errorButton.addEventListener('click', () => this.hideErrorModal());
    }

    //Method to open the modal
    openModal() {
        this.modal.style.display = "flex";
    }

    //Close the modal
    closeModal() {
        this.modal.style.display = "none";
    }

    //Hide modal
    hideErrorModal() {
        this.errorModal.style.display = 'none';
    }

    //Method to submit the form using AJAX
    submitForm(event) {
        event.preventDefault();
        $.ajax({
            url: this.ajaxUrl,
            type: "post",
            data: this.form.serialize(),
            success: (response) => this.handleResponse(response),
        });
    }

    //Method to handle the response from the server
    handleResponse(response) {
        var data = JSON.parse(response);
        if (data.error) {
            this.errorMessageElement.textContent = data.error;
            this.errorModal.style.display = 'flex';
        } else {
            this.closeModal();
            this.successModal.style.display = 'flex';
        }
    }
}

//Initialise the class
$(document).ready(function(){
    var createQuizModal = new CreateQuizModal(
        "createQuizModal",
        "createQuizButton",
        "close",
        "#createQuizForm",
        "../../php/studentAssignQuiz.php",
        'errorMessage',
        'errorModal',
        'successModal',
        'successButton',
        'errorButton'
    );
    createQuizModal.init();
});