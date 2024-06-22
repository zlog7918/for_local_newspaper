(function() {
    let tab_name=':::tab_name:::', tab, tab_p, list;
    let setup, reload, load, func_view_article, create_tab;

    setup=()=>{
        tab=document.getElementById(`tab_${tab_name}`);
        tab_p=document.getElementById(`tab_${tab_name}_p`);
        list=tab_p.getElementsByTagName('ul')[0];
        tab.addEventListener('click', ()=>{change_to_tab(tab, tab_p);next_page_no_reload(`/?p=${tab_name}`);}, false);

        let btn=tab_p.getElementsByTagName('button')[0];
        btn.addEventListener('click', ()=>{reload();}, false);

        load();
    };

    load=async ()=>{
        list.innerHTML='';
        await do_action('get_articles').then(data=>{
            if(data['error']) {
                display_err(data['message']);
            } else {
                list_inner='';
                data['data'].forEach(d=>{
                    list_inner+=`<li data-id="${d['id']}"><span class="a"><div><span class="title">${d['title']}</span><span class="subtitle">${d['author']}</span></div></span></li>`;
                });
                list.innerHTML=list_inner;
                [...list.getElementsByTagName('li')].forEach(li=>{
                    li.addEventListener('click', async ()=>{
                        func_view_article(li.dataset.id, tab, tab_p);
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

    create_tab=(method, name, tab, tab_p, a_href)=>{
        let new_tab=document.createElement('div');
        new_tab.classList.add('tab');
        new_tab.id=`tab_${method}`;
        new_tab.innerText=name;

        let new_tab_p=document.createElement('div');
        new_tab_p.classList.add('tab_p');
        new_tab_p.id=`tab_${method}_p`;

        new_tab.addEventListener('click', ()=>{change_to_tab(new_tab, new_tab_p);next_page_no_reload(a_href);}, false);
        tab.parentElement.appendChild(new_tab);
        tab_p.parentElement.appendChild(new_tab_p);
        return [new_tab, new_tab_p];
    };

    func_view_article=async (id, tab, tab_p)=>{
        let method_name='view_article';
        let a_href=`/?p=${method_name}&article_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'View article', tab, tab_p, a_href);
        await do_action(method_name, a_href).then(async data=>{
            if(data['error']) {
                // console.log(data);
                display_err(data['message']);
                new_tab.parentElement.removeChild(new_tab);
                new_tab_p.parentElement.removeChild(new_tab_p);
            } else {
                // display_success();
                new_tab_p.innerHTML=`
                    <div style="display:flex;flex-direction:column;">
                        <div style="display:flex;padding-bottom:5px;">
                            <div style="flex-grow:1;flex-shrink:0;">
                                <h3>${data['data']['title']}</h3>
                                <h5>${data['data']['author']}</h5>
                            </div>
                        </div>
                        <textarea cols="80" rows="30" readonly>${data['data']['text']}</textarea>
                    </div>
                `;
                change_to_tab(new_tab, new_tab_p);
                next_page_no_reload(a_href);
            }
        }, err=>{
            display_err(err);
            new_tab.parentElement.removeChild(new_tab);
            new_tab_p.parentElement.removeChild(new_tab_p);
        });
    };

    setup();
})();