import { getBoardState } from "./board_state.js";
import { initializeNewNeuralState } from "./neural_net.js";
$(function() {
    let currentPlayer = ["X", "O"]; // X always starts first
    let currentPlayerIndex = 0;

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

        $(".board-state-history").append("<div class='board-state-entry'><label id='board-state-" + $("#move-count").text() +"'>" + boardState2 + "</label></div>");

        // Check if boardstate is not in the record
        initializeNewNeuralState();
        // If not, add it to the record with initialized random neural net values then
        // repeatedly train/update if the best move is already occupied/marked in the board

        let winner = checkWin(boardState2);
        if (winner) {
            if (winner === 'draw') {
                alert('It\'s a draw!');
            } else {
                alert(winner + ' wins!');
            }
            // Optionally disable cells or reset the game
        }

        /* Learning Phase */
        // Add logic for computer's move and checking for win/draw
        // Learn/training occurs when: 
        // If X, which is the player/human, wins over O, which is the computer/AI:
    });
});