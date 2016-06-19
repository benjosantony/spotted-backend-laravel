/**
 * Created by spiritbomb on 11/5/2015.
 */
var timerStart = false;
var time, delay, timeout;

onmessage = function(event){
    timerStart = event.data.start;
    time = event.data.time;
    delay = event.data.delay;
    if(timerStart)
        timeout = setInterval(myTimerZYA, delay);
    else clearTimeout(timeout);
};

function myTimerZYA(){
    var date = new Date();
    date.setTime(time);
    var strDate = "" + date.getFullYear() + " " + (date.getMonth() + 1) + " " + date.getDate() + " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
    postMessage({'time': strDate});
    time = date.getTime() + delay;
}