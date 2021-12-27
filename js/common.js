// PAGE

var common = {

    init: function() {
        //elements to animate
        const logo = gc('logo_wrapper')[0]
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
        fade_in(logo);
        fade_in(button_play);
        fade_in(button_store);
        fade_in(button_install);
        fade_in(guy);
        fade_in(card_coin);
        fade_in(card_clocks);
        fade_in(card_percent);
        //set animating elements on visibility
        hourglass.style.opacity = 0;
        lupa.style.opacity = 0;
        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                fade_in(hourglass);
                fade_in(lupa);
            }
        }, { threshold:[0.1]});
        observer.observe(document.querySelector('.hourglass_lupa'));
        //method for fade in animation
        function fade_in(element) {
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
