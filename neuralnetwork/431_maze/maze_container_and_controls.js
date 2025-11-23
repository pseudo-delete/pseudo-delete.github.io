$(function()
{
    let tileDiv = [];
    function establishPlatform(TileColumnsCount, TileRowsCount)
    {
        for(let x=0;x<TileColumnsCount;x++)
        {
            tileDiv[x] = [];
            for(let y=0;y<TileRowsCount;y++)
            {
                // create tile at (x,y) iteration by row
                $(".maze-container").append("<div class='maze-tile' id='tile-"+x+"-"+y+"'></div>");
                tileDiv[x][y] = $("#tile-"+x+"-"+y+"");
                tileDiv[x][y].css("left", (x*32)+"px");
                tileDiv[x][y].css("top", (y*32)+"px");
            }
        }
    }

    establishPlatform(10,10);
});