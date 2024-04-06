
const errorHandler = new ErrorHandler();

const phpData = document.getElementById('phpData');
const userIsLoggedIn = phpData.getAttribute('data-user-is-logged-in') === '1';
let intArrayOfFavouriteGuns = JSON.parse(phpData.getAttribute('data-int-array-of-favourite-guns') || '[]');
const userLoggedInId = phpData.getAttribute('data-user-logged-in-id') !== 'null' ? phpData.getAttribute('data-user-logged-in-id') : null;

document.addEventListener('DOMContentLoaded', function () {
    updateData();

    document.getElementById('gunSearchInput').addEventListener('input', function () {
        const searchTerm = this.value;
        searchGuns(searchTerm);
    });

    document.getElementById('addGunButton').addEventListener('click', function () {
        window.location.href = '/createandeditweapon';
    });
});

function filterByType(type) {
    //if type is empty, get all guns.
    const endpoint = type ? `/api/favourite/displayGunsBasedOnType?type=${encodeURIComponent(type)}` : `/api/favourite/displayGuns`;
    fetch(endpoint)
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = ''; //this clears current guns

            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'filterByType', 'favourite.js');
            errorHandler.showAlert('Error. Please refresh the page.');
        });
}

function searchGuns(searchTerm) {
    fetch(`/api/favourite/displayGunsBasedOnSearchTerm?searchTerm=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = '';

            // Add filtered guns
            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'searchGuns', 'favourite.js');
            errorHandler.showAlert('Error. Please refresh the page.');
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

function editGun(gunId) {
    window.location.href = `/createandeditweapon?gunId=${gunId}`;
}

function deleteGun(gunId) {
    fetch('/api/favourite/deleteGun', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ gunId: gunId })
    })
        .then(response => response.json().then(data => ({ ok: response.ok, body: data })))
        .then(result => {
            if (!result.ok) {
                throw new Error(result.body.message || 'Network response was not ok');
            }
            updateData();

        }).catch((error) => {
            errorHandler.logError(error, 'deleteGun', 'favourite.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function updateData() {
    fetch('/api/favourite/displayGuns')
        .then(response => response.json())
        .then(guns => {
            const gunsContainer = document.querySelector('.guns_store');
            gunsContainer.innerHTML = '';

            guns.forEach(gun => {
                gunsContainer.innerHTML += createGunCard(gun, userIsLoggedIn, intArrayOfFavouriteGuns);
            });
        })
        .catch((error) => {
            errorHandler.logError(error, 'updateData', 'favourite.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

function createGunCard(gun, userIsLoggedIn, intArrayOfFavouriteGuns) {

    let gunUserIdInt = parseInt(gun.userId);
    let userIdInt = parseInt(userLoggedInId);
    const isFavourite = intArrayOfFavouriteGuns.includes(gun.gunId);
    const isGunMadeByUser = userIsLoggedIn && gunUserIdInt === userIdInt;

    // only proceed to create the card if the gun is a favourite or made by the user
    if (isFavourite || isGunMadeByUser) {
        const starImageSrc = isFavourite ? "/images/elements/star.png" : "/images/elements/starv2.png";
        const starImageOnClick = userIsLoggedIn ? `toggleFavourite('${gun.gunId}', this)` : "window.location.href='/login'";
        const imagePath = DOMPurify.sanitize(gun.imagePath);
        const description = DOMPurify.sanitize(gun.description);
        const gunName = DOMPurify.sanitize(gun.gunName);
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
                            ${isGunMadeByUser ? `<button onclick="deleteGun('${gun.gunId}')" class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i> Delete</button>` : ''}
                            ${isGunMadeByUser ? `<button onclick="editGun('${gun.gunId}')" class="btn btn-primary btn-sm"><i class="bi bi-pencil-fill"></i> Edit</button>` : ''}
                            ${isGunMadeByUser ? '' : `<img src="${starImageSrc}" class="clickable img-button img-fluid" alt="Star Image" onclick="${starImageOnClick}">`}
                             <img src="/images/elements/speaker-filled-audio-tool.png" class="clickable img-button img-fluid" alt="Audio Image" onclick="playAudio('${gun.gunId}')">
                            <audio id="audioPlayer-${gun.gunId}" src="${soundPath}" class="audio-player"></audio>
                        </div>
                    </div >
                </div >
            </div >`;
    } else {
        // return an empty string if the gun is neither a favourite nor made by the user
        return '';
    }
}

function toggleFavourite(gunId, element) {
    removeFromFavourites(gunId);
    gunId = parseInt(gunId);
    element.src = "/images/elements/starv2.png";
    intArrayOfFavouriteGuns = intArrayOfFavouriteGuns.filter(id => id !== gunId); //remove gun from array
}

function removeFromFavourites(gunId) {
    fetch('/api/favourite/removeGunFromFavourites', {
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
            updateData();
        }).catch((error) => {
            errorHandler.logError(error, 'removeFromFavourites', 'favourite.js');
            errorHandler.showAlert('Error. Please try again.');
        });
}

