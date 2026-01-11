$(document).ready(function()
{
    // $('#add-competitor').on('click', function(){
    //     $(".add-competitor-popup").addClass(".popup");
    // });

    $('.avatar-button').on('click', function()
    {
        $('#selected-avatar-text').html($(this).text());
    });
});