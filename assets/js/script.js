// AutoDeal — public site helpers

document.addEventListener('DOMContentLoaded', function () {
    // Gallery: click a thumbnail to swap the main image
    const mainImg = document.getElementById('galleryMain');
    const thumbs = document.querySelectorAll('.gallery-thumbs img');
    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (!mainImg) return;
            mainImg.src = thumb.dataset.full || thumb.src;
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');
        });
    });

    // Mobile nav toggle
    const toggle = document.querySelector('.mobile-toggle');
    const links = document.querySelector('.nav-links');
    if (toggle && links) {
        toggle.addEventListener('click', function () {
            links.style.display = links.style.display === 'flex' ? 'none' : 'flex';
        });
    }
});
