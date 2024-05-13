document.addEventListener("DOMContentLoaded", function() {

    let BannerUpBanner = document.querySelector("#BannerUpBanner");
    let BannerUpCloseIcon = document.querySelector("#closeBannerUp");
    let BannerUpActionButton = document.querySelector("#BannerUpActionButton");
    let BannerUpForm = document.querySelector('#sec-question');

    // Banner close icon
    BannerUpCloseIcon.addEventListener('click', function(){
                BannerUpBanner.style.display = "none";
    });

    // Banner action button or form handler 
    BannerUpForm.addEventListener('submit', function(e){
        e.preventDefault();
        BannerUpActioned();
    });

    async function BannerUpActioned(){
        const question = document.querySelector('#sec-question-select').value;
        const answer= document.querySelector('#sec-question-answer').value;

        scrollLock.enablePageScroll();
        BannerUpBanner.style.display = "none";
        let BannerUpId = BannerUpBanner.dataset.id;

        const url = '/wp-json/banner-up/action-completed';
        const headers = {
            credentials: 'same-origin',
            'Content-Type': 'application/json',
            'X-WP-Nonce': bannerup_object.rest_nonce,
        };
        const data = JSON.stringify({ id: BannerUpId, action: `SecurityQuestion`, question: question, answer: answer}); 
        fetch(url, { method: 'POST', headers, body: data });
    }

});