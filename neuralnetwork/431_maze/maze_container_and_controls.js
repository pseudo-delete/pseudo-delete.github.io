$(function()
{
    let tileDiv = [];
    function establishPlatform(TileColumnsCount, TileRowsCount)
    {
        for(let y=0;y<TileRowsCount;y++)
        {
            tileDiv[x] = [];
            for(let x=0;x<TileColumnsCount;x++)
            {
                // create tile at (x,y) iteration by row
                tileDiv[x][y] = $("<div class='maze-tile' id='tile-"+x+"-"+y+"'></div>");
                tileDiv.css("left", (x*32)+"px");
                tileDiv.css("top", (y*32)+"px");
                $(".maze-container").append(tileDiv);
            }
        }
    }

    establishPlatform(10,10);
});