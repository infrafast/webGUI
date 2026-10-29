var ICON_MIXER="!";

function userPong(jsonValue){           // THAT'S THE TWO MANDATORY FUNCTION USER HAS TO IMPLEMENT
    _l("userPong: "+JSON.stringify(jsonValue))
    processIncomingJson(JSON.stringify(jsonValue));   // testing of 
}

function userInit(){
    // call the user code
    //postQuery({command:"userConfig", param:""});
}

////////////////////////////////////////////////////////////////////////////////////

function userConfig(jsonValue){
    _l("userConfig()");
}

function userInterfaceCfg(key,value){
    var node="";
    switch (key){
        case "discovery":
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