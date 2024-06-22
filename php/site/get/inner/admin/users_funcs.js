func_view_user=(id, tab, tab_p)=>{};
(function() {
    let setup, view_user_funcs, create_tab;

    setup=()=>{
        let func_toggle_state=async (id, method, tab, tab_p)=>{
            let a_href=`/?p=${method}&user_nr=${id}`;
            await do_action(method, a_href).then(data=>{
                if(data['error']) {
                    // console.log(data);
                    display_err(data['message']);
                } else {
                    func_view_user(id, tab, tab_p);
                }
            }, err=>{
                display_err(err);
            });
        };

        view_user_funcs={
            'make_admin': (id, tab, tab_p)=>func_toggle_state(id, 'give_admin_rights', tab, tab_p),
            'deactivate_user': (id, tab, tab_p)=>func_toggle_state(id, 'deactivate_user', tab, tab_p),
            'make_std_user': (id, tab, tab_p)=>func_toggle_state(id, 'revoke_admin_rights', tab, tab_p),
            'activate_user': (id, tab, tab_p)=>func_toggle_state(id, 'activate_user', tab, tab_p),
        };
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

    func_view_user=async (id, tab, tab_p)=>{
        let method_name='view_user';
        let a_href=`/?get=admin&p=${method_name}&user_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'View user', tab, tab_p, a_href);
        await do_action(method_name, `/?p=${method_name}&user_nr=${id}`).then(async data=>{
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
                                <h3>${data['data']['nick']}</h3>
                                <h5>${data['data']['author']}</h5>
                            </div>
                            <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                                ${(!data['data']['is_admin']) && data['data']['is_active'] ? '<button type="button" data-method="make_admin">A</button><button type="button" data-method="deactivate_user">d</button>':''}
                                ${data['data']['is_admin'] ? '<button type="button" data-method="make_std_user">u</button>':''}
                                ${!data['data']['is_active'] ? '<button type="button" data-method="activate_user">a</button>':''}
                            </div>
                        </div>
                    </div>
                `;
                [...new_tab_p.getElementsByTagName('button')].forEach(btn=>{
                    btn.addEventListener('click', ev=>{
                        if(btn.dataset.method in view_user_funcs) {
                            view_user_funcs[btn.dataset.method](id, tab, tab_p);
                            return;
                        }
                    }, false);
                });
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