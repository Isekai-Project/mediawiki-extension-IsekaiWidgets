function register(namespace, func) {
    let nsList = namespace.split('.');

    if(!('isekai' in window)){
        window.isekai = {};
    }

    let obj = window.isekai;
    for(var i = 0; i < nsList.length - 1; i ++){
        let ns = nsList[i];
        if(!(ns in obj)){
            obj[ns] = {};
        }
        obj = obj[ns];
    }
    obj[nsList[i]] = func;
}

module.exports = register;