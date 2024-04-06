
const errorHandler = new ErrorHandler();

const phpData = document.getElementById('phpData');
const userIsAdmin = phpData.getAttribute('data-user-is-admin') === '1';
const userIsLoggedIn = phpData.getAttribute('data-user-is-logged-in') === '1';
let intArrayOfFavouriteGuns = JSON.parse(phpData.getAttribute('data-int-array-of-favourite-guns') || '[]');
const userLoggedInId = phpData.getAttribute('data-user-logged-in-id') !== 'null' ? phpData.getAttribute('data-user-logged-in-id') : null;

document.addEventListener('DOMContentLoaded', function () {
    updateData();

    if (userIsAdmin) {
        document.getElementById('createButtonForAdminWithJs').innerHTML = `<button id="addGunButton" class="fancy-add-btn">
        <i class="fas fa-plus"></i> Add Gun
         </button>`;
    }

    if (userIsAdmin) {
        document.getElementById('addGunButton').addEventListener('click', function () {
            window.location.href = '/createandeditweapon';
        });
    }

    document.getElementById('gunSearchInput').addEventListener('input', function () {
        const searchTerm = this.value;
        searchGuns(searchTerm);

    });

});

function createGunCard(gun, userIsAdmin, userIsLoggedIn, intArrayOfFavouriteGuns) {

    const isFavourite = intArrayOfFavouriteGuns.includes(gun.gunId);
    const starImageSrc = isFavourite ? "/images/elements/star.png" : "/images/elements/starv2.png";
    const starImageOnClick = userIsLoggedIn ? `toggleFavourite('${gun.gunId}', this)` : "window.location.href='/login'";
    const imagePath = DOMPurify.sanitize(gun.imagePath);
    const gunName = DOMPurify.sanitize(gun.gunName);
    const description = DOMPurify.sanitize(gun.description);
    const gunType = DOMPurify.sanitize(gun.gunType);
    const year = DOMPurify.sanitize(gun.year);
    const countryOfOrigin = DOMPurify.sanitize(gun.countryOfOrigin);
    const estimatedPrice = DOMPurify.sanitize(gun.estimatedPrice);
    const soundPath = DOMPurify.sanitize(gun.soundPath);

    return `
        <div id="gunId-data" class="card mb-3" gunId-data="${gun.gunId}">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="${imagePath}" class="img-fluid rounded-start" alt="Gun Image">
                </div>
                <div class="col-md-8">
                    <div class="card-body position-relative">
                        <h5 class="gun-title card-title">${gunName}</h5>
                        <p class="gun-description card-text">${description}</p>
                        <p class="card-text"><small class="text-muted">Type of gun:
                                ${gunType} | Year:
                                ${year} |
                                Country:
                                ${countryOfOrigin} | Price:~
                                ${estimatedPrice} $
                            </small></p>
                        ${userIsAdmin ? `<button onclick="deleteGun('${gun.gunId}')" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> Delete</button>` : ''}
                        ${userIsAdmin ? `<button  onclick="editGun('${gun.gunId}')" class="btn btn-primary btn-sm"><i class="bi bi-pencil-fill"></i> Edit</button>` : ''}
                        ${userIsAdmin ? '' : `<img src="${starImageSrc}" class="clickable img-button img-fluid" alt="Star Image" onclick="${starImageOnClick}">`}
                        <img src="/images/elements/speaker-filled-audio-tool.png" class="clickable img-button img-fluid" alt="Audio Image" onclick="playAudio('${gun.gunId}')">
                        <audio id="audioPlayer-${gun.gunId}" src="${soundPath}" class="audio-player"></audio>
                    </div>
                </div>
            </div>
        </div>`;
}

function addToFavourites(gunId) {
    fetch('/api/guns/addGunToFavourites', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ userId: userLoggedInId, gunId: gunId })
    }).then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
        }).catch((error) => {
            errorHandler.logError(error, 'addToFavourites', 'guns.js');
            errorHandler.showAlert('Could not add to favourites. Please try again later.');
        });
}

function removeFromFavourites(gunId) {
    fetch('/api/guns/removeGunFromFavourites', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ userId: userLoggedInId, gunId: gunId })
    }).then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
        }).catch((error) => {
            errorHandler.logError(error, 'removeFromFavourites', 'guns.js');
            errorHandler.showAlert('Could not remove from favourites. Please try again later.');
        });
}

function toggleFavourite(gunId, element) {
    const isFavourite = intArrayOfFavouriteGuns.includes(gunId);
    if (isFavourite) {
        removeFromFavourites(gunId);
        element.src = "/images/elements/starv2.png";
        intArrayOfFavouriteGuns = intArrayOfFavouriteGuns.filter(id => id !== gunId); // remove from local favorites array
    } else {
        addToFavourites(gunId);
        element.src = "/images/elements/star.png";
        intArrayOfFavouriteGuns.push(gunId);
    }
}

function updateData() {
    fetch(`/api/guns/displayGuns`)
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = '';

            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsAdmin, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'updateData', 'guns.js');
            errorHandler.showAlert('Could not update. Please try again later.');
        });
}

function editGun(gunId) {
    window.location.href = `/createandeditweapon?gunId=${gunId}`;
}

function deleteGun(gunId) {
    fetch('/api/guns/deleteGun', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ gunId: gunId })
    }).then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            updateData();

        }).catch((error) => {
            errorHandler.logError(error, 'deleteGun', 'guns.js');
            errorHandler.showAlert('Could not delete gun. Please try again later.');
        });
}

function playAudio(gunId) {
    var allAudio = document.getElementsByClassName('audio-player');
    for (var i = 0; i < allAudio.length; i++) {
        allAudio[i].pause();
        allAudio[i].currentTime = 0;
    }

    var audio = document.getElementById("audioPlayer-" + gunId);
    audio.play();
}

function searchGuns(searchTerm) {
    fetch(`/api/guns/displayGunsBasedOnSearchTerm?searchTerm=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = '';
            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsAdmin, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'searchGuns', 'guns.js');
            errorHandler.showAlert('Could not find gun. Please try again later.');
        });
}

function filterByType(type) {
    //if type is empty, get all guns.
    const endpoint = type ? `/api/guns/displayGunsBasedOnType?type=${encodeURIComponent(type)}` : `/api/guns/displayGuns`;
    fetch(endpoint)
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = ''; //this clears current guns

            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsAdmin, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'filterByType', 'guns.js');
            errorHandler.showAlert('Could not filter gun. Please try again later.');
        });
}
