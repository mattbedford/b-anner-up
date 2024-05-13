
// Wait for page load before triggering script
document.addEventListener("DOMContentLoaded", function() {
    
    function openBannerUp() {
        scrollLock.disablePageScroll();
    }

    const closeBannerUp = document.getElementById('closeBannerUp');
    closeBannerUp.addEventListener('click', function() {
            scrollLock.enablePageScroll();
    });

    openBannerUp();
});

