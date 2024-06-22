(function() {
    let tab_name=':::tab_name:::', tab, tab_p;
    let setup, ch_pass;

    setup=()=>{
        tab=document.getElementById(`tab_${tab_name}`);
        tab_p=document.getElementById(`tab_${tab_name}_p`);
        tab.addEventListener('click', ()=>{change_to_tab(tab, tab_p);next_page_no_reload(`/?p=${tab_name}`);}, false);

        let form=tab_p.getElementsByTagName('form')[0];
        let not_busy=true;
        form.addEventListener('submit', async ev=>{
            ev.preventDefault();
            if(form.checkValidity())
                if(not_busy) {
                    not_busy=false;
                    try {
                        await ch_pass(form);
                    } catch (e) {}
                    not_busy=true;
                }
            return false;
        }, false);
    };

    ch_pass=async form=>{
        await do_action(tab_name, null, form).then(data=>{
            if(data['error']) {
                // console.log(data);
                display_err(data['message']);
            } else {
                display_success('Successfully changed password');
                [...tab_p.getElementsByTagName('input')].forEach(inp=>{
                    inp.value='';
                })
            }
        }, err=>{
            display_err(err);
        });
    }

    setup();
})();