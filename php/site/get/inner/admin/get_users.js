(function() {
    let tab_name=':::tab_name:::', tab, tab_p, list;
    let setup, reload, load;

    setup=()=>{
        tab=document.getElementById(`tab_${tab_name}`);
        tab_p=document.getElementById(`tab_${tab_name}_p`);
        list=tab_p.getElementsByTagName('ul')[0];
        tab.addEventListener('click', ()=>{change_to_tab(tab, tab_p);next_page_no_reload(`/?get=admin&p=${tab_name}`);}, false);

        let btn=tab_p.getElementsByTagName('button')[0];
        btn.addEventListener('click', ()=>{reload();}, false);

        load();
    };

    load=async ()=>{
        list.innerHTML='';
        await do_action(tab_name).then(data=>{
            if(data['error']) {
                display_err(data['message']);
            } else {
                list_inner='';
                data['data'].forEach(d=>{
                    list_inner+=`<li data-id="${d['id']}"><span class="a"><div><span class="title">${d['nick']}</span><span class="subtitle">${d['author']}</span></div></span></li>`;
                });
                list.innerHTML=list_inner;
                [...list.getElementsByTagName('li')].forEach(li=>{
                    li.addEventListener('click', async ()=>{
                        func_view_user(li.dataset.id, tab, tab_p);
                    }, false);
                });
            }
        }, err=>{
            display_err(err);
        });
    };

    reload=()=>{
        load();
    };

    setup();
})();