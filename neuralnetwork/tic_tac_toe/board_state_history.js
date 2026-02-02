import { getBoardState } from "./functions.js";

$(".cell").click(function() 
{
    $(".board-state-history").append("<div class='board-state-entry'><label id='board-state-" + $("#move-count").text() +"'>" + getBoardState() + "</label></div>");
});