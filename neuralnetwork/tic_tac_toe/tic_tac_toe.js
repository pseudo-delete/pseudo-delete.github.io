$(function() {
    // let board = [
    //     ["", "", ""],
    //     ["", "", ""],
    //     ["", "", ""]
    // ];
    let currentPlayer = ["X", "O"]; // X always starts first
    let currentPlayerIndex = 0;

    let boardState = [0,0,0,0,0,0,0,0,0]; // 0 = empty, 1 = X, -1 = O

    $(".cell").click(function() {
        
        $(this).text(currentPlayer[currentPlayerIndex]);
        
        if (currentPlayerIndex == 0)currentPlayerIndex = 1;
        else if(currentPlayerIndex == 1)currentPlayerIndex = 0;

        $("#move-count").text(parseInt($("#move-count").text()) + 1);

        $("#board-state").text(window.getBoardState());

        // Add logic for computer's move and checking for win/draw
    });
});