$(document).ready(function() {
    // let board = [
    //     ["", "", ""],
    //     ["", "", ""],
    //     ["", "", ""]
    // ];
    let currentPlayer = ["X", "O"]; // X always starts first
    let currentPlayerIndex = 0;

    $(".cell").click(function() {
        if (currentPlayerIndex == 0)
        {
            $(this).text("X");
            currentPlayerIndex = 1;
        }
        else if(currentPlayerIndex == 1)
        {
            $(this).text("O");
            currentPlayerIndex = 0;
        }

        // Add logic for computer's move and checking for win/draw
    });
});