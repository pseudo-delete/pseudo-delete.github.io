import { insertInformation } from "./add_information";

$(function()
{
    $("#button-insert").click(function()
    {
        let name = $("#input-name").val();
        let age = $("#input-age").val();
        insertInformation(name, age);
    });
});