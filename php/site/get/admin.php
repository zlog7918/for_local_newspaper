<header id="header">
    <?php require 'inner/header.php'; ?>
</header>
<main id="main">
    <div class="window">
        <?php
            $tabs=[
                'get_users'=>[
                    'tab_name'=>'View users',
                    'selected'=>true,
                    'content'=>file_get_contents('get/inner/admin/get_users.html'),
                    'script'=>file_get_contents('get/inner/admin/get_users.js'),
                ],
                'create_user'=>[
                    'tab_name'=>'Create user',
                    'content'=>file_get_contents('get/inner/admin/create_user.html'),
                    'script'=>file_get_contents('get/inner/admin/create_user.js'),
                ],
            ];
        ?>
        <div class="tabs">
            <?php $was_not_selected=true; ?>
            <?php foreach ($tabs as $tab_name=>$tab_set): ?>
                <div id='tab_<?=$tab_name?>' class="tab<?=($was_not_selected && isset($tab_set['selected']) && $tab_set['selected']===true) ? ' selected':''?>"><?=$tab_set['tab_name']?></div>
                <?php if(isset($tab_set['selected']) && $tab_set['selected']===true) $was_not_selected=false; ?>
            <?php endforeach ?>
        </div>
        <script type="text/javascript"><?=file_get_contents('get/inner/admin/users_funcs.js')?></script>
        <div class="window_main">
            <?php $was_not_selected=true; ?>
            <?php foreach ($tabs as $tab_name=>$tab_set): ?>
                <div id='tab_<?=$tab_name?>_p' class="tab_p<?=($was_not_selected && isset($tab_set['selected']) && $tab_set['selected']===true) ? ' selected':''?>"><?=$tab_set['content']?></div>
                <script type="text/javascript"><?=isset($tab_set['script']) ? str_replace(':::tab_name:::', $tab_name, $tab_set['script']):''?></script>
                <?php if(isset($tab_set['selected']) && $tab_set['selected']===true) $was_not_selected=false; ?>
            <?php endforeach ?>
        </div>
    </div>
</main>
<footer id="footer">
    <?php require 'inner/footer.php'; ?>
</footer>
