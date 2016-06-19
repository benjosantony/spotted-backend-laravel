/**
 * Created by Trung on 10/3/2015.
 */
var CSRF_TOKEN = "";
var admin_url = "";

onmessage = function(e){
    CSRF_TOKEN = e.data.token;
    admin_url = e.data.url;
    setTimeout(getGameRecordsBeforeAmountOfSeconds, 5000);
};

function getGameRecordsBeforeAmountOfSeconds(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            postMessage(xhttp.responseText);
        }
    }
    xhttp.open("POST", "../admin/gameStatistic/30");
    xhttp.send();
    setTimeout(getGameRecordsBeforeAmountOfSeconds, 30000);
}