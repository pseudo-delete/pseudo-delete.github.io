$(function() {
    // let board = [
    //     ["", "", ""],
    //     ["", "", ""],
    //     ["", "", ""]
    // ];
    let currentPlayer = ["X", "O"]; // X always starts first
    let currentPlayerIndex = 0;

    let boardState = [0,0,0,0,0,0,0,0,0]; // 0 = empty, 1 = X, -1 = O

    function getBoardState()
    {
        for(let boardCellIndex = 0; boardCellIndex < 9; boardCellIndex++)
        {
            if($("#cell-" + boardCellIndex).text() == "X")
            {
                boardState[boardCellIndex] = 1;
            }
            else if($("#cell-" + boardCellIndex).text() == "O")
            {
                boardState[boardCellIndex] = -1;
            }
            else
            {
                boardState[boardCellIndex] = 0;
            }
        }

        return boardState;
    }

    function checkWin(board) {
        const winCombos = [
            [0,1,2], [3,4,5], [6,7,8], // rows
            [0,3,6], [1,4,7], [2,5,8], // columns
            [0,4,8], [2,4,6] // diagonals
        ];
        for (let combo of winCombos) {
            if (board[combo[0]] !== 0 && board[combo[0]] === board[combo[1]] && board[combo[1]] === board[combo[2]]) {
                return board[combo[0]] === 1 ? 'X' : 'O';
            }
        }
        if (board.every(cell => cell !== 0)) return 'draw';
        return null;
    }

    $(".cell").click(function() {
        
        $(this).text(currentPlayer[currentPlayerIndex]);
        
        if (currentPlayerIndex == 0)currentPlayerIndex = 1;
        else if(currentPlayerIndex == 1)currentPlayerIndex = 0;

        $("#move-count").text(parseInt($("#move-count").text()) + 1);

        let boardState2 = getBoardState();

        $("#board-state").text(boardState2);

        let winner = checkWin(boardState2);
        if (winner) {
            if (winner === 'draw') {
                alert('It\'s a draw!');
            } else {
                alert(winner + ' wins!');
            }
            // Optionally disable cells or reset the game
        }

        // Add logic for computer's move and checking for win/draw
    });
});