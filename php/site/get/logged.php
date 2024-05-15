<header id="header">
    <?php require 'inner/header.php'; ?>
</header>
<main id="main">
    <div class="window">
        <?php
            $tabs=[
                'get_articles'=>[
                    'tab_name'=>'View articles',
                    'selected'=>true,
                    'content'=>'',
                    'script'=>'(function() {
                        let tab_name=\':::tab_name:::\';
                        let tab=document.getElementById(`tab_${tab_name}`), tab_p=document.getElementById(`tab_${tab_name}_p`);
                        let setup;

                        setup=()=>{
                            tab.addEventListener(\'click\', ()=>{change_to_tab(tab, tab_p)}, false);
                        };

                        setup();
                    })();',
                ],
                'add_article'=>[
                    'tab_name'=>'Create article',
                    'content'=>'
                        <form style="display:flex;flex-direction:column;justify-content:center;">
                            <input name="title" type="text" placeholder="Title" style="width:100%;">
                            <textarea name="text" cols="80" rows="30" placeholder="Here goes your article..."></textarea>
                            <button>Create</button>
                        </form>
                    ',
                    'script'=>'(function() {
                        let tab_name=\':::tab_name:::\', tab, tab_p;
                        let setup, add;

                        setup=()=>{
                            tab=document.getElementById(`tab_${tab_name}`);
                            tab_p=document.getElementById(`tab_${tab_name}_p`);
                            tab.addEventListener(\'click\', ()=>{change_to_tab(tab, tab_p)}, false);
                            let form=tab_p.getElementsByTagName(\'form\')[0];
                            let not_busy=true;
                            form.addEventListener(\'submit\', async ev=>{
                                ev.preventDefault();
                                if(not_busy) {
                                    not_busy=false;
                                    try {
                                        await add(new FormData(form));
                                    } catch (e) {}
                                    not_busy=true;
                                }
                                return false;
                            }, false);
                        };

                        add=async data=>{
                            await do_action(tab_name, null, data).then(data=>{
                                if(data[\'error\']) {
                                    // console.log(data);
                                    display_err(data[\'message\']);
                                } else
                                    display_success(`Successfully added article (id: ${data[\'id\']})`);
                                    next_page(\'/\');
                                    // next_page(\'/p=view_article&article_nr=\');
                            }, err=>{
                                display_err(err);
                            });
                        };

                        setup();
                    })();'
                ],
            ];
        ?>
        <div class="tabs">
            <?php $was_not_selected=true; ?>
            <?php foreach ($tabs as $tab_name=>$tab_set): ?>
                <div id='tab_<?=$tab_name?>' class="tab<?=(isset($tab_set['selected']) && $tab_set['selected']===true)&$was_not_selected ? ' selected':''?>"><?=$tab_set['tab_name']?></div>
                <?php if(isset($tab_set['selected']) && $tab_set['selected']===true) $was_not_selected=false; ?>
            <?php endforeach ?>
            <div id='tab_:::tab_name:::' class="tab selected">:::tab_name:::</div>
        </div>
        <div class="window_main">
            <?php $was_not_selected=true; ?>
            <?php foreach ($tabs as $tab_name=>$tab_set): ?>
                <div id='tab_<?=$tab_name?>_p' class="tab_p<?=(isset($tab_set['selected']) && $tab_set['selected']===true)&$was_not_selected ? ' selected':''?>"><?=$tab_set['content']?></div>
                <script type="text/javascript"><?=isset($tab_set['script']) ? str_replace(':::tab_name:::', $tab_name, $tab_set['script']):''?></script>
                <?php if(isset($tab_set['selected']) && $tab_set['selected']===true) $was_not_selected=false; ?>
            <?php endforeach ?>
            <div id='tab_:::tab_name:::_p' class="tab_p selected">
                <div style="display:flex;flex-direction:column;">
                    <div style="display:flex;padding:5px;">
                        <div style="flex-grow:1;flex-shrink:1;"></div>
                        <button style="flex-grow:0;flex-shrink:0;">&#x27F3;</button>
                    </div>
                    <ul class="listing">
                    </ul>
                </div>
            </div>
            <script type="text/javascript">
                (function() {
                    let tab_name=':::tab_name:::', tab, tab_p, list;
                    let setup, load;

                    setup=()=>{
                        tab=document.getElementById(`tab_${tab_name}`);
                        // list=document.getElementById(`list_${tab_name}`);
                        tab_p=document.getElementById(`tab_${tab_name}_p`);
                        list=tab_p.getElementsByTagName('ul')[0];
                        tab.addEventListener('click', ()=>{change_to_tab(tab, tab_p);}, false);

                        // let btn=document.getElementById(`refresh_btn_${tab_name}`);
                        let btn=tab_p.getElementsByTagName('button')[0];
                        btn.addEventListener('click', ()=>{reload();}, false);

                        load();
                    };

                    load=async ()=>{
                        // list.innerHTML+='<li><a href="#"><div><span class="title">Cpoś</span><span class="subtitle">copś</span></div></a></li>'; // href="/p=view_article&article_nr="
                        await do_action('get_articles').then(data=>{
                            list.innerHTML='';
                            if(data['error']) {
                                // console.log(data);
                                display_err(data['message']);
                            } else {
                                data['data'].forEach(d=>{
                                    list_inner+=`<li><a href="#"><div><span class="title">${d['title']}</span><span class="subtitle">${d['author']}</span></div></a></li>`; // href="/p=view_article&article_nr="
                                });
                                list.innerHTML=list_inner;
                            }
                        }, err=>{
                            list.innerHTML='';
                            display_err(err);
                        });
                    }

                    reload=()=>{
                        load();
                    }

                    setup();
                })();
            </script>
        </div>
    </div>
    <!-- <script type="text/javascript" src="<?=file_and_last_edit('scripts/logged.js')?>"></script> -->
</main>
<footer id="footer">
    <?php require 'inner/footer.php'; ?>
</footer>
