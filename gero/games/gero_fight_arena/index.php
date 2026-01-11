<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="index.css"/>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
</head>
<body>
    <div class="intro-curtain">
        <h1>FIGHT CYCLE</h1>
    </div>
    <!--BGM-->
    <audio autoplay loop class="hidden-audio">
        <source src="music/main.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <audio autoplay class="hidden-audio">
        <source src="sound/welcome_to_cycle_1.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <!--End code for BGM-->

    <h1> FIGHT CYCLE </h1>
    <h1 id="cycle">CYCLE 1</h1>
 
    <!-- <div class="hall-of-fame-container">
        <h2><a href="#">Hall of Fame</a></h2>
        <table>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div> -->

    <?php include 'rankings.html';?>

    <?php include 'competitors.php';?>
    
    <div class="page-display">
        <div class="arena-background-container">
            <!-- <img id="arena-background" title="arena" src="img/arena/L1_Local_Arena_inside.png"/> -->
        </div>
        
        <div class="bracket-container">
            <!--Bracket designing-->
            <table id="arena-bracket">
            <?php
                $Players = 8;

                // Potential problem to be faced: Total players count is an even number. Potential solution: Have 1 losers bracket if this happens.

                for($BracketParticipant=1;$BracketParticipant<=$Players;$BracketParticipant*=2)
                {
                    echo "<tr>";
                    for($Participant=1;$Participant<=$BracketParticipant;$Participant++)
                    {
                        echo "<td id='td-bracket'></td>";
                    }
                    echo "</tr>";
                }
            ?>
            </table>
        </div>

        <div class="play-summary-container">
            <button class="page-buttons" type="button"><a href="#">Play</a></button>
            <label>Arena Competition: Local</label>
            <label>Next Battle: <span>Player 1</span>vs <span>Player 2</span></label>
            <div>
                <button class="page-buttons" type="button"><a href="#">Hall of Fame</a></button>
                <button class="page-buttons" type="button"><a href="#">Rankings</a></button><button class="page-buttons" type="button"><a href="#">Competitors</a></button>
            </div>
        </div>
    </div>

    <script src="index.js"></script>
    <script src="competitors.js"></script>
</body>
</html>