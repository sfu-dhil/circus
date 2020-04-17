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

const horses = document.querySelectorAll('.horses')[0];
horses.addEventListener('click', function(){
    let clicks = this.getAttribute('data-clicks');
    let count = clicks == null ? 0 : (clicks * 1) + 1;
    if (count == 4){
        horses.classList.add('egg');
        this.setAttribute('data-clicks', 0);
        this.addEventListener('transitionend', function(){
            this.classList.remove('egg');
        });
    } else {
        this.setAttribute('data-clicks', count);
    }
  });
    
