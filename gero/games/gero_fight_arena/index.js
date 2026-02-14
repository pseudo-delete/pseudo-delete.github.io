// after 25s, add the background image
setTimeout(() => {
    document.body.style.backgroundImage = 'url("img/Cosmic_Vastness.png")';
    document.body.style.backgroundSize = 'cover';
    document.body.style.backgroundPosition = 'center';
    document.body.style.backgroundRepeat = 'no-repeat';
}, 2500);// 25 seconds = 25000 milliseconds

// function loadBattlefield() {
//     document.body.style.backgroundImage = 'url("../img/arena/L1_Local_Arena_inside.png")';
//     document.body.style.backgroundSize = 'cover';
//     document.body.style.backgroundPosition = 'center';
//     document.body.style.backgroundRepeat = 'no-repeat';
// }

$(function (){
    $('#button-play').on('click', function () {
        const sound = $('#play-sound')[0]; // get native audio element
        sound.currentTime = 0;
        sound.play();
        window.open('battlefield/battle.html', '_self');
        // $('body').load('battlefield/battle.html');
        // loadBattlefield();
    });
});
