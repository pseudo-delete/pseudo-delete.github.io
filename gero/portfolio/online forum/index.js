$(document).ready(function()
{
    var postId = 1;

    $("#btn-post").on('click', function()
    {
        $(".posts-container").append("<div id='post-card-container" + postId + "' class='post-card-container'>");

        $("#post-card-container" + postId).append("<p>" + $("#input-post").text() + "</p>");

        // html template
        /*
            <!-- Template -->
            <div class="post-card-container">
                <div>
                    <p>Lorem ipsum</p>
                </div>

                <div>
                    <label><span id="like-count">0</span> Likes</label>
                </div>

                <div>
                    <button type="button">Like</button>
                </div>
            </div>
            <!-- End of Template-->
        */
    });
});