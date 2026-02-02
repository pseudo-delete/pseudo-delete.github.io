import { getBoardState } from "./functions.js";

let boardState = [0,0,0,0,0,0,0,0,0]; // 0 = empty, 1 = X, -1 = O
$(".cell").click(function() 
{
    $(".board-state-history").append("<div class='board-state-entry'><label id='board-state-" + $("#move-count").text() +"'>" + getBoardState() + "</label></div>");
});