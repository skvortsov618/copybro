// PAGE

var common = {

    init: function() {
        const logo = document.getElementsByClassName('logo-wrapper')[0];
        const guy = document.getElementsByClassName('guy')[0];
        const cardCoin = document.getElementsByClassName('card card_coin')[0];
        const cardClocks = document.getElementsByClassName('card card_clocks')[0];
        const cardPercent = document.getElementsByClassName('card card_percent')[0];
        const buttonPlay = document.getElementsByClassName('download-button store google')[0];
        const buttonStore = document.getElementsByClassName('download-button store apple')[0];
        const buttonInstall = document.getElementsByClassName('download-button desktop')[0];
        const hourglass = document.getElementsByClassName('hourglass')[0];
        const lupa = document.getElementsByClassName('lupa')[0];

        fadeIn(logo);
        fadeIn(buttonPlay);
        fadeIn(buttonStore);
        fadeIn(buttonInstall);
        fadeIn(guy);
        fadeIn(cardCoin);
        fadeIn(cardClocks);
        fadeIn(cardPercent);

        function fadeIn(element) {
            let id = null
            let opacity = 0
            element.style.opacity = opacity;
            clearInterval(id)
            id = setInterval(frame,100);
            function frame() {
                if (opacity == 1){
                    clearInterval(id);
                } else {
                    opacity += 0.2;
                    element.style.opacity = opacity;
                }
            }
        }
        
        hourglass.style.opacity - 0;
        lupa.style.opacity - 0;
        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                fadeIn(hourglass);
                fadeIn(lupa);
            }
        }, { threshold:[0.1]});
        observer.observe(document.querySelector('.hourglass-lupa'));
    }

}

add_event(document, 'DOMContentLoaded', common.init);
