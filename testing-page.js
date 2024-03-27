// Set the variables
let currentQuestion = 1;
let questionID = document.getElementById(`question-${currentQuestion}`).dataset.questionId;
let score = 0;
let userChoice;
let correctQuestions = 0;

// Show the first question
document.getElementById('question-1').style.display = 'block';

// Add click event listeners to the answers and next question buttons
document.querySelectorAll('.question-container').forEach((questionContainer) => {
const answers = questionContainer.querySelectorAll('.answer');
answers.forEach((answer, index) => {
    answer.addEventListener('click', () => {
        // Remove the "selected" class from all answers
        answers.forEach((answer) => {
            answer.classList.remove('selected');
        });

        // Add the "selected" class to the clicked answer
        answer.classList.add('selected');
        // Store the number of the clicked answer
        userChoice = index + 1;

        // Display the "Next Question" button
        questionContainer.querySelector('.next-question').style.display = 'block';
    });
});

// Add click event listener to the "Next Question" button
const nextQuestionButton = questionContainer.querySelector('.next-question');
nextQuestionButton.addEventListener('click', () => {
    checkAnswerAndUpdateProgress();
});
});

// Set progress bar to 0% on question 1
document.querySelector('#question-1 .progress-bar i').style.width = '0%';

function checkAnswerAndUpdateProgress() {
var xhr = new XMLHttpRequest();
xhr.open("POST", "../../php/checkAnswer.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.send(`quizID=${quizID}&questionNumber=${currentQuestion}&userChoice=${userChoice}&questionID=${questionID}`);
xhr.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        // The server has responded
        var response = JSON.parse(this.responseText);

        // Check if the user's answer was correct
        if (response.correct) {
            score += 30;
            correctQuestions++;
        } else {
            score -= 10;
        }

        // Go to the next question
        if (currentQuestion < totalQuestions) {
            document.getElementById(`question-${currentQuestion}`).style.display = 'none';
            currentQuestion++;
            questionID = document.getElementById(`question-${currentQuestion}`).dataset.questionId;
            document.getElementById(`question-${currentQuestion}`).style.display = 'block';
            // Calculate the progress percentage for the progress bar
            const progressPercentage = ((currentQuestion - 1) / totalQuestions) * 100;
            // Set the width of the progress bar div to the progress percentage
            document.querySelector(`#question-${currentQuestion} .progress-bar i`).style.width = `${progressPercentage}%`;
        }
        else {
            displayResults();
        }
    }
};
}

function displayResults() {
//Display the results
let percentage = (correctQuestions / totalQuestions) * 100;
percentage = Math.round(percentage);
//document.getElementById(`question-${currentQuestion}`).style.display = 'none';
document.getElementById('complete-modal').style.display = 'flex';
document.getElementById('total-score').textContent += score;
document.getElementById('correct-answers').textContent += `${correctQuestions}/${totalQuestions}`;
document.getElementById('percentage').textContent += `${percentage}%`;
var xhr = new XMLHttpRequest();
xhr.open("POST", "../../php/updateCompletedQuiz.php", true);
xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
xhr.send(`studentQuizLinkID=${studentQuizLinkID}&completed=1&questionCount=${totalQuestions}&correctCount=${correctQuestions}&points=${score}`);
}

document.getElementById('show-results-button').addEventListener('click', () => {
// Hide the complete modal and show the results modal
document.getElementById('complete-modal').style.display = 'none';
document.getElementById('results-modal').style.display = 'flex';
});