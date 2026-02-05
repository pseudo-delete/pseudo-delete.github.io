$(function()
{
    // $("#button-insert").click(function()
    // {
    //     let name = $("#input-name").val();
    //     let age = $("#input-age").val();
    //     insertInformation(name, age);
    // });

    $('#button-insert').click(function () {
        // $.ajax({
        //     method: 'POST',
        //     url: '/insert-information',
        //     data: {
        //     name: $('#input-name').val(),
        //     age: $('#input-age').val()
        //     }
        // }).done(function (res) {
        //     console.log(res);
        // });

        $.post("/.netlify/functions/insert-information", { name: $('#input-name').val(),
            age: $('#input-age').val() })
  .done(response => console.log(response))
  .fail(err => console.error(err));
    });

    


});