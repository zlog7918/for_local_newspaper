(function() {
    let form;
    // let url=new URL(window.location);
    let setup, logging;
    setup=async ()=>{
        form=document.getElementById('login_form');
        let buttons=[...form.getElementsByTagName('button')];
        let not_busy=true;
        form.addEventListener('submit', async ev=>{
            ev.preventDefault();
            if(not_busy) {
                not_busy=false;
                try {
                    await logging();
                } catch (e) {}
                not_busy=true;
            }
            return false;
        }, false);
    };

    logging=async ()=>{
        await do_action('log_in', null, new FormData(form)).then(data=>{
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
