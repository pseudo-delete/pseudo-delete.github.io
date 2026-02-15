$(function()
{
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
});

