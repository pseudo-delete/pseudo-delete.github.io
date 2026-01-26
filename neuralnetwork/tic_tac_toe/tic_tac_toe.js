import { getBoardState } from './board_state.js';
$(document).ready(function() {
    // let board = [
    //     ["", "", ""],
    //     ["", "", ""],
    //     ["", "", ""]
    // ];
    let currentPlayer = ["X", "O"]; // X always starts first
    let currentPlayerIndex = 0;

    $(".cell").click(function() {
        
        $(this).text(currentPlayer[currentPlayerIndex]);
        
        if (currentPlayerIndex == 0)currentPlayerIndex = 1;
        else if(currentPlayerIndex == 1)currentPlayerIndex = 0;

        $("#move-count").text(parseInt($("#move-count").text()) + 1);

        $("#board-state").text(getBoardState());

        // Add logic for computer's move and checking for win/draw
    });
});