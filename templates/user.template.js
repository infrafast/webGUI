var ICON_MIXER="!";

function userPong(jsonValue){           // THAT'S THE TWO MANDATORY FUNCTION USER HAS TO IMPLEMENT
    _l("userPong: "+JSON.stringify(jsonValue));
}

function userInit(){
    // call the user code
    postQuery({command:"userConfig", param:""});
}

////////////////////////////////////////////////////////////////////////////////////


function userInterfaceCfg(key,value){
    var node="";
    switch (key){
        case "abc":
            node = "<br>"+i18n.gettext(key)+`: <label class="switch"><input type="checkbox" cfg=${key} checked=${value}><div class="slider"></div></label>`;
        break;
        case "":
        
        break;
        case "":
        
        break;
        case "":
        
        break;
    }
    return node;
}