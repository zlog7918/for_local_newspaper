func_view_article=(id, tab, tab_p)=>{};
func_edit_article=(id, tab, tab_p)=>{};
func_add_review=(id, tab, tab_p)=>{};
func_view_review=(id, tab, tab_p)=>{};
func_edit_review=(id, tab, tab_p)=>{};
(function() {
    let setup, view_article_funcs, create_tab;

    setup=()=>{
        let func_toggle_state=async (id, method, tab, tab_p)=>{
            let a_href=`/?p=${method}&article_nr=${id}`;
            await do_action(method, a_href).then(data=>{
                if(data['error']) {
                    // console.log(data);
                    display_err(data['message']);
                } else {
                    func_view_article(id, tab, tab_p);
                }
            }, err=>{
                display_err(err);
            });
        };

        view_article_funcs={
            'publish_article': (id, tab, tab_p)=>func_toggle_state(id, 'publish_article', tab, tab_p),
            'unpublish_article': (id, tab, tab_p)=>func_toggle_state(id, 'unpublish_article', tab, tab_p),
            'archive_article': (id, tab, tab_p)=>func_toggle_state(id, 'archive_article', tab, tab_p),
            'dearchive_article': (id, tab, tab_p)=>func_toggle_state(id, 'dearchive_article', tab, tab_p),
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
                            <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                                <button type="button" data-method="edit_article">&#x270E;</button>
                                <button type="button" data-method="add_review">&#xFF0B;</button>
                            </div>
                            <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                                ${!(data['data']['is_published'] || data['data']['is_archived']) ? '<button type="button" data-method="publish_article">&#x27a4;</button>':''}
                                ${data['data']['is_published'] ? '<button type="button" data-method="unpublish_article">&#x23CE;</button>':''}
                                ${!data['data']['is_archived'] ? '<button type="button" data-method="archive_article">&#x1F5D1;</button>':''}
                                ${data['data']['is_archived'] ? '<button type="button" data-method="dearchive_article">&#x1F5B9;</button>':''}
                            </div>
                        </div>
                        <textarea cols="80" rows="30" readonly>${data['data']['text']}</textarea>
                        <h5>Reviews:</h5>
                        <ul class="listing"></ul>
                    </div>
                `;
                [...new_tab_p.getElementsByTagName('button')].forEach(btn=>{
                    btn.addEventListener('click', ev=>{
                        if(btn.dataset.method in view_article_funcs) {
                            view_article_funcs[btn.dataset.method](id, tab, tab_p);
                            return;
                        }
                        if(btn.dataset.method==='edit_article')
                            func_edit_article(id, tab, tab_p);
                        else
                            func_add_review(id, tab, tab_p);
                    }, false);
                });
                let list=new_tab_p.getElementsByTagName('ul')[0];

                list.innerHTML='';
                await do_action('get_reviews', a_href).then(data=>{
                    if(data['error']) {
                        display_err(data['message']);
                    } else {
                        list_inner='';
                        data['data'].forEach(d=>{
                            list_inner+=`<li data-id="${d['id']}"><span class="a"><div><span class="title">${d['author']}</span><span class="subtitle">${d['degree']}</span></div></span></li>`;
                        });
                        list.innerHTML=list_inner;
                        [...list.getElementsByTagName('li')].forEach(li=>{
                            li.addEventListener('click', async ()=>{
                                func_view_review(li.dataset.id, tab, tab_p);
                            }, false);
                        });
                    }
                }, err=>{
                    display_err(err);
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

    func_edit_article=async (id, tab, tab_p)=>{
        let method_name='edit_article';
        let a_href=`/?p=${method_name}&article_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'Edit article', tab, tab_p, a_href);
        await do_action('view_article', a_href).then(data=>{
            if(data['error']) {
                // console.log(data);
                display_err(data['message']);
                new_tab.parentElement.removeChild(new_tab);
                new_tab_p.parentElement.removeChild(new_tab_p);
            } else {
                new_tab_p.innerHTML=`
                    <div style="display:flex;padding-bottom:5px;">
                        <div style="flex-grow:1;flex-shrink:0;">
                            <h3>${data['data']['title']}</h3>
                        </div>
                        <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                            <button type="button" data-method="edit_article">&#x27a4;</button>
                        </div>
                    </div>
                    <form method="post" style="display:flex;flex-direction:column;">
                        <input name="article_nr" type="hidden" value="${data['data']['id']}" required>
                        <textarea name="text" cols="80" rows="30" placeholder="Here goes your article..." required>${data['data']['text']}</textarea>
                    </form>
                `;
                let form=new_tab_p.getElementsByTagName('form')[0];
                form.addEventListener('submit',ev=>{ev.preventDefault();return false;}, false);
                [...new_tab_p.getElementsByTagName('button')].forEach(btn=>{
                    btn.addEventListener('click', async ev=>{
                        if(form.reportValidity())
                            await do_action(method_name, null, form).then(data=>{
                                if(data['error']) {
                                    display_err(data['message']);
                                } else {
                                    display_success('Successfully edited article');
                                    func_view_article(id, tab, tab_p);
                                    new_tab.parentElement.removeChild(new_tab);
                                    new_tab_p.parentElement.removeChild(new_tab_p);
                                }
                            }, err=>{
                                display_err(err);
                            });
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

    func_add_review=async (id, tab, tab_p)=>{
        let method_name='add_review';
        let a_href=`/?p=${method_name}&article_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'Add review', tab, tab_p, a_href);
        new_tab_p.innerHTML=`
            <form style="display:flex;flex-direction:column;justify-content:center;">
                <input name="article_nr" type="hidden" value="${id}">
                <input name="degree" type="number" min="1" max="5" placeholder="Degree" style="width:100%;" required>
                <textarea name="text" cols="80" rows="10" placeholder="Here goes your opinion about article..." required></textarea>
                <button>Create</button>
            </form>
        `;
        let form=new_tab_p.getElementsByTagName('form')[0];
        let not_busy=true;
        form.addEventListener('submit', async ev=>{
            ev.preventDefault();
            if(form.checkValidity())
                if(not_busy) {
                    not_busy=false;
                    try {
                        await do_action(method_name, null, form).then(data=>{
                            if(data['error']) {
                                // console.log(data);
                                display_err(data['message']);
                            } else {
                                // display_success();
                                display_success('Successfully added review');
                                func_view_review(data['id'], tab, tab_p);
                                new_tab.parentElement.removeChild(new_tab);
                                new_tab_p.parentElement.removeChild(new_tab_p);
                            }
                        }, err=>{
                            display_err(err);
                        });
                    } catch (e) {}
                    not_busy=true;
                }
            return false;
        }, false);
        change_to_tab(new_tab, new_tab_p);
        next_page_no_reload(a_href);
    };

    func_view_review=async (id, tab, tab_p)=>{
        let method_name='view_review';
        let a_href=`/?p=${method_name}&review_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'View review', tab, tab_p, a_href);
        await do_action(method_name, a_href).then(data=>{
            if(data['error']) {
                // console.log(data);
                display_err(data['message']);
                new_tab.parentElement.removeChild(new_tab);
                new_tab_p.parentElement.removeChild(new_tab_p);
            } else {
                // display_success();
                new_tab_p.innerHTML=`
                    <div style="display:flex;padding-bottom:5px;">
                        <div style="flex-grow:1;flex-shrink:0;">
                            <h3>${data['data']['degree']}: ${data['data']['article_title']}</h3>
                            <h5>${data['data']['author']}</h5>
                        </div>
                        <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                            <button type="button" data-method="edit_review">&#x270E;</button>
                            <button type="button" data-method="delete_review">&#x1F5D1;</button>
                        </div>
                    </div>
                    <textarea cols="80" rows="10" readonly>${data['data']['text']}</textarea>
                `;
                let a_id=data['data']['article_id'];
                [...new_tab_p.getElementsByTagName('button')].forEach(btn=>{
                    btn.addEventListener('click', async ev=>{
                        if(btn.dataset.method==='edit_review') {
                            func_edit_review(id, tab, tab_p);
                            return;
                        }
                        if(!confirm("Do you really want to delete this review?"))
                            return;
                        await do_action('delete_review', a_href).then(data=>{
                            if(data['error']) {
                                // console.log(data);
                                display_err(data['message']);
                            } else {
                                func_view_article(a_id, tab, tab_p);
                                new_tab.parentElement.removeChild(new_tab);
                                new_tab_p.parentElement.removeChild(new_tab_p);
                            }
                        }, err=>{
                            display_err(err);
                        });
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

    func_edit_review=async (id, tab, tab_p)=>{
        let method_name='edit_review';
        let a_href=`/?p=${method_name}&review_nr=${id}`;
        let new_tab=document.getElementById(`tab_${method_name}`);
        let new_tab_p=document.getElementById(`tab_${method_name}_p`);
        if(!new_tab)
            [new_tab, new_tab_p]=create_tab(method_name, 'Edit review', tab, tab_p, a_href);
        await do_action('view_review', a_href).then(data=>{
            if(data['error']) {
                // console.log(data);
                display_err(data['message']);
                new_tab.parentElement.removeChild(new_tab);
                new_tab_p.parentElement.removeChild(new_tab_p);
            } else {
                new_tab_p.innerHTML=`
                    <div style="display:flex;padding-bottom:5px;">
                        <div style="flex-grow:1;flex-shrink:0;">
                            <h3>${data['data']['article_title']}</h3>
                        </div>
                        <div style="display:flex;flex-grow:0;flex-shrink:0;flex-direction:column;justify-content:center;">
                            <button type="button" data-method="edit_review">&#x270E;</button>
                        </div>
                    </div>
                    <form method="post" style="display:flex;flex-direction:column;">
                        <input name="review_nr" type="hidden" value="${data['data']['id']}" required>
                        <input name="degree" type="number" min="1" max="5" value="${data['data']['degree']}" placeholder="Degree" style="width:100%;" required>
                        <textarea name="text" cols="80" rows="10" placeholder="Here goes your opinion about article..." required>${data['data']['text']}</textarea>
                    </form>
                `;
                let form=new_tab_p.getElementsByTagName('form')[0];
                form.addEventListener('submit',ev=>{ev.preventDefault();return false;}, false);
                [...new_tab_p.getElementsByTagName('button')].forEach(btn=>{
                    btn.addEventListener('click', async ev=>{
                        if(form.reportValidity())
                            await do_action(method_name, null, form).then(data=>{
                                if(data['error']) {
                                    display_err(data['message']);
                                } else {
                                    display_success('Successfully edited review');
                                    func_view_review(id, tab, tab_p);
                                    new_tab.parentElement.removeChild(new_tab);
                                    new_tab_p.parentElement.removeChild(new_tab_p);
                                }
                            }, err=>{
                                display_err(err);
                            });
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