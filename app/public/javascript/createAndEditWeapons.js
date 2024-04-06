const fileSize = 5000000; //5MB
const errorHandler = new ErrorHandler();

let isAdmin = document.getElementById('createGunForm').dataset.typeAdminBool === '1';
let checkIsGunPresent = document.getElementById('createGunForm').dataset.typeGunpresent === '1';
let gunId = document.getElementById('createGunForm').dataset.typeGunid;


document.getElementById('createGunForm').addEventListener('submit', function (event) {
    event.preventDefault(); // prevent the form from submitting in the traditional way
    if (checkIsGunPresent) {
        updateGun(isAdmin, gunId);
    } else {
        createNewGun(isAdmin);
    }

});

function createNewGun(isAdmin) {
    const formData = createGunFormData(true);
    fetch('/api/createandeditweapon/addGun', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            if (isAdmin) {
                window.location.href = '/guns';
            } else {
                window.location.href = '/favourite';
            }

        })
        .catch((error) => {
            errorHandler.logError(error, 'createNewGun', 'createAndEditWeapons.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function updateGun(isAdmin, gunId) {
    const formData = createGunFormData(false);
    formData.append('gunId', gunId);

    fetch('/api/createandeditweapon/editGun', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            if (isAdmin) {
                window.location.href = '/guns';
            } else {
                window.location.href = '/favourite';
            }

        })
        .catch((error) => {
            errorHandler.logError(error, 'updateGun', 'createAndEditWeapons.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function playSound() {
    var audio = document.getElementById("gunSoundAudio");
    if (audio.paused) {
        audio.play();
    } else {
        audio.currentTime = 0;
    }
}

function validateGunDetails(gunName, gunDescription, countryOfOrigin, year, estimatedPrice, typeOfGun, gunImage, gunSound) {
    if (!gunName || !gunDescription || !countryOfOrigin || !year || !estimatedPrice || !typeOfGun || !gunImage || !gunSound) {
        errorHandler.showAlert('Please fill all the fields');
        return false;
    }
    else if (gunImage.size > fileSize) {
        errorHandler.showAlert('Image size should be less than 5MB');
        return false;
    }
    else if (gunSound.size > fileSize) {
        errorHandler.showAlert('Sound size should be less than 5MB');
        return false;
    }
    else if (estimatedPrice < 0) {
        errorHandler.showAlert('Price should be greater than 0');
        return false;
    }
    else if (year <= 0 || isNaN(year) || !Number.isInteger(Number(year))) {
        errorHandler.showAlert('Year should be a positive integer');
        return false;
    }

    // If all validations pass
    return true;
}
function validateGunDetailsForEdit(gunName, gunDescription, countryOfOrigin, year, estimatedPrice, typeOfGun) {
    if (!gunName || !gunDescription || !countryOfOrigin || !year || !estimatedPrice || !typeOfGun) {
        errorHandler.showAlert('Please fill all the fields');
        return false;
    }
    else if (estimatedPrice < 0) {
        errorHandler.showAlert('Price should be greater than 0');
        return false;
    }
    else if (year <= 0 || isNaN(year) || !Number.isInteger(Number(year))) {
        errorHandler.showAlert('Year should be a positive integer');
        return false;
    }

    return true;
}

function createGunFormData(checkToPerform) {
    const gunName = document.getElementById('gunName').value;
    const gunDescription = document.getElementById('gunDescription').value;
    const countryOfOrigin = document.getElementById('gunCountry').value;
    const year = document.getElementById('gunYear').value;
    const estimatedPrice = document.getElementById('estimatedPrice').value;
    const typeOfGun = document.getElementById('gunType').value;
    const gunImage = document.getElementById('gunImage').files[0];
    const gunSound = document.getElementById('gunSound').files[0];


    if (checkToPerform) {
        if (!validateGunDetails(gunName, gunDescription, countryOfOrigin, year, estimatedPrice, typeOfGun, gunImage, gunSound)) {
            return;
        }
    }
    else {
        if (!validateGunDetailsForEdit(gunName, gunDescription, countryOfOrigin, year, estimatedPrice, typeOfGun)) {
            return;
        }
        else if (gunImage && gunImage.size > fileSize) {
            errorHandler.showAlert('Image size should be less than 5MB');
            return false;
        }
        else if (gunSound && gunSound.size > fileSize) {
            errorHandler.showAlert('Sound size should be less than 5MB');
            return false;
        }
    }

    const formData = new FormData();
    if (checkToPerform) {
        formData.append('userId', document.getElementById('createGunForm').dataset.typeUserid);
    }
    formData.append('gunName', gunName);
    formData.append('gunDescription', gunDescription);
    formData.append('countryOfOrigin', countryOfOrigin);
    formData.append('year', year);
    formData.append('estimatedPrice', estimatedPrice);
    formData.append('typeOfGun', typeOfGun);
    formData.append('gunImage', gunImage);
    formData.append('gunSound', gunSound);
    formData.append('showInGunsPage', isAdmin);

    return formData;
}