document.addEventListener("DOMContentLoaded", function() {

    let BannerOnBanner = document.querySelector("#BannerOnBanner");
    let BannerOnCloseIcon = document.querySelector("#closeBannerOn");
    let BannerOnActionButton = document.querySelector("#BannerOnActionButton");
    let BannerOnForm = document.querySelector('#sec-question');

    // Banner close icon
    BannerOnCloseIcon.addEventListener('click', function(){
                BannerOnBanner.style.display = "none";
    });

    // Banner action button or form handler 
    BannerOnForm.addEventListener('submit', function(e){
        e.preventDefault();
        BannerOnActioned();
    });

    async function BannerOnActioned(){
        const question = document.querySelector('#sec-question-select').value;
        const answer= document.querySelector('#sec-question-answer').value;

        scrollLock.enablePageScroll();
        BannerOnBanner.style.display = "none";
        let BannerOnId = BannerOnBanner.dataset.id;

        const url = '/wp-json/banner-on/action-completed';
        const headers = {
            credentials: 'same-origin',
            'Content-Type': 'application/json',
            'X-WP-Nonce': banneron_object.rest_nonce,
        };
        const data = JSON.stringify({ id: BannerOnId, action: `SecurityQuestion`, question: question, answer: answer}); 
        fetch(url, { method: 'POST', headers, body: data });
    }

});