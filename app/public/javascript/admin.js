
const errorHandler = new ErrorHandler();
const imageSizeMax = 10085760; // 10MB

let deleteBtns = document.querySelectorAll('.delete-user-btn');

document.addEventListener('DOMContentLoaded', function () {
    updateData();
    updateModifications();

    document.getElementById('submitNewInfo').addEventListener('click', function () {
        addQandA();
    });

    document.getElementById('addQandABtn').addEventListener('click', function () {
        document.getElementById('addForm').classList.remove('d-none'); // show the add form
    });

});


function deleteModification(button) {
    const modificationCard = button.closest('.modification-card');
    const modificationId = modificationCard.getAttribute('data-id');

    if (confirm('Are you sure you want to delete this modification?')) {
        fetch('/api/admin/deleteModification', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ modificationId: modificationId })
        })
            .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
            .then(result => {
                if (!result.ok) {
                    throw new Error(result.body.message || 'Network response was not ok');
                }
                modificationCard.remove();
            })
            .catch(error => {
                errorHandler.logError(error, 'deleteModification', 'admin.js');
                errorHandler.showAlert('Error deleting modification. Please try again.');
            });
    }
}

function saveNewModification() {

    const card = document.getElementById('newModificationCard');
    const name = card.querySelector('.nameInput').value.trim();
    const description = card.querySelector('.descriptionInput').value.trim();
    const estimatedPrice = card.querySelector('.estimatedPriceInput').value.trim();
    const imageInput = card.querySelector('.imageInput');

    const validationResult = validateModification({ name, description, estimatedPrice, imageInput }, imageSizeMax);
    if (!validationResult.isValid) {
        errorHandler.showAlert(validationResult.message);
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('description', description);
    formData.append('estimatedPrice', estimatedPrice);
    formData.append('image', imageInput.files[0]);

    fetch('/api/admin/addModification', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            card.querySelector('.nameInput').value = '';
            card.querySelector('.descriptionInput').value = '';
            card.querySelector('.estimatedPriceInput').value = '';
            imageInput.value = '';
            updateModifications();
        })
        .catch(error => {
            errorHandler.logError(error, 'saveNewModification', 'admin.js');
            errorHandler.showAlert('Error saving modification. Please try again.');
        });

}

function updateModifications() {
    fetch('/api/admin/getModifications')
        .then(response => response.json())
        .then(modifications => {
            const modificationsContainer = document.getElementById('containerForModifications');
            modificationsContainer.innerHTML = '';

            modifications.forEach(modification => {
                const cardHTML = createModificationCard(modification);
                modificationsContainer.innerHTML += cardHTML;
            });
        })
        .catch(error => {
            errorHandler.logError(error, 'updateModifications', 'admin.js');
            errorHandler.showAlert('Error updating modification.');
        });
}

function createModificationCard(modification) {
    const formattedPrice = parseFloat(modification.estimatedPrice).toFixed(2); // Ensuring two decimal places
    const name = DOMPurify.sanitize(modification.name);
    const description = DOMPurify.sanitize(modification.description);
    const imagePath = DOMPurify.sanitize(modification.imagePath);

    return `
        <div class="modification-card p-3 border-bottom d-flex justify-content-between align-items-start" data-id="${modification.modificationId}">
            <div>
                <p>Name:</p>
                <p class="name h5 font-weight-bold mb-0" contenteditable="false">
                    ${name}
                </p>
                <br>
                <p>Description:</p>
                <p class="description mt-2" contenteditable="false">
                    ${description}
                </p>
                <p>Estimated Price:</p>
                <p class="estimatedPrice mt-2 d-block">
                    €${formattedPrice}
                </p>
                <input type="number" step="0.01" class="form-control mt-2 estimatedPriceInput d-none" value="${formattedPrice}">
                <p>Image:</p>
                <div class="row">
                    <div class="col-6 col-md-4 col-lg-3">
                        <img src="${imagePath}" class="img-fluid img-thumbnail" alt="Modification Image" onerror="this.onerror=null; this.src='defaultImagePath.jpg';">
                    </div>
                    <div class="col-6 col-md-8 col-lg-9 d-flex flex-column justify-content-end">
                        <input type="file" class="form-control mt-2" disabled>
                    </div>
                </div>
            </div>
            <div class="buttons-container d-flex align-items-start">
                <button class="edit-modification-btn btn btn-primary py-2 px-4 rounded hover:bg-opacity-75 transition" onclick="editModification(this)">Edit</button>
                <button class="delete-modification-btn btn btn-danger ml-2 py-2 px-4 rounded hover:bg-opacity-75 transition" onclick="deleteModification(this)">Delete</button>
            </div>
        </div>`;
}

function editModification(button) {
    const modificationCard = button.closest('.modification-card');
    const isEditing = button.textContent === 'Save';

    const name = modificationCard.querySelector('.name');
    const description = modificationCard.querySelector('.description');
    const estimatedPricePara = modificationCard.querySelector('.estimatedPrice');
    const estimatedPriceInput = modificationCard.querySelector('.estimatedPriceInput');
    const fileInput = modificationCard.querySelector('input[type="file"]');

    if (isEditing) {
        // Collect data to update
        const modificationId = modificationCard.getAttribute('data-id');
        const updatedName = name.innerText;
        const updatedDescription = description.innerText;
        const updatedEstimatedPrice = estimatedPriceInput.value;

        const validationResult = validateModificationEdit({ name: updatedName, description: updatedDescription, estimatedPrice: updatedEstimatedPrice, imageInput: fileInput });
        if (!validationResult.isValid) {
            errorHandler.showAlert(validationResult.message);
            return;
        }

        const formData = new FormData();
        formData.append('modificationId', modificationId);
        formData.append('name', updatedName);
        formData.append('description', updatedDescription);
        formData.append('estimatedPrice', updatedEstimatedPrice);

        if (fileInput.files[0]) {
            formData.append('image', fileInput.files[0]);
        }

        fetch('/api/admin/editModification', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
            .then(result => {
                if (!result.ok) {
                    throw new Error(result.body.message || 'Network response was not ok');
                }
                updateModifications();
            })
            .catch(error => {
                errorHandler.logError(error, 'editModification', 'admin.js');
                errorHandler.showAlert('Error editing modification. Please try again.');
            });

        // disable editing
        fileInput.disabled = true;
        name.contentEditable = false;
        description.contentEditable = false;
        estimatedPricePara.classList.remove('d-none');
        estimatedPriceInput.classList.add('d-none');
        button.textContent = 'Edit';

    } else {
        // enable editing
        fileInput.disabled = false;
        name.contentEditable = true;
        description.contentEditable = true;
        estimatedPricePara.classList.add('d-none');
        estimatedPriceInput.classList.remove('d-none');
        button.textContent = 'Save';
    }
}

deleteBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        if (confirm('Are you sure you want to delete this USER? Everything associated with this account will get deleted, even the GUNSS!!!')) { // Confirm deletion with the user
            const userId = btn.getAttribute('data-type-user-id');
            const data = {
                userId
            };

            fetch('/api/admin/deleteUser', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
                .then(result => {
                    if (!result.ok) {
                        throw new Error(result.body.message || 'Network response was not ok');
                    }
                    else if (result.ok) {
                        btn.parentElement.remove();
                    }
                })
                .catch(error => {
                    errorHandler.logError(error, 'deleteUser', 'admin.js');
                    errorHandler.showAlert('Error deleting user. Please try again.');
                });
        }
    });


});

function updateData() {
    fetch('/api/admin/getQandAs')
        .then(response => response.json())
        .then(questionAndAnswers => {
            const QandAContainer = document.getElementById('containerForQandAstorage');
            QandAContainer.innerHTML = '';

            questionAndAnswers.forEach(questionAndAnswer => {
                const cardHTML = createQandACard(questionAndAnswer);
                QandAContainer.innerHTML += cardHTML;

            });
        })
        .catch(error => {
            errorHandler.logError(error, 'updateData', 'admin.js');
            errorHandler.showAlert('Error updating Question and Answers. Please try again.');
        });
}

function createQandACard(QandA) {
    const question = DOMPurify.sanitize(QandA.question);
    const answer = DOMPurify.sanitize(QandA.answer);

    return `
        <div class="qanda-card p-3 border-bottom d-flex justify-content-between align-items-start" data-id="${QandA.questionAndAnswerId}">
            <div>
                <p>Q:</p>
                <p class="question h5 font-weight-bold mb-0" contenteditable="false">
                    ${question}
                </p>
                <p>A:</p>
                <p class="answer mt-2" contenteditable="false">
                    ${answer}
                </p>
            </div>
            <div class="buttons-container d-flex align-items-start">
                <button class="edit-QandA-btn btn btn-primary py-2 px-4 rounded hover:bg-opacity-75 transition" onclick="editQandA(this)">Edit</button>
                <button class="delete-QandA-btn btn btn-danger ml-2 py-2 px-4 rounded hover:bg-opacity-75 transition" onclick="deleteQandA(this)">Delete</button>
            </div>
        </div>`;
}

function editQandA(button) {
    const qandaCard = button.closest('.qanda-card');
    const id = qandaCard.getAttribute('data-id');
    const question = qandaCard.querySelector('.question');
    const answer = qandaCard.querySelector('.answer');

    // Convert text to editable fields
    question.contentEditable = true;
    answer.contentEditable = true;
    button.textContent = 'Save'; // Change the button text to 'Save'
    button.onclick = function () { saveQandA(id, question, answer, button); }; // Change the onclick function to save
}

function saveQandA(id, questionElement, answerElement, button) {
    const updatedQuestion = questionElement.innerText;
    const updatedAnswer = answerElement.innerText;

    if (!updatedQuestion.trim() || !updatedAnswer.trim()) {
        errorHandler.showAlert('Both fields are mandatory.');
        return;
    }

    fetch('/api/admin/editQandA', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id, question: updatedQuestion, answer: updatedAnswer })
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            questionElement.contentEditable = false;
            answerElement.contentEditable = false;
            button.textContent = 'Edit'; // Change the button text back to 'Edit'
            button.onclick = function () { editQandA(button); }; // Change the onclick function back to edit
        })
        .catch(error => {
            errorHandler.logError(error, 'saveQandA', 'admin.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function deleteQandA(button) {
    console.log('delete');
    const qandaCard = button.closest('.qanda-card');
    const id = qandaCard.getAttribute('data-id');

    if (confirm('Are you sure you want to delete this Q&A?')) {
        fetch('/api/admin/deleteQandA', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
            .then(result => {
                if (!result.ok) {
                    throw new Error(result.body.message || 'Network response was not ok');
                }
                qandaCard.remove();
            })
            .catch((error) => {
                errorHandler.logError(error, 'deleteQandA', 'admin.js');
                errorHandler.showAlert('Error. Please try again.');
            });
    }
}

function addQandA() {
    // Get the values from the form
    const question = document.getElementById('newQuestion').value;
    const answer = document.getElementById('newAnswer').value;

    // Validate input
    if (!question.trim() || !answer.trim()) {
        errorHandler.showAlert('Both fields are mandatory.');
        return;
    }

    fetch('/api/admin/addQandA', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ question: question, answer: answer })
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            document.getElementById('newQuestion').value = '';
            document.getElementById('newAnswer').value = '';
            document.getElementById('addForm').classList.add('d-none'); // Hide the form again
            updateData();
        })
        .catch((error) => {
            errorHandler.logError(error, 'addQandA', 'admin.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function validateModification({ name, description, estimatedPrice, imageInput }, imageSizeMax) {
    if (!name || !description || !estimatedPrice || !imageInput.files.length) {
        return { isValid: false, message: 'All fields are mandatory.' };
    }
    else if (isNaN(estimatedPrice) || parseFloat(estimatedPrice) < 0) {
        return { isValid: false, message: 'Estimated price must be a positive number.' };
    }
    else if (imageInput.files[0].size > imageSizeMax) {
        return { isValid: false, message: 'Image size must be less than 10MB.' };
    }

    return { isValid: true };
}

function validateModificationEdit({ name, description, estimatedPrice, imageInput }) {
    if (!name || !description || !estimatedPrice) {
        return { isValid: false, message: 'Name, description and price fields are mandatory.' };
    }
    else if (isNaN(estimatedPrice) || parseFloat(estimatedPrice) < 0) {
        return { isValid: false, message: 'Estimated price must be a positive number.' };
    }
    else if (imageInput.files[0] && imageInput.files[0].size > imageSizeMax) {
        return { isValid: false, message: 'Image size must be less than 10MB.' };
    }

    return { isValid: true };
}




