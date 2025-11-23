$(function()
{
    let tileDiv = [];
    function establishPlatform(TileColumnsCount, TileRowsCount, TileWidth, TileHeight)
    {
        
        $(".maze-container").css("width", (TileColumnsCount*TileWidth)+"px");
        $(".maze-container").css("height", ((TileRowsCount*TileHeight) + (TileHeight*2))+"px");

        for(let x=0;x<TileColumnsCount;x++)
        {
            tileDiv[x] = [];
            for(let y=0;y<=TileRowsCount+1;y++)// y starts at 1 to match tile id in order to put an allowance at the top for the exit area of the maze, also added +1 to the TileRowsCount for the entry area of the maze
            {
                if(y>0 && y < TileRowsCount+1)
                {
                    // create tile at (x,y) iteration by row
                    $(".maze-container").append("<div class='maze-tile' id='tile-"+x+"-"+y+"'></div>");

                    tileDiv[x][y] = $("#tile-"+x+"-"+y+"");
                    tileDiv[x][y].css("left", (x*50)+"px");
                    tileDiv[x][y].css("top", (y*50)+"px");
                    tileDiv[x][y].css("width", TileWidth+"px");
                    tileDiv[x][y].css("height", TileHeight+"px");
                }
            }
        }
    }

    establishPlatform(10,10, 50, 50);
});