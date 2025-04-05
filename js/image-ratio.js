document.addEventListener('DOMContentLoaded', function() {
    function checkImageRatio(wrapper, img) {
        const ratio = img.naturalWidth / img.naturalHeight;
        if (ratio >= 0.8 && ratio <= 1.2) {
            wrapper.classList.add('is-square');
        }
    }

    document.querySelectorAll('.card-image-wrapper').forEach(wrapper => {
        const img = wrapper.querySelector('.card-image');
        if (img.complete) {
            checkImageRatio(wrapper, img);
        } else {
            img.addEventListener('load', function() {
                checkImageRatio(wrapper, img);
            });
            // Fallback на случай ошибки загрузки
            img.addEventListener('error', function() {
                wrapper.classList.remove('is-square');
            });
        }
    });
});