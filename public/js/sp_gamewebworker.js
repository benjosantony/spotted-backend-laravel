/**
 * Created by Trung on 10/3/2015.
 */
var CSRF_TOKEN = "";
var last_id = 0;
var admin_url = "";

onmessage = function(e){
    last_id = e.data.lastId;
    CSRF_TOKEN = e.data.token;
    admin_url = e.data.url;
    setTimeout(getAllNewGamesFromLastGame, 10000);
};

function getAllNewGamesFromLastGame(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            var result = JSON.parse(xhttp.responseText);
            if(result.total > 0) {
                last_id = result.lastId;
                postMessage({total: result.total, data: result.data});
            } else {
                postMessage({total: 0});
            }
        }
    }
    xhttp.open("POST", admin_url + "getGameFromId/" + last_id);
    xhttp.send();
    setTimeout(getAllNewGamesFromLastGame, 10000);
}