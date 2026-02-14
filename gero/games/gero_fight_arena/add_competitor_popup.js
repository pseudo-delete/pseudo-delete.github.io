$(function(){

    let players = 2;// Players count, which will be updated when a new character is added. This will be used to determine the number of rows and columns in the bracket display.
    let bracketParticipantName = [];// Container of Players Information for Display Corresponding to Avatar Choice
    let avatarChoice = [];// Container of Players' Avatar Choice for Display Corresponding to Avatar Choice

    function RefreshBracket(Players, BracketParticipantName, AvatarChoice) {
        LoadBracket(Players, BracketParticipantName, AvatarChoice);
    }

    function LoadBracket(Players, BracketParticipantName, AvatarChoice) {
        $.ajax({
            url: 'bracket_functions.php',
            type: 'POST',
            data: {
                Players: players,
                BracketParticipantName: bracketParticipantName,
                AvatarChoice: avatarChoice
            },
            success: function(response) {
                // change this into a passing data equivalent to the refreshBracket function in bracket_functions.php, we must have js version of it so both server side and client side could do both actions wherever the data is
                $("#arena-bracket").empty(); // Clear the existing bracket content
                $("#arena-bracket").html(response);
            },
            error: function(xhr, status, error) {
                // Handle any errors that occur during the AJAX request
                console.error(error);
            }
        });
    }

    LoadBracket(players, bracketParticipantName, avatarChoice);

    $('.avatar-button').on('click', function()
    {
        $('#selected-avatar-text').html($(this).text());
    });

    $('#add-character-button').on('click', function(){
        // for loop checking if bracket slot is occupied or vacant, then the next player will be appointed on the vacant slot next to the occupied slots.
        let selectedAvatar = $('#selected-avatar-text').text().slice(-1); // Get the last character of the text, which should be the avatar number
        let playerName = $('#selected-avatar-name').val();

        bracketParticipantName.push(playerName);
        avatarChoice.push(selectedAvatar);

        // Send data to index.php using AJAX
        RefreshBracket(players, bracketParticipantName, avatarChoice);
        // $.ajax({
        //     url: 'bracket_functions.php',
        //     type: 'POST',
        //     data: {
        //         Players: Players,
        //         BracketParticipantName: BracketParticipantName,
        //         AvatarChoice: AvatarChoice,
        //     },
        //     success: function(response) {
        //         // change this into a passing data equivalent to the refreshBracket function in bracket_functions.php, we must have js version of it so both server side and client side could do both actions wherever the data is
        //         $("#arena-bracket").empty(); // Clear the existing bracket content
        //         $("#arena-bracket").html(response);
        //     },
        //     error: function(xhr, status, error) {
        //         // Handle any errors that occur during the AJAX request
        //         console.error(error);
        //     }
        // });
    });

    function refreshBracket() {
        // This function should be implemented to update the bracket display after adding a new competitor.
        // It can make an AJAX request to fetch the updated bracket data and then update the DOM accordingly.
    }
});