$(document).ready(function()
{
    
    let postId = 0;
    let postCount = 0;

    if(postCount==0)
    {
        $("#no-post-label").show();
    }

    function createComment(pId, cId)
    {
        pId=pId-1;
        let commentText = $('<p>',{
            text: $('#input-comment' + pId).val()
        });

        let likeCommentButton = $('<button>', {
            text: 'Like'
        });// add this later if this is possible, too complicated for now

        let singleCommentCon = $('<div>', {
            class: 'div-single-comment-container',
            id: 'div-single-comment-container'  + pId + "-" + cId
        });

        singleCommentCon.append(commentText);

        $('#div-comments-area' + pId).append(singleCommentCon);

        if(cId>0)
        {
            $("#no-comment-label" + pId).hide();
        }

        $('#input-comment' + pId).val("");
    }

    function createPost(id, textInput)
    {
        let postText = $('<p>', {
            class: 'p-text',
            text: textInput
        });

        let likeCount = 0;
        let likeCountDisplay = $('<label>', {
            id: 'label-like-count' + id,
            text: likeCount + ' likes'
        });

        let divLikecount = $('<div>',{
            class: 'div-like-count'
        });// commands container, class="div-like-count"

        divLikecount.append(likeCountDisplay);

        likeButtonStat = 0
        let likeButton = $('<button>', {
            class: 'like-btn',
            id: 'like-btn' + id,
            text: 'Like'
        }).on('click', function(){
            if(likeButtonStat==0)// Like Button
            {
                likeCount += 1;
                likeButtonStat = 1;
                $(this).text('Unlike');
            }
            else if(likeButtonStat==1)// Unlike Button, meaning the like was pressed
            {
                likeCount -= 1;
                likeButtonStat = 0;
                $(this).text('Like');
            }
            $('#label-like-count' + id).text(likeCount + ' likes');
        });

        let deleteButton = $('<button>', {
            class: 'delete-btn',
            id: 'delete-btn' + id,
            text: 'Delete'
        }).on('click', function(){
            if (confirm('Are you sure you want to delete this post?')) {
                $(this).closest('.div-post-container').remove();
            }
        });

        let divCommands = $('<div>',{
            class: 'div-commands'
        });// commands container, class="div-commands"

        divCommands.append(likeButton, deleteButton);

        let commentsTitle = $('<h4>',{
            class: 'comments-title',
            text: 'Comments'
        });

        let noCommentLabel = $('<label>', {
            id: 'no-comment-label' + postId,
            text: 'No Comment Yet'
        });

        let divCommentsArea = $('<div>', {
            class: 'div-comments-area',
            id: 'div-comments-area' + postId
        });

        divCommentsArea.append(noCommentLabel);

        let inputComment = $('<input>', {
            placeholder: 'Type your comment here...',
            class: 'input-comment',
            id: 'input-comment' + postId
        });

        let commentId = 0;
        let sendButton = $('<button>', {
            class: 'comment-btn',
            id: 'comment-btn' + id,
            text: 'Comment'
        }).on('click', function(){
            commentId+=1;
            createComment(postId, commentId);
        });

        let divCommentControl = $('<div>', {
            class: 'div-comment-control'
        });

        divCommentControl.append(inputComment, sendButton);

        let divComSec = $('<div>', {
            class: 'div-comment-section'
        });

        divComSec.append(commentsTitle, divCommentsArea, divCommentControl);

        let divPostCon = $('<div>',{
            class: 'div-post-container',
            id: 'div-post-container' + postId
        });// post container, class="div-post-container"

        divPostCon.append(postText, divLikecount, divCommands, divComSec);

        $(".div-post-outer-container").append(divPostCon);

        postCount+=1;

        if(postCount>=1)
        {
            $("#no-post-label").hide();
        }
    }// end of function createPost(id, textInput)

    $("#post-button").on('click', function()
    {
        createPost(postId, $("#post-input").val());
        $("#post-input").val("");
        postId++;
    });
});