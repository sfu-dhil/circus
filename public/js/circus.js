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
