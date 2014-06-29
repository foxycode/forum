function filtr()
{
    location.href='?view='+document.getElementById('view').options[document.getElementById('view').selectedIndex].value+'&hash='+document.getElementById('hash').value;
    return false;
}

function gE(obj) {
    var object = document.getElementById(obj);
    return object;
};

HTTPRequest = function(strURL, callBackFunction, method) {
    // Mozilla/Safari
    if (typeof(XMLHttpRequest) != 'undefined') {
        var XHR = new XMLHttpRequest();
    }
    else {
        try {
            /*- IE */
            var XHR = new ActiveXObject('Msxml2.XMLHTTP'); /*- "Microsoft.XMLHTTP*/
        }
        catch (e) {
            try {
                var XHR = new ActiveXObject('Microsoft.XMLHTTP');
            }
            catch (e) {
                return 0;
            }
        }
    }
    XHR.open(method, strURL, true);
    XHR.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    XHR.onreadystatechange = onreadystatechangeFunction;
    function onreadystatechangeFunction() {
        if (XHR.readyState == 4) {
            if (XHR.status == 200) {
                // odchyceni chybky, kdyby se vratil nejaky maglajz
                try {
                    if(typeof callBackFunction == 'string') {
                        eval(callBackFunction + '(' + XHR.responseText + ')');
                    } else if (typeof callBackFunction == 'function') {
                        eval('var data =' + XHR.responseText);
                        callBackFunction(data);
                    }

                }
                catch (e) {
                    alert("Chyba aplikace.\n"+e);
                }
            }
        }
    }
    if(method == 'POST')
        XHR.send(arguments[3]);
    else
        XHR.send(null);
};