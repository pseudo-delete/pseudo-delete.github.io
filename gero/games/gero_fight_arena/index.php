<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>

        <link rel="stylesheet" href="index.css"/>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <script src="index.js"></script>
        <script src="competitors.js"></script>
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
                <!-- <php include 'arena_bracket_variables_and_array.php';// Other bracket arrays here ?> -->
                <table id="arena-bracket">
                <?php

                    // Potential problem to be faced: Total players count is an even number. Potential solution: Have 1 losers bracket if this happens.

                    // include 'bracket_functions.php';

                    // loadBracketParticipants($Players, $BracketParticipantName, $AvatarChoice);
                    
                ?>
                </table>
            </div>

            <div class="play-summary-container">
                <button class="page-buttons" type="button" id="button-play"><!--a href="#"-->Play<!--/a--></button>
                <audio id="play-sound" src="sound/play.m4a" preload="auto"></audio>
                <label>Arena Competition: Local</label>
                <label>Next Battle: <span>Player 1</span>vs <span>Player 2</span></label>
                <div>
                    <button class="page-buttons" type="button"><a href="#">Hall of Fame</a></button>
                    <button class="page-buttons" type="button"><a href="#">Rankings</a></button><button class="page-buttons" type="button"><a href="#">Competitors</a></button>
                </div>
            </div>
        </div>
        <!-- <script>
            $('#button-play').on('click', function () {
                const sound = $('#play-sound')[0]; // get native audio element
                sound.currentTime = 0;
                sound.play();
            });
        </script> -->
    </body>
    <footer>
        <p>Copyright &copy; 2024 Gero. All rights reserved.</p>
    </footer>
</html>


