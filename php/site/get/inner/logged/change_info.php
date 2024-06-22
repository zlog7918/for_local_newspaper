<?php
    ob_start();
?><form style="display:flex;flex-direction:column;">
    <input name="nick" type="text" placeholder="Nick" value="<?=$_SESSION['user']->get_nick()?>" required>
    <input name="name" type="text" placeholder="Name" value="<?=$_SESSION['user']->get_name()?>" required>
    <input name="surname" type="text" placeholder="Surname" value="<?=$_SESSION['user']->get_surname()?>" required>
    <input name="academic_titles" type="text" placeholder="Academic titles" value="<?=$_SESSION['user']->get_academic_titles()?>" required>
    <button>Submit</button>
</form><?php
    return ob_get_clean();
?>
