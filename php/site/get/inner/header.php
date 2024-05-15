<h1>LocPaper</h1>
<div class="header_buttons">
    <?php if($_SESSION['user']->is_logged()): ?>
        <button type="button" onclick="do_action('log_out').then(data=>{if(data['error']) display_err(data['message']); else next_page('/');}, err=>{display_err(err);});">Log out</button>
        <?php if($_SESSION['user'] instanceof \Users\Admin): ?>
            <button type="button" onclick="window.location=this.dataset['href']" data-href='/?get=admin'>Admin</button>
            <!-- <a href="/?get=admin" class="button">Admin</a> -->
        <?php endif ?>
    <?php else: ?>
        <!-- <button type="button" onclick="window.location=this.dataset['href']" data-href='/?get=log_in'>Log in</button> -->
        <!-- <a href="/?get=log_in" class="button">Log in</a> -->
    <?php endif ?>
</div>