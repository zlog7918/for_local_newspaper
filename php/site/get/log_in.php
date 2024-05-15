<header id="header">
    <?php require 'inner/header.php'; ?>
</header>
<main id="main">
    <div class="window">
        <!-- <div class="tabs">
            <div class="tab" data-href='/?p=get_articles'>View articles</div>
            <div class="tab" data-href='/?p=add_article'>Create article</div>
        </div> -->
        <div class="window_main">
            <form id="login_form" style="display:flex;flex-direction:column;" action="log_in" method="POST">
                <input name="nick" type="text" placeholder="nick">
                <input name="pass" type="password" placeholder="password">
                <button>Submit</button>
            </form>
        </div>
    </div>
    <script type="text/javascript" src="<?=file_and_last_edit('scripts/log_in.js')?>"></script>
</main>
<footer id="footer">
    <?php require 'inner/footer.php'; ?>
</footer>
