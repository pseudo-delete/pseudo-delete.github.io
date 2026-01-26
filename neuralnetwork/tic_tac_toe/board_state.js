$(function()
{
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
});