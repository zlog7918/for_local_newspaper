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
                    'content'=>'
                        <div style="display:flex;flex-direction:column;">
                            <div style="display:flex;padding:5px;">
                                <div style="flex-grow:1;flex-shrink:1;"></div>
                                <button style="flex-grow:0;flex-shrink:0;">&#x27F3;</button>
                            </div>
                            <ul class="listing">
                            </ul>
                        </div>
                    ',
                    'script'=>'(function() {
                        let tab_name=\':::tab_name:::\', tab, tab_p, list;
                        let setup, load;

                        setup=()=>{
                            tab=document.getElementById(`tab_${tab_name}`);
                            tab_p=document.getElementById(`tab_${tab_name}_p`);
                            list=tab_p.getElementsByTagName(\'ul\')[0];
                            tab.addEventListener(\'click\', ()=>{change_to_tab(tab, tab_p);}, false);

                            let btn=tab_p.getElementsByTagName(\'button\')[0];
                            btn.addEventListener(\'click\', ()=>{reload();}, false);

                            load();
                        };

                        load=async ()=>{
                            // list.innerHTML+=\'<li><a href="#"><div><span class="title">Cpoś</span><span class="subtitle">copś</span></div></a></li>\'; // href="/p=view_article&article_nr="
                            await do_action(\'get_articles\').then(data=>{
                                list.innerHTML=\'\';
                                if(data[\'error\']) {
                                    // console.log(data);
                                    display_err(data[\'message\']);
                                } else {
                                    list_inner=\'\';
                                    // console.log(data);
                                    data[\'data\'].forEach(d=>{
                                        list_inner+=`<li><a href="/p=view_article&article_nr=${d[\'id\']}" data-id="${d[\'id\']}"><div><span class="title">${d[\'title\']}</span><span class="subtitle">${d[\'author\']}</span></div></a></li>`;
                                    });
                                    list.innerHTML=list_inner;
                                    let method_name=\'view_article\';
                                    [...list.getElementsByTagName(\'a\')].forEach(a=>{
                                        a.addEventListener(\'click\', async ev=>{
                                            ev.preventDefault();
                                            let new_tab=document.getElementById(`tab_${method_name}`);
                                            let new_tab_p=document.getElementById(`tab_${method_name}_p`);
                                            if(!new_tab) {
                                                new_tab=document.createElement(\'div\');
                                                new_tab.classList.add(\'tab\');
                                                new_tab.id=`tab_${method_name}`;
                                                new_tab.innerText=\'View article\';

                                                new_tab_p=document.createElement(\'div\');
                                                new_tab_p.classList.add(\'tab_p\');
                                                new_tab_p.id=`tab_${method_name}_p`;

                                                new_tab.addEventListener(\'click\', ()=>{change_to_tab(new_tab, new_tab_p);}, false);
                                                tab.parentElement.appendChild(new_tab);
                                                tab_p.parentElement.appendChild(new_tab_p);
                                            }
                                            await do_action(method_name, a.href).then(data=>{
                                                if(data[\'error\']) {
                                                    // console.log(data);
                                                    display_err(data[\'message\']);
                                                } else
                                                    // display_success();
                                                    new_tab_p.innerHTML=`
                                                        <h3>${data[\'data\'][\'title\']}</h3>
                                                        <h5><a href="#">${data[\'data\'][\'author\']}</a></h5>
                                                        <textarea cols="80" rows="30" readonly>${data[\'data\'][\'text\']}</textarea>
                                                    `;
                                                    change_to_tab(new_tab, new_tab_p);
                                                    next_page_no_reload(a.href);
                                            }, err=>{
                                                display_err(err);
                                            });

                                            return false;
                                        }, false);
                                    });
                                }
                            }, err=>{
                                list.innerHTML=\'\';
                                display_err(err);
                            });
                        }

                        reload=()=>{
                            load();
                        }

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
                    })();',
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
