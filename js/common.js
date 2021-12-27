// PAGE

var common = {

    init: function() {
        //elements to animate
        // const logo = document.getElementsByClassName('logo_wrapper')[0];
        const logo = gc('logo_wrapper', document)[0]
        const guy = gc('guy')[0];
        const card_coin = gc('card card_coin')[0];
        const card_clocks = gc('card card_clocks')[0];
        const card_percent = gc('card card_percent')[0];
        const button_play = gc('download_button store google')[0];
        const button_store = gc('download_button store apple')[0];
        const button_install = gc('download_button desktop')[0];
        const hourglass = gc('hourglass')[0];
        const lupa = gc('lupa')[0];
        //animating elements on contentloaded
        fadeIn(logo);
        fadeIn(button_play);
        fadeIn(button_store);
        fadeIn(button_install);
        fadeIn(guy);
        fadeIn(card_coin);
        fadeIn(card_clocks);
        fadeIn(card_percent);
        //set animating elements on visibility
        hourglass.style.opacity = 0;
        lupa.style.opacity = 0;
        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                fadeIn(hourglass);
                fadeIn(lupa);
            }
        }, { threshold:[0.1]});
        observer.observe(document.querySelector('.hourglass_lupa'));
        //method for fade in animation
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
    }

}

add_event(document, 'DOMContentLoaded', common.init);
