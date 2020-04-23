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
let clicks = 0;
horses.addEventListener('click', function(){
    clicks++;
    if ((clicks % 5) == 0){
        horses.classList.add('egg');
        this.addEventListener('transitionend', function(){
            this.classList.remove('egg');
        });
    }
  });


$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
