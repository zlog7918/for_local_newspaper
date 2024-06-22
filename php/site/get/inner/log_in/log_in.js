(function() {
    let tab_name=':::tab_name:::', tab, tab_p;
    let setup, create_tab, logging;

    setup=()=>{
        tab=document.getElementById(`tab_${tab_name}`);
        tab_p=document.getElementById(`tab_${tab_name}_p`);
        tab.addEventListener('click', ()=>{change_to_tab(tab, tab_p);next_page_no_reload(`/?p=${tab_name}`);}, false);

        form=tab_p.getElementsByTagName('form')[0];
        let not_busy=true;
        form.addEventListener('submit', async ev=>{
            ev.preventDefault();
            if(form.checkValidity())
                if(not_busy) {
                    not_busy=false;
                    try {
                        await logging(form);
                    } catch (e) {}
                    not_busy=true;
                }
            return false;
        }, false);
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

    logging=async form=>{
        await do_action('log_in', null, form).then(data=>{
            if(data['error']) {
                // console.log(data);
                form['pass'].value='';
                display_err(data['message']);
            } else
                next_page('/');
        }, err=>{
            display_err(err);
        });
    };

    setup();
})();