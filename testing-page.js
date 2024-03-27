class Quiz {
    // Constructor for the Quiz class
    constructor(totalQuestions, quizID, studentQuizLinkID) {
        this.currentQuestion = 1;
        this.questionID = document.getElementById(`question-${this.currentQuestion}`).dataset.questionId;
        this.score = 0;
        this.userChoice = null;
        this.correctQuestions = 0;
        this.totalQuestions = totalQuestions;
        this.quizID = quizID;
        this.studentQuizLinkID = studentQuizLinkID;
        this.init();
    }

    //Initialise the quiz
    init() {
        this.showQuestion(this.currentQuestion);
        this.addEventListeners();
        this.updateProgressBar(0);
    }

    // Show the question number
    showQuestion(questionNumber) {
        document.getElementById(`question-${questionNumber}`).style.display = 'block';
    }

    //Event listeners for when the user clicks an answer and clicks the next question button
    addEventListeners() {
        document.querySelectorAll('.question-container').forEach((questionContainer) => {
            const answers = questionContainer.querySelectorAll('.answer');
            answers.forEach((answer, index) => {
                answer.addEventListener('click', () => {
                    answers.forEach((answer) => {
                        answer.classList.remove('selected'); //Remove the selected class from the answer
                    });
                    answer.classList.add('selected'); //Set the user's answer choice as selected
                    this.userChoice = index + 1;
                    questionContainer.querySelector('.next-question').style.display = 'block';
                });
            });

            const nextQuestionButton = questionContainer.querySelector('.next-question');
            nextQuestionButton.addEventListener('click', () => {
                //Check the answer and update the progress when the next question button is clicked
                this.checkAnswerAndUpdateProgress();
            });
        });
        
        //Event listener for when the user clicks the show results button
        document.getElementById('show-results-button').addEventListener('click', () => {
            document.getElementById('complete-modal').style.display = 'none'; //Hide the complete modal after the user clicks the show results button
            document.getElementById('results-modal').style.display = 'flex'; //Show the results modal
        });
    }

    //Update the progress bar based on the current question
    updateProgressBar(progressPercentage) {
        document.querySelector(`#question-${this.currentQuestion} .progress-bar i`).style.width = `${progressPercentage}%`;
    }

    //Method to check the user's answer and update the progress
    checkAnswerAndUpdateProgress() {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../../php/checkAnswer.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.addEventListener('error', function() {
            console.error("An error occurred while making the AJAX request.");
        });
        //Send an AJAX request to check the user's answer
        xhr.send(`quizID=${this.quizID}&questionNumber=${this.currentQuestion}&userChoice=${this.userChoice}&questionID=${this.questionID}`);
        xhr.onreadystatechange = () => {
            if (xhr.readyState == 4 && xhr.status == 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                } catch (e) {
                    console.error("An error occured while parsing the JSON data: " + e.message);
                }
                //Check the users answer and update their score based off it
                if (response.correct) {
                    this.score += 30;
                    this.correctQuestions++;
                } else {
                    this.score -= 10;
                }
                //Update the score and the correct questions count
                if (this.currentQuestion < this.totalQuestions) {
                    document.getElementById(`question-${this.currentQuestion}`).style.display = 'none';
                    this.currentQuestion++;
                    this.questionID = document.getElementById(`question-${this.currentQuestion}`).dataset.questionId;
                    this.showQuestion(this.currentQuestion);
                    const progressPercentage = ((this.currentQuestion - 1) / this.totalQuestions) * 100;
                    this.updateProgressBar(progressPercentage);
                }
                else {
                    //After the last question then display the results
                    this.displayResults();
                }
            }
        };
    }

    //Method to display the results
    displayResults() {
        let percentage = (this.correctQuestions / this.totalQuestions) * 100;
        percentage = Math.round(percentage);
        document.getElementById('complete-modal').style.display = 'flex';
        document.getElementById('total-score').textContent += this.score;
        document.getElementById('correct-answers').textContent += `${this.correctQuestions}/${this.totalQuestions}`;
        document.getElementById('percentage').textContent += `${percentage}%`;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "../../php/updateCompletedQuiz.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`studentQuizLinkID=${this.studentQuizLinkID}&completed=1&questionCount=${this.totalQuestions}&correctCount=${this.correctQuestions}&points=${this.score}`);
    }
}

// Instantiate the Quiz class
const quiz = new Quiz(totalQuestions, quizID, studentQuizLinkID);