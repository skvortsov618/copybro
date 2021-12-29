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
        common.fade_in(logo);
        common.fade_in(button_play);
        common.fade_in(button_store);
        common.fade_in(button_install);
        common.fade_in(guy);
        common.fade_in(card_coin);
        common.fade_in(card_clocks);
        common.fade_in(card_percent);
        //set animating elements on visibility
        const observer = new IntersectionObserver(function(entries) {
            if(entries[0].isIntersecting === true) {
                common.fade_in(hourglass);
                common.fade_in(lupa);
            }
        }, { threshold:[0.4]});
        observer.observe(document.querySelector('.hourglass_lupa'));
    },
    //method for animation
    fade_in: async function(el) {
        el.classList.add("fade_in")
    }

}

// add_event(document, 'DOMContentLoaded', common.init);
add_event(window, 'load', common.init);
