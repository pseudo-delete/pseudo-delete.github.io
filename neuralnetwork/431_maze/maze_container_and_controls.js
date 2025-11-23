$(function()
{
    let tileDiv = [];
    function establishPlatform(TileColumnsCount, TileRowsCount, width, height)
    {
        for(let x=0;x<TileColumnsCount;x++)
        {
            tileDiv[x] = [];
            for(let y=0;y<TileRowsCount;y++)
            {
                // create tile at (x,y) iteration by row
                $(".maze-container").css("width", (TileColumnsCount*width)+"px");
                $(".maze-container").css("height", (TileRowsCount*height)+"px");
                $(".maze-container").append("<div class='maze-tile' id='tile-"+x+"-"+y+"'></div>");

                tileDiv[x][y] = $("#tile-"+x+"-"+y+"");
                tileDiv[x][y].css("left", (x*50)+"px");
                tileDiv[x][y].css("top", (y*50)+"px");
                tileDiv[x][y].css("width", width+"px");
                tileDiv[x][y].css("height", height+"px");
            }
        }
    }

    establishPlatform(10,10, 50, 50);
});