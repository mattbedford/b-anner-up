
// Wait for page load before triggering script
document.addEventListener("DOMContentLoaded", function() {
    
    function openBannerUp() {
        scrollLock.disablePageScroll();
    }

    const closeBannerOn = document.getElementById('closeBannerUp');
    closeBannerOn.addEventListener('click', function() {
            scrollLock.enablePageScroll();
    });

    openBannerUp();
});

