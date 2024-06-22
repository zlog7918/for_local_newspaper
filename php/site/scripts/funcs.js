function get_url(href) {
    let url=new URL(window.location);
    if(href==='.') {
        url.href=url.origin+url.pathname;
        return url;
    }
    if(href==='/') {
        url.href=url.origin;
        return url;
    }
    if(href.at(0)==='/') {
        url.href=url.origin+href;
        return url;
    }
    if(href.at(0)==='.') {
        url.href=`${url.origin}${url.pathname}${href.substr(1)}`;
        return url;
    }
    url.href=href
    return url;
}

function next_page(href) {
    if(href==='.')
        window.location.href=window.location.pathname+window.location.search;
    else
        window.location.href=get_url(href).href;
}

function next_page_no_reload(href) {
    history.pushState({}, "", get_url(href).href);
}

function ajax_call(action_name, name, is_post_method, get, data) {
    let is_get=(get!==undefined && get!==null);
    if(is_get) {
        if(get.at(0)==='/')
            get=get.substr(1);
        if(get.at(0)==='?')
            get=get.substr(1);
    }
    return new Promise((resolve, reject)=>{
        let ajax_req=new XMLHttpRequest();
        ajax_req.open(is_post_method ? "POST":"GET", `/?${action_name}=${name}${is_get ? `&${get}`:''}`);
        ajax_req.onreadystatechange=function() {
            if(this.readyState==XMLHttpRequest.DONE)
                if(this.status==200)
                    resolve(ajax_req.responseText);
                else
                    reject({'err_code':ajax_req.status, 'err_mess':ajax_req.responseText});
        }
        if(is_post_method)
            ajax_req.send(data);
        else
            ajax_req.send();
    });
}

// function fetch_page(name, get) {
//     return ajax_call('get', name, false, get);
// }

function do_action(name, get, data) {
    let is_post_method=(data!==undefined && data!==null);
    data=(is_post_method && data instanceof HTMLFormElement) ? (new FormData(data)):data;
    return new Promise(async (resolve, reject)=>{
        ajax_call('do', name, is_post_method, get, data).then(ret_data=>{
        	try {
            	resolve(JSON.parse(ret_data));
            } catch(e) {
                console.log(ret_data);
            	reject(e)
            }
        }, err=>{
            err=`Error in sending request. Sorry for inconvinience try again later.<br>Error code: ${err.err_code}`;
            // display_err(err);
            reject(err);
        });
    });
}

function display_err(mess) {
    console.log(mess=`${mess}`);
    alert(mess.replace('<br>', "\n"));
}

function display_success(mess) {
    mess=`${mess}`;
    // console.log(mess);
    alert(mess.replace('<br>', "\n"));
}

function change_to_tab(tab, tab_p, selected_class='selected') {
    if(tab.classList.contains(selected_class)) {
        if(!tab_p.classList.contains(selected_class))
            tab_p.classList.add(selected_class);
        return;
    }
    [...tab.parentElement.children].forEach(sel=>{sel.classList.remove(selected_class)})
    tab.classList.add(selected_class);
    [...tab_p.parentElement.children].forEach(sel=>{sel.classList.remove(selected_class)})
    tab_p.classList.add(selected_class);
}