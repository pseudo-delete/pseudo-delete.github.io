$(function()
{
    // var timer = setInterval(function()
    // {

    // }, 1000);

    function goLobby()
    {
        window.location.href = '../';
    }

    $('body').css(
    {
        'background-image': 'url("../img/arena/L1_Local_Arena_inside.png")',
        'background-size': 'cover',
        'background-position': 'center',
        'background-repeat': 'no-repeat',
        'background-attachment': 'fixed'
    });

    $('#button-play').on('click', function() 
    {
        $('.intro-curtain').fadeOut(0, function()
        {
            $(this).remove();
        });
        $('.intro-curtain, .intro-curtain * ').css('z-index', '-1');
    });

    $('.attack-button').on('click', function()
    {
        const sound = $('#attack-sound')[0]; // get native audio element
        sound.currentTime = 0;
        sound.play();
    });

    $('#btn-attack').on('click', function()
    {
        newHealth = parseInt($('#player1-health').text()) - parseInt($('#player2-damage-points').text());
        newHealth2 = parseInt($('#player2-health').text()) - parseInt($('#player1-damage-points').text());
        $('#player1-health').text(newHealth2);
        $('#player2-health').text(newHealth);
        
        if (newHealth==0 && newHealth2==0)
        {
            alert('Draw!');
            goLobby();
        }
        else if (newHealth2 === 0)
        {
            alert('Player 1 wins!');
            goLobby();
        }
        else if (newHealth === 0)
        {
            alert('Player 2 wins!');
            goLobby();
        }

        $("#turn-number").text(parseInt($("#turn-number").text()) + 1);
    });
});

