<?php
    $Players = $_POST['Players'];
    $BracketParticipantName = isset($_POST['BracketParticipantName']) ? $_POST['BracketParticipantName'] : [];
    $AvatarChoice = isset($_POST['AvatarChoice']) ? $_POST['AvatarChoice'] : [];

    // This will be refreshed if #add-character-button is clicked, and the information will be retrieved from the database, which will be updated when a new character is added.
    // function refreshBracket($Players, $BracketParticipantName, $AvatarChoice)
    // {
    //     // Code to retrieve information from the database and update the bracket display
    //     loadBracketParticipants($Players, $BracketParticipantName, $AvatarChoice);
    // }

    // function loadBracketParticipants($Players, $BracketParticipantName, $AvatarChoice)
    // {
        // Code to load the bracket participants into the bracket display
        // for($BracketParticipant=1;$BracketParticipant<=$Players;$BracketParticipant*=2)
        // {
        //     echo "<tr>";
        //     for($Participant=1;$Participant<=$BracketParticipant;$Participant++)
        //     {
        //         if ($Participant > count($BracketParticipantName))
        //         {
        //             echo "<td class='td-bracket' id='td-bracket$Participant'>
        //                 <div class='bracket-participant'>
        //                     <img class='img-character-avatar' src='img/character_avatar/default.png' alt='avatar'>
        //                     <label>Empty</label>
        //                 </div>
        //             </td>";
        //             continue;
        //         }
        //         else
        //         {
        //             echo "<td class='td-bracket' id='td-bracket$Participant'>
        //                 <div class='bracket-participant'>
        //                     <img class='img-character-avatar' src='img/character_avatar/".$AvatarChoice[$Participant-1].".png' alt='avatar'>
        //                     <label>".$BracketParticipantName[$Participant-1]."</label>
        //                 </div>
        //             </td>";
        //         }
        //     }
        //     echo "</tr>";
        // }
        echo "<tr>";
        for($Participant=1;$Participant<=$Players;$Participant++)
        {
            if ($Participant > count($BracketParticipantName))
            {
                echo "<td class='td-bracket' id='td-bracket$Participant'>
                    <div class='bracket-participant'>
                        <img class='img-character-avatar' src='img/character_avatar/default.png' alt='avatar'>
                        <label>Empty</label>
                    </div>
                </td>";
                continue;
            }
            else
            {
                echo "<td class='td-bracket' id='td-bracket$Participant'>
                    <div class='bracket-participant'>
                        <img class='img-character-avatar' src='img/character_avatar/avatar".$AvatarChoice[$Participant-1].".png' alt='avatar'>
                        <label>".$BracketParticipantName[$Participant-1]."</label>
                    </div>
                </td>";
            }
        }
        echo "</tr>";
    // }
?>