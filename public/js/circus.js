(function ($, window, document) {
    $(document).ready(function () {
        /*
        * Basic Javascript for Circus site
        *
        *
        */

        const cards =  document.querySelectorAll('.card');
        cards.forEach(function(card){
            card.addEventListener('click', function(e){
                window.location.href = card.getAttribute('data-path');
            })
        });

        const cardLinks = document.querySelectorAll('.card a');
        cardLinks.forEach(function(link){
            link.addEventListener('click', function(event){
                event.cancelBubble = true;
                event.stopPropogation();
            })
        });


        const burgers = document.querySelectorAll('.hamburger');
        burgers.forEach(function(burg){
            burg.addEventListener('click', function(){
                burg.classList.toggle('is-active');
            })
        });




        /* ssshhh this is a secret */

        const horses = document.querySelectorAll('.horses');
        console.log('horses', horses)
        horses.forEach(function(horse){
            let clicks = 0;
            horse.addEventListener('click', function() {
                clicks++;
                if ((clicks % 5) == 0){
                    horse.classList.add('egg');
                    this.addEventListener('transitionend', function(){
                        this.classList.remove('egg');
                    });
                }
            });
        });
    })
})(jQuery, window, document);