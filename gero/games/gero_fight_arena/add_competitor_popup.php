<link rel="stylesheet" href="add_competitor_popup.css">
<div class="add-competitor-popup" class="close-popup">
    <?php
    $img_dir = 'img/character_avatar/';
    $images = glob($img_dir. '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    ?>

    <h1>Choose Avatar</h1>
    
    <div class='avatar-div'>
    <?php
        $AvatarId = 1;
        foreach($images as $image)
        {
            echo "
                <button class='avatar-button'><img class='avatar-img' src='$image' alt='avatar'><br>Avatar $AvatarId</button>
            ";
            $AvatarId++;
        }
    ?>
    </div>

    <div class='character-info'>
        <label id="selected-avatar-text"></label>
        <input type="text" placeholder="Name"></input>
    </div>

    <div class='add-competitor-popup-commands'>
        <button id="add-character-button">Save/Add Character</button>
    </div>
</div>