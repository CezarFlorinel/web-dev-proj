document.addEventListener('DOMContentLoaded', (event) => {
    carouselImages();
    document.querySelectorAll('.toggle-sign').forEach(item => {
        item.addEventListener('click', function () {
            const answer = this.parentElement.nextElementSibling;
            if (answer.style.display === 'none' || answer.style.display === '') {
                answer.style.display = 'block';
                this.src = 'images/elements/- sign.png';
                this.setAttribute('data-toggle', 'open');
            } else {
                answer.style.display = 'none';
                this.src = 'images/elements/+ sign.png';
                this.setAttribute('data-toggle', 'closed');
            }
        });
    });
});

function carouselImages() {

    const images = [
        "/images/elements/g-1.jpg",
        "/images/elements/g-2.png",
        "/images/elements/g-3.webp",
        "/images/elements/g-4.jpg"
    ];

    let currentImageIndex = 0;
    const carousel = document.getElementById('carousel');
    const carouselImage = carousel.querySelector('.homeImage');

    const updateImage = (index) => {
        carouselImage.src = images[index];
    };

    // initialize carousel with the first image
    updateImage(currentImageIndex);

    const nextImage = () => {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateImage(currentImageIndex);
    };

    // const prevImage = () => {
    //     currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    //     updateImage(currentImageIndex);
    // };

    // change image every 5 seconds
    setInterval(nextImage, 5000);

}

