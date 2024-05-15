// // console.log(scriptParams);
// (function() {
//     // let is_logged=!(u===undefined || u!=='y'),
//     let is_logged,
//         url=new URL(window.location),
//         header=document.getElementById('header'),
//         main=document.getElementById('main'),
//         footer=document.getElementById('footer');
//     // [...url.searchParams.keys()].forEach(key=>{url.searchParams.delete(key)})
//     let setup, fetch_page, fetch_page_frag, display_err, logging, page_reload, do_action, ajax_call;
//     setup=async ()=>{
//         // page_reload();
//     };

//     display_err=mess=>{
//         console.log(mess);
//         alert(mess.replace('<br>', "\n"));
//     }

//     fetch_page_frag=(frag, get)=>{
//         return new Promise(async (resolve, reject)=>{
//             await fetch_page(frag.id, get).then(data=>{
//                 frag.innerHTML=data;
//                 resolve(true);
//             }, err=>{
//                 display_err('Error in page fetching. Sorry for inconvinience try again later.<br>Error code: '+err.err_code);
//                 resolve(false);
//             });
//         });
//     };

//     ajax_call=(action_name, name, is_post_method, get, data)=>{
//         return new Promise((resolve, reject)=>{
//             let ajax_req=new XMLHttpRequest();
//             ajax_req.open(is_post_method ? "POST":"GET", `/?${action_name}=${name}${(get===undefined || get===null) ? '':'&'+get}`);
//             ajax_req.onreadystatechange=function() {
//                 if(this.readyState==XMLHttpRequest.DONE)
//                     if(this.status==200)
//                         resolve(ajax_req.responseText);
//                     else
//                         reject({'err_code':ajax_req.status, 'err_mess':ajax_req.responseText});
//             }
//             if(is_post_method)
//                 ajax_req.send(data);
//             else
//                 ajax_req.send();
//         });
//     }

//     fetch_page=(name, get)=>{
//         return ajax_call('get', name, false, get);
//     };

//     do_action=(name, get, data)=>{
//         let is_post_method=!(data===undefined || data===null);
//         return new Promise(async (resolve, reject)=>{
//             await ajax_call('do', name, is_post_method, get, data).then(ret_data=>{
//                 resolve(JSON.parse(ret_data));
//             }, err=>{
//                 err=`Error in sending request. Sorry for inconvinience try again later.<br>Error code: ${err.err_code}`;
//                 // display_err(err);
//                 reject(err);
//             });
//         });
//     };

//     page_reload=async ()=>{
//         let header_wait=fetch_page_frag(header).then((is_good)=>{
//             if(!is_good) {
//                 return is_logged=false;
//             }
//             let logout_btn=document.getElementById('logout_btn');
//             is_logged=logout_btn!==null;
//             if(is_logged) {
//                 logout_btn.addEventListener('click', ev=>{

//                 }, false);
//             } else {
//                 let login_btn=document.getElementById('login_btn');
//             }
//             return true;
//         });
//         // let main_wait=fetch_page_frag(main);
//         // header_wait=await header_wait;
//         if(await header_wait) {
//             // if(await main_wait) {
//                 if(is_logged) {
//                     // TODO
//                 } else {
//                     // TO BE changed (main fetches sth different)
//                     logging();
//                 }
//             // }
//         }
//     }

//     logging=async ()=>{
//         let main_wait=fetch_page_frag(main, 'do=log_in');
//         if(await main_wait) {
//             let form=document.getElementById('login_form');
//             let buttons=[...form.getElementsByTagName('button')];
//             buttons.forEach(btn=>{
//                 if(btn.type==='button')
//                     btn.addEventListener('click', async ev=>{
//                         await do_action('log_in', null, form).then(data);
                        
//                     }, false);
//             })
//             console.log(form);
//         }
//     }
    
//     setup();
// })();