/**
 * Created by Trung on 9/24/2015.
 */
var CSRF_TOKEN = $('meta[name="csrf-token"]').prop('content');
var tbl_main = null;
var tbl_sub = null;
var backupRows = [];
var main_loading = $("#content-loading-screen");
var user_worker;
var game_record_worker;
var timer_worker;
var game_statistic_worker;
var data = [], totalPoints = 100, maxGameNumber = 100;
var interactive_plot;

while (data.length < totalPoints) {
    data.push(0);
}

$("body").resize(function() {
    $('.main-sidebar').css('height', $(this).height());
});

function initialInteractivePlot(value) {
    interactive_plot = $.plot("#interactive", [getNumberOfGameFromServer(value)], {
        grid: {
            borderColor: "#f3f3f3",
            borderWidth: 1,
            tickColor: "#f3f3f3"
        },
        series: {
            shadowSize: 0, // Drawing is faster without shadows
            color: "#3c8dbc"
        },
        lines: {
            fill: true, //Converts the line chart to area chart
            color: "#3c8dbc"
        },
        yaxis: {
            min: 0,
            max: maxGameNumber,
            show: true
        },
        xaxis: {
            show: false
        }
    });
}

function startUserWorker(lastUserId) {
    if(typeof(Worker) !== "undefined") {
        if(typeof(user_worker) == "undefined") {
            user_worker = new Worker("/js/sp_userwebworker.js");
        }
        user_worker.postMessage({lastId: lastUserId, token: CSRF_TOKEN, url: adminURL});
        user_worker.onmessage = function(event) {
            var total = event.data.total;
            if(total > 0) {
                var data = event.data.data;
                $.each(data, function(index, value){
                    var gender = "";
                    var userStatus = "";
                    var bankStatus = "";
                    var fullname = data[index].fullname == null ? "" : data[index].fullname;
                    var picture = data[index].picture == null ? "" : data[index].picture;
                    var fbEmail = data[index].fbEmail == null ? "" : data[index].fbEmail;
                    var phone = data[index].phone == null ? "" : data[index].phone;
                    var bankName = data[index].bankName == null ? "" : data[index].bankName;
                    var bankAccount = data[index].bankAccount == null ? "" : data[index].bankAccount;
                    if(data[index].gender == 1) {
                        gender = "Male";
                    } else {
                        gender = "Female";
                    }
                    if(data[index].status == 0) {
                        userStatus = '<span style="color: grey">Inactive</span>';
                    } else if(data[index].status == 1) {
                        userStatus = '<span style="color: blue">Active</span>';
                    } else if(data[index].status == 1) {
                        userStatus = '<span style="color: red">Ban</span>';
                    } else {
                        userStatus = 'Undefined';
                    }
                    if(data[index].bankStatus == 0) {
                        bankStatus = '<span style="color: grey">Not Update</span>';
                    } else if(data[index].bankStatus == 1) {
                        bankStatus = '<span style="color: blue">Approved</span>';
                    } else if(data[index].bankStatus == 2) {
                        bankStatus = '<span style="color: green">Pending ...</span>';
                    } else if(data[index].bankStatus == 3) {
                        bankStatus = '<span style="color: red">Denied</span>';
                    } else {
                        bankStatus = 'Undefined';
                    }

                    tbl_main.row.add([
                        '<td>' + data[index].fbId + '</td>',
                        '<td>' + fullname + '</td>',
                        '<td><a href="' + picture + '" target="_blank"><img src="' + picture + '" alt="" width="40px" height="40px"></a></td>',
                        '<td>' + fbEmail + '</td>',
                        '<td>' + data[index].age + '</td>',
                        '<td>' + gender + '</td>',
                        '<td>' + phone + '</td>',
                        '<td>' + bankName + '</td>',
                        '<td>' + bankAccount + '</td>',
                        '<td>' + data[index].level + '</td>',
                        '<td>' + data[index].exp + '</td>',
                        '<td>' + data[index].buildingToken + '</td>',
                        '<td>' + data[index].coin + '</td>',
                        '<td></td>',
                        '<td>' + data[index].dateCreate + '</td>',
                        '<td>' + userStatus + '</td>',
                        '<td>' + bankStatus + '</td>',
                        '<td><button class="btn btn-sm btn-primary" onclick="goToDetailUser(' + data[index].id +');">Detail</button></td>'
                    ]).draw();
                });
            }
        };
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function startGameWorker(lastGameId) {
    if(typeof(Worker) !== "undefined") {
        if(typeof(game_record_worker) == "undefined") {
            game_record_worker = new Worker("/js/sp_gamewebworker.js");
        }
        game_record_worker.postMessage({lastId: lastGameId, token: CSRF_TOKEN, url: adminURL});
        game_record_worker.onmessage = function(event) {
            var total = event.data.total;
            if(total > 0) {
                var data = event.data.data;
                $.each(data, function(index, value){
                    var win = "";
                    var reward = "";
                    var fullname = data[index].fullname == null ? "" : data[index].fullname;
                    var name = data[index].name == null ? "" : data[index].name;
                    var dateEnd = data[index].dateEnd == null ? "" : data[index].dateEnd;
                    var timeActualPlay = data[index].timeActualPlay == null ? "" : data[index].timeActualPlay;
                    if(data[index].win == 1) {
                        win = '<span style="color: darkgreen">Success</span>';
                        reward = data[index].gold + " coins, " + data[index].exp + " experiences, " + data[index].buildingToken + " tokens";
                    } else if(data[index].win == 0) {
                        win = '<span style="color: lightgrey">Failed</span>';
                        reward = 'Nothing';
                    }

                    tbl_main.row.add([
                        '<td>' + fullname + '</td>',
                        '<td>' + name + '</td>',
                        '<td>' + dateEnd + '</td>',
                        '<td>' + win + '</td>',
                        '<td>' + timeActualPlay + '</td>',
                        '<td>' + data[index].compassRate + '</td>',
                        '<td>' + data[index].numberOfKey + '</td>',
                        '<td>' + reward + '</td>'
                    ]).draw();
                });
            }
        };
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function startGameStatisticWorker() {
    if(typeof(Worker) !== "undefined") {
        if(typeof(game_statistic_worker) == "undefined") {
            game_statistic_worker = new Worker("/js/sp_gamechartwebworker.js");
        }
        game_statistic_worker.postMessage({token: CSRF_TOKEN});
        realtime = "on";
        game_statistic_worker.onmessage = function(event) {
            var num = event.data;
            if (realtime === "on") {
                if (num > maxGameNumber) {
                    maxGameNumber = num;
                    initialInteractivePlot(num);
                } else {
                    interactive_plot.setData([getNumberOfGameFromServer(num)]);
                    // Since the axes don't change, we don't need to call plot.setupGrid()
                    interactive_plot.draw();
                }
            }
        };
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function startTimerWorker(time) {
    if (typeof(Worker) !== "undefined") {
        if (typeof(timer_worker) == "undefined") {
            timer_worker = new Worker("/js/sp_timerwebworker.js");
            timer_worker.postMessage({'start': true, 'time': time, 'delay': 1000});
            var servertime = $("#servertime");
            timer_worker.onmessage = function(event){
                servertime.html(event.data.time);
            }
        }
    }
}

function stopTimerWorker(){
    if(typeof(Worker) !== "undefined") {
        if(typeof(timer_worker) != "undefined") {
            timer_worker.postMessage({'start': false, 'time': 0, 'delay': 0});
            timer_worker.terminate();
            timer_worker = undefined;
        }
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function stopUserWorker() {
    if(typeof(Worker) !== "undefined") {
        if(typeof(user_worker) != "undefined") {
            user_worker.terminate();
            user_worker = undefined;
        }
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function stopGameWorker() {
    if(typeof(Worker) !== "undefined") {
        if(typeof(game_record_worker) != "undefined") {
            game_record_worker.terminate();
            game_record_worker = undefined;
        }
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function stopGameStatisticWorker() {
    if(typeof(Worker) !== "undefined") {
        if(typeof(game_statistic_worker) != "undefined") {
            game_statistic_worker.terminate();
            game_statistic_worker = undefined;
        }
    } else {
        console.log("Sorry! No Web Worker support.");
    }
}

function stopAllWebWorkers(){
    stopUserWorker();
    stopGameWorker();
    stopGameStatisticWorker();
    //stopTimerWorker();
}

$(document).ready(function(){
    startGameStatisticWorker();
});

/* Create an array with the values of all the input boxes in a column */
$.fn.dataTable.ext.order['dom-text'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).val();
    } );
}

/* Create an array with the values of all the input boxes in a column, parsed as numbers */
$.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).val() * 1;
    } );
}

/* Create an array with the values of all the select options in a column */
$.fn.dataTable.ext.order['dom-select'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('select', td).val();
    } );
}

/* Create an array with the values of all the checkboxes in a column */
$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
{
    return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
        return $('input', td).prop('checked') ? '1' : '0';
    } );
}

var TableUserDateRangeFilter = function( settings, data, dataIndex ) {
    var min = Date.parse( $('#filter-date-value-1').val(), 10 );
    var max = Date.parse( $('#filter-date-value-2').val(), 10 );
    var date = Date.parse( data[14].substr(0, 10) ) || 0;

    if ( ( isNaN( min ) && isNaN( max ) ) ||
        ( isNaN( min ) && date <= max ) ||
        ( min <= date   && isNaN( max ) ) ||
        ( min <= date   && date <= max ) )
    {
        return true;
    }
    return false;
}

var TableGameDateRangeFilter = function( settings, data, dataIndex ) {
    var min = Date.parse( $('#game-filter-date-value-1').val(), 10 );
    var max = Date.parse( $('#game-filter-date-value-2').val(), 10 );
    var date = Date.parse( data[2].substr(0, 10) ) || 0;

    if ( ( isNaN( min ) && isNaN( max ) ) ||
        ( isNaN( min ) && date <= max ) ||
        ( min <= date   && isNaN( max ) ) ||
        ( min <= date   && date <= max ) )
    {
        return true;
    }
    return false;
}

var TablePayoutDateRangeFilter = function( settings, data, dataIndex ) {
    var min = Date.parse( $('#payout-filter-date-value-1').val(), 10 );
    var max = Date.parse( $('#payout-filter-date-value-2').val(), 10 );
    var date = Date.parse( data[4].substr(0, 10) ) || 0;

    if ( ( isNaN( min ) && isNaN( max ) ) ||
        ( isNaN( min ) && date <= max ) ||
        ( min <= date   && isNaN( max ) ) ||
        ( min <= date   && date <= max ) )
    {
        return true;
    }
    return false;
}

var TableUserLevelRangeFilter = function( oSettings, aData, iDataIndex ) {
    var iMin = document.getElementById('filter-numeric-value-1').value * 1;
    var iMax = document.getElementById('filter-numeric-value-2').value * 1;
    var iLevel = aData[9] == "" ? 0 : aData[9] * 1;
    if ( iMin == "" && iMax == "" )
    {
        return true;
    }
    else if ( iMin == "" && iLevel <= iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && "" == iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && iLevel <= iMax )
    {
        return true;
    }
    return false;
}

var TableGameTimeRangeFilter = function( oSettings, aData, iDataIndex ) {
    var iMin = document.getElementById('game-filter-numeric-value-1').value * 1;
    var iMax = document.getElementById('game-filter-numeric-value-2').value * 1;
    var iLevel = aData[4] == "" ? 0 : aData[4] * 1;
    if ( iMin == "" && iMax == "" )
    {
        return true;
    }
    else if ( iMin == "" && iLevel <= iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && "" == iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && iLevel <= iMax )
    {
        return true;
    }
    return false;
}

var TablePayoutAmountRangeFilter = function( oSettings, aData, iDataIndex ) {
    var iMin = document.getElementById('payout-filter-numeric-value-1').value * 1;
    var iMax = document.getElementById('payout-filter-numeric-value-2').value * 1;
    var iLevel = aData[5] == "" ? 0 : aData[5] * 1;
    if ( iMin == "" && iMax == "" )
    {
        return true;
    }
    else if ( iMin == "" && iLevel <= iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && "" == iMax )
    {
        return true;
    }
    else if ( iMin <= iLevel && iLevel <= iMax )
    {
        return true;
    }
    return false;
}

function getNumberOfGameFromServer(number) {
    data.shift();
    data.push(number);
    var res = [];
    for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]]);
    }
    return res;
}

$(document).ready(function(){
    var visitorsData = [];
    $.ajax({
        url: adminURL + "visitorCountry",
        method: "POST",
        dataType: "json"
    }).done(function (data) {
        $.each(data, function(index, value){
            visitorsData[value['country']] = value['total'];
        });
    }).fail(function () {
        console.log("Failed in loading country.");
    }).always(function () {
        //World map by jvectormap
        $('#world-map').vectorMap({
            map: 'world_mill_en',
            backgroundColor: "transparent",
            regionStyle: {
                initial: {
                    fill: '#e4e4e4',
                    "fill-opacity": 1,
                    stroke: 'none',
                    "stroke-width": 0,
                    "stroke-opacity": 1
                }
            },
            series: {
                regions: [{
                    values: visitorsData,
                    scale: ["#92c1dc", "#ebf4f9"],
                    normalizeFunction: 'polynomial'
                }]
            },
            onRegionLabelShow: function (e, el, code) {
                if (typeof visitorsData[code] != "undefined")
                    el.html(el.html() + ': ' + visitorsData[code] + ' visitors');
            }
        });
    });

    //Sparkline charts
    var myvalues = [];
    $.ajax({
        url: adminURL + "visitorPerDay",
        method: "POST",
        dataType: "json"
    }).done(function (data) {
        $.each(data, function(index, value){
            myvalues.push(value);
        });
        $('#sparkline-1').sparkline(myvalues, {
            type: 'line',
            lineColor: '#92c1dc',
            fillColor: "#ebf4f9",
            height: '50',
            width: '80'
        });
    }).fail(function () {
        console.log("Failed in loading visitor per day.");
    }).always(function () {
        myvalues = [515, 519, 520, 522, 652, 810, 370, 627, 319, 630, 921];
        $('#sparkline-2').sparkline(myvalues, {
            type: 'line',
            lineColor: '#92c1dc',
            fillColor: "#ebf4f9",
            height: '50',
            width: '80'
        });
        myvalues = [15, 19, 20, 22, 33, 27, 31, 27, 19, 30, 21];
        $('#sparkline-3').sparkline(myvalues, {
            type: 'line',
            lineColor: '#92c1dc',
            fillColor: "#ebf4f9",
            height: '50',
            width: '80'
        });
    });

    var areaChartData = {
        labels: ["January", "February", "March", "April", "May", "June", "July"],
        datasets: [
            {
                label: "Cashouts",
                fillColor: "rgba(210, 214, 222, 1)",
                strokeColor: "rgba(210, 214, 222, 1)",
                pointColor: "rgba(210, 214, 222, 1)",
                pointStrokeColor: "#c1c7d1",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: [65, 59, 80, 81, 56, 55, 40]
            },
            {
                label: "Users",
                fillColor: "rgba(60,141,188,0.9)",
                strokeColor: "rgba(60,141,188,0.8)",
                pointColor: "#3b8bba",
                pointStrokeColor: "rgba(60,141,188,1)",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(60,141,188,1)",
                data: [28, 48, 40, 19, 86, 27, 90]
            }
        ]
    };

    $.ajax({
        url: adminURL + "cashoutAndUser",
        method: "POST",
        dataType: "json"
    }).done(function (data) {
        var months = [];
        var cashData = [];
        var userData = [];
        $.each(data, function(index, value){
            months.push(value["Month"]);
            cashData.push(value["Cashout"]);
            userData.push(value["User"]);
        });
        areaChartData["labels"] = months;
        areaChartData["datasets"][0]["data"] = cashData;
        areaChartData["datasets"][1]["data"] = userData;
    }).fail(function () {
        console.log("Failed in loading cashout & user.");
    }).always(function () {
        //-------------
        //- BAR CHART -
        //-------------
        var bar_chart = $("#barChart");
        if (bar_chart.length > 0) {
            var barChartCanvas = bar_chart.get(0).getContext("2d");
            var barChart = new Chart(barChartCanvas);
            var barChartData = areaChartData;
            barChartData.datasets[0].fillColor = "#00a65a";
            barChartData.datasets[0].strokeColor = "#00a65a";
            barChartData.datasets[0].pointColor = "#00a65a";
            barChartData.datasets[1].fillColor = "#f39c12";
            barChartData.datasets[1].strokeColor = "#f39c12";
            barChartData.datasets[1].pointColor = "#f39c12";
            var barChartOptions = {
                //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: true,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - If there is a stroke on each bar
                barShowStroke: true,
                //Number - Pixel width of the bar stroke
                barStrokeWidth: 2,
                //Number - Spacing between each of the X value sets
                barValueSpacing: 5,
                //Number - Spacing between data sets within X values
                barDatasetSpacing: 1,
                //String - A legend template
                legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",
                //Boolean - whether to make the chart responsive
                responsive: true,
                maintainAspectRatio: false
            };

            barChartOptions.datasetFill = false;
            barChart.Bar(barChartData, barChartOptions);
        }
    });

    /*
     * Flot Interactive Chart
     * -----------------------
     */
    // We use an inline data source in the example, usually data would
    // be fetched from a server

    //REALTIME TOGGLE
    $("#realtime .btn").click(function () {
        if ($(this).data("toggle") === "on") {
            realtime = "on";
        }
        else {
            realtime = "off";
        }
    });

    initialInteractivePlot(0);
    /*
     * END INTERACTIVE CHART
     */

    //-------------
    //- PIE CHART -
    //-------------
    // Get context with jQuery - using jQuery's .get() method.
    var pieChartCanvas = $("#pieChart").get(0).getContext("2d");
    var pieChart = new Chart(pieChartCanvas);
    var PieData = [
        {
            value: 700,
            color: "#f56954",
            highlight: "#f56954",
            label: "Chrome"
        },
        {
            value: 500,
            color: "#00a65a",
            highlight: "#00a65a",
            label: "IE"
        },
        {
            value: 400,
            color: "#f39c12",
            highlight: "#f39c12",
            label: "FireFox"
        },
        {
            value: 600,
            color: "#00c0ef",
            highlight: "#00c0ef",
            label: "Safari"
        },
        {
            value: 300,
            color: "#3c8dbc",
            highlight: "#3c8dbc",
            label: "Opera"
        },
        {
            value: 100,
            color: "#d2d6de",
            highlight: "#d2d6de",
            label: "Navigator"
        }
    ];
    var pieOptions = {
        //Boolean - Whether we should show a stroke on each segment
        segmentShowStroke: true,
        //String - The colour of each segment stroke
        segmentStrokeColor: "#fff",
        //Number - The width of each segment stroke
        segmentStrokeWidth: 1,
        //Number - The percentage of the chart that we cut out of the middle
        percentageInnerCutout: 50, // This is 0 for Pie charts
        //Number - Amount of animation steps
        animationSteps: 100,
        //String - Animation easing effect
        animationEasing: "easeOutBounce",
        //Boolean - Whether we animate the rotation of the Doughnut
        animateRotate: true,
        //Boolean - Whether we animate scaling the Doughnut from the centre
        animateScale: false,
        //Boolean - whether to make the chart responsive to window resizing
        responsive: true,
        // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
        maintainAspectRatio: false,
        //String - A legend template
        legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
        //String - A tooltip template
        tooltipTemplate: "<%=value %> <%=label%> users"
    };
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    pieChart.Doughnut(PieData, pieOptions);
    //-----------------
    //- END PIE CHART -
    //-----------------
});

function redirectToGetDataByMenuTitle(menu, title){
    getDataByMenuTitle($("#" + menu), title);
}

function getDataByMenuTitle(obj, title) {
    stopAllWebWorkers();
    main_loading.show();
    var requestURL = adminURL + title;
    if(!$.isEmptyObject(obj)) {
        $(".sidebar-menu").find("li.active").removeClass("active");
        $(obj).parent().addClass("active");
        var menuType = $(obj).parent().parent();
        if(menuType.prop("class") == "treeview-menu menu-open"){
            menuType.parent().addClass("active");
        }
    }
    $.ajax({
        url: requestURL,
        data: {advertiseId: $("#advertiseList").val()}
    })
        .done(function (data) {
            if($.fn.dataTable.ext.search.indexOf(TableUserDateRangeFilter) != -1){
                $.fn.dataTable.ext.search.splice($.fn.dataTable.ext.search.indexOf(TableUserDateRangeFilter), 1);
            }
            if($.fn.dataTableExt.afnFiltering.indexOf(TableUserLevelRangeFilter) != -1){
                $.fn.dataTableExt.afnFiltering.splice($.fn.dataTableExt.afnFiltering.indexOf(TableUserLevelRangeFilter), 1);
            }
            if($.fn.dataTable.ext.search.indexOf(TableGameDateRangeFilter) != -1){
                $.fn.dataTable.ext.search.splice($.fn.dataTable.ext.search.indexOf(TableGameDateRangeFilter), 1);
            }
            if($.fn.dataTableExt.afnFiltering.indexOf(TableGameTimeRangeFilter) != -1){
                $.fn.dataTableExt.afnFiltering.splice($.fn.dataTableExt.afnFiltering.indexOf(TableGameTimeRangeFilter), 1);
            }
            if($.fn.dataTable.ext.search.indexOf(TablePayoutDateRangeFilter) != -1){
                $.fn.dataTable.ext.search.splice($.fn.dataTable.ext.search.indexOf(TablePayoutDateRangeFilter), 1);
            }
            if($.fn.dataTableExt.afnFiltering.indexOf(TablePayoutAmountRangeFilter) != -1){
                $.fn.dataTableExt.afnFiltering.splice($.fn.dataTableExt.afnFiltering.indexOf(TablePayoutAmountRangeFilter), 1);
            }
            $(".content").html(data);
            var main_table = $("#main-table");
            if(main_table.length) {
                switch (title){
                    case "academyConfig":
                    case "dockConfig":
                    case "factoryConfig":
                        tbl_main = main_table.DataTable({
                            "columns": [
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                null
                            ],
                            "columnDefs": [ { "targets": 3, "orderable": false } ],
                            "pageLength": 100
                        });
                        break;
                    case "levelConfig":
                    case "dailyRewardConfig":
                        tbl_main = main_table.DataTable({
                            "columns": [
                                null,
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                null
                            ],
                            "columnDefs": [ { "targets": 4, "orderable": false } ],
                            "pageLength": 100
                        });
                        break;
                    case "compassRate":
                        tbl_main = main_table.DataTable({
                            "columns": [
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                null
                            ],
                            "columnDefs": [ { "targets": 2, "orderable": false } ],
                            "pageLength": 100
                        });
                        break;
                    case "advertisement":
                        tbl_main = main_table.DataTable({
                            "columnDefs": [ { "targets": [1, 15], "orderable": false } ],
                            "pageLength": 100
                        });
                        changeCheckboxToSwitchery();
                        break;
                    case "eventAdvertisement":
                        tbl_main = main_table.DataTable({
                            "columnDefs": [ { "targets": [1, 6], "orderable": false } ],
                            "pageLength": 100
                        });
                        changeCheckboxToSwitchery();
                        break;
                    case "eventLeaderboard":
                        tbl_main = main_table.DataTable({
                            "columns": [
                                {"orderDataType": "dom-text", type: 'string'},
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                {"orderDataType": "dom-text-numeric", type: 'string'},
                                {"orderDataType": "dom-text", type: 'string'},
                                {"orderDataType": "dom-text", type: 'string'},
                                {"orderDataType": "dom-text", type: 'string'},
                                {"orderDataType": "dom-select", type: 'numeric'},
                                null
                            ],
                            "order": [[ 1, "asc" ]],
                            "columnDefs": [ { "targets": 6, "orderable": false } ],
                            "pageLength": 100
                        });
                        $("select[id^=cell-][id$=-win]").change(function(){
                            var winId = $(this).prop("id");
                            var winText = winId.substr(0, winId.length - 3) + "value";
                            var winValue = $("#" + winText);
                            if ($(this).val() == 0) {
                                winValue.val("");
                                winValue.prop("disabled", true);
                            }
                            else winValue.prop("disabled", false);
                        });
                        break;
                    case "user":
                        $.fn.dataTable.ext.search.push(TableUserDateRangeFilter);
                        $.fn.dataTableExt.afnFiltering.push(TableUserLevelRangeFilter);
                        tbl_main = main_table.DataTable({
                            "columns": [
                                null, null, null, null, null, null, null, null,
                                null, null, null, null, null, null, null, null,
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                null
                            ],
                            "columnDefs": [ { "targets": [2, 17], "orderable": false } ],
                            "order": [[ 16, "asc" ]],
                            "pageLength": 100
                        });
                        $(document).find("input[id^=filter-date-value]").datepicker({
                            format: "yyyy-mm-dd"
                        });
                        $("#filter-string-value").keyup(function(){
                            var filter_operation = $("#filter-string-operation").val();
                            var filter_column = $("#filter-string-column").val();
                            if(filter_column == "name"){
                                if(filter_operation == "is"){
                                    tbl_main.column(1).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(1).search($(this).val()).draw();
                                }
                            }
                            if(filter_column == "bank"){
                                if(filter_operation == "is"){
                                    tbl_main.column(7).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(7).search($(this).val()).draw();
                                }
                            }
                            if(filter_column == "account"){
                                if(filter_operation == "is"){
                                    tbl_main.column(8).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(8).search($(this).val()).draw();
                                }
                            }
                        });
                        $('#filter-date-value-1, #filter-date-value-2').change( function() { tbl_main.draw(); });
                        $('#filter-numeric-value-1, #filter-numeric-value-2').keyup( function() { tbl_main.draw(); } );
                        startUserWorker($("#last-user-id").val());
                        break;
                    case "gameRecord":
                        //$.fn.dataTable.ext.search.push(TableGameDateRangeFilter);
                        //$.fn.dataTableExt.afnFiltering.push(TableGameTimeRangeFilter);
                        tbl_main = main_table.DataTable({
                            /*"oLanguage": {
                                "sProcessing": '<i class="fa fa-refresh fa-spin" style="color: #000; font-size: 50px; left: 50%; margin-left: -25px; margin-top: -25px; position: absolute; top: 50%;"></i>'
                            },*/
                            "processing": true,
                            "serverSide": true,
                            "ajax": adminURL + "gameRecordPagingAjax",
                            "order": [[ 3, "desc" ]],
                            "pageLength": 100
                            /*dom: '<"row"<"col-sm-4"l><"col-sm-2"B><"col-sm-2"T><"col-sm-4"f>><"row"<"col-sm-12"rt>><"row"<"col-sm-5"i><"col-sm-7"p>>',
                            "oTableTools": {
                                "aButtons": [ {
                                    "sExtends": "ajax",
                                    "fnClick": function () {
                                        //var search = this.s.dt.oPreviousSearch.sSearch;
                                        var iframe = document.createElement('iframe');
                                        iframe.style.height = "0px";
                                        iframe.style.width = "0px";
                                        iframe.src = adminURL + "gameRecordPagingAjax";// + encodeURIComponent(search);
                                        document.body.appendChild( iframe );
                                    }
                                } ]
                            },
                            buttons: [
                                {
                                    extend: 'collection',
                                    text: 'Export',
                                    buttons: [
                                        {
                                            title: 'Spotted Puzzle - Game Record Information',
                                            extend: 'copy',
                                            text: 'Copy',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6]
                                            }
                                        },
                                        {
                                            title: 'Spotted Puzzle - Game Record Information',
                                            extend: 'csv',
                                            text: 'CSV',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6]
                                            }
                                        },
                                        {
                                            title: 'Spotted Puzzle - Game Record Information',
                                            extend: 'excelHtml5',
                                            text: 'Excel',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6],
                                                modifier: {
                                                    search: 'applied',
                                                    page: 'all'
                                                }
                                            }
                                        },
                                        {
                                            title: 'Spotted Puzzle - Game Record Information',
                                            extend: 'pdf',
                                            text: 'PDF',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6]
                                            }
                                        },
                                        {
                                            title: 'Spotted Puzzle - Game Record Information',
                                            extend: 'print',
                                            text: 'Print',
                                            exportOptions: {
                                                columns: [0, 1, 2, 3, 4, 5, 6]
                                            }
                                        }
                                    ]
                                }
                            ]*/
                        });
                        /*$("#game-filter-string-value").keyup(function(){
                         var filter_operation = $("#game-filter-string-operation").val();
                         var filter_column = $("#game-filter-string-column").val();
                         if(filter_column == "username"){
                         if(filter_operation == "is"){
                         tbl_main.column(0).search('"' + $(this).val() + '"').draw();
                         }
                         if(filter_operation == "contain"){
                         tbl_main.column(0).search($(this).val()).draw();
                         }
                         }
                         if(filter_column == "advertiseTitle"){
                         if(filter_operation == "is"){
                         tbl_main.column(1).search('"' + $(this).val() + '"').draw();
                         }
                         if(filter_operation == "contain"){
                         tbl_main.column(1).search($(this).val()).draw();
                         }
                         }
                         });
                         $(document).find("input[id^=game-filter-date-value]").datepicker({
                         format: "yyyy-mm-dd"
                         });
                         $('#game-filter-date-value-1, #game-filter-date-value-2').change( function() { tbl_main.draw(); });
                         $('#game-filter-numeric-value-1, #game-filter-numeric-value-2').keyup( function() { tbl_main.draw(); } );
                         startGameWorker($("#last-game-id").val());*/
                        break;
                    case "payout":
                        $.fn.dataTable.ext.search.push(TablePayoutDateRangeFilter);
                        $.fn.dataTableExt.afnFiltering.push(TablePayoutAmountRangeFilter);
                        tbl_main = main_table.DataTable({
                            "oLanguage": {
                                "sProcessing": '<i class="fa fa-refresh fa-spin" style="color: #000; font-size: 50px; left: 50%; margin-left: -25px; margin-top: -25px; position: absolute; top: 50%;"></i>'
                            },
                            "columns": [
                                null, null, null, null, null, null,
                                {"orderDataType": "dom-text-numeric", type: 'numeric'},
                                null
                            ],
                            "order": [[ 6, "asc" ]],
                            "pageLength": 100,
                            dom: '<"row"<"col-sm-4"l><"col-sm-4"B><"col-sm-4"f>><"row"<"col-sm-12"rt>><"row"<"col-sm-5"i><"col-sm-7"p>>',
                            buttons: [
                                {
                                    title: 'Spotted Puzzle - Payout Information',
                                    extend: 'copyHtml5',
                                    text: 'Copy',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    title: 'Spotted Puzzle - Payout Information',
                                    extend: 'csvHtml5',
                                    text: 'CSV',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    title: 'Spotted Puzzle - Payout Information',
                                    extend: 'excelHtml5',
                                    text: 'Excel',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    title: 'Spotted Puzzle - Payout Information',
                                    extend: 'pdfHtml5',
                                    text: 'PDF',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                },
                                {
                                    title: 'Spotted Puzzle - Payout Information',
                                    extend: 'print',
                                    text: 'Print',
                                    exportOptions: {
                                        columns: [0, 1, 2, 3, 4, 5, 6]
                                    }
                                }
                            ]
                        });
                        $("#payout-filter-string-value").keyup(function(){
                            var filter_operation = $("#payout-filter-string-operation").val();
                            var filter_column = $("#payout-filter-string-column").val();
                            if(filter_column == "username"){
                                if(filter_operation == "is"){
                                    tbl_main.column(0).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(0).search($(this).val()).draw();
                                }
                            }
                            if(filter_column == "bankName"){
                                if(filter_operation == "is"){
                                    tbl_main.column(1).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(1).search($(this).val()).draw();
                                }
                            }
                            if(filter_column == "bankAccount"){
                                if(filter_operation == "is"){
                                    tbl_main.column(2).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(2).search($(this).val()).draw();
                                }
                            }
                            if(filter_column == "phone"){
                                if(filter_operation == "is"){
                                    tbl_main.column(3).search('"' + $(this).val() + '"').draw();
                                }
                                if(filter_operation == "contain"){
                                    tbl_main.column(3).search($(this).val()).draw();
                                }
                            }
                        });
                        $(document).find("input[id^=payout-filter-date-value]").datepicker({
                            format: "yyyy-mm-dd"
                        });
                        $('#payout-filter-date-value-1, #payout-filter-date-value-2').change( function() { tbl_main.draw(); });
                        $('#payout-filter-numeric-value-1, #payout-filter-numeric-value-2').keyup( function() { tbl_main.draw(); } );
                        break;
                    default:
                }
            } else {
                if (title == "export") {
                    $("#game-date").daterangepicker({
                        format: "YYYY-MM-DD",
                        separator: " ~ "
                    });
                    $("#event-date").daterangepicker({
                        format: "YYYY-MM-DD",
                        separator: " ~ "
                    });
                }
            }
            var sub_table = $("#sub-table");
            if(sub_table.length) {
                tbl_sub = sub_table.DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false
                });
            }
        })
        .fail(function () {
            showModalBox("Loading failed. Please try again later !", 0);
        })
        .always(function () {
            main_loading.hide();
        });
}

function showModalBox(text, type){
    if(type == 1) {
        $("#modal-text").text(text).parent().removeClass().addClass("alert alert-success");
        $("#modal-glyphicon").removeClass().addClass("glyphicon glyphicon-ok pull-right");
    }
    if(type == 0){
        $("#modal-text").text(text).parent().removeClass().addClass("alert alert-danger");
        $("#modal-glyphicon").removeClass().addClass("glyphicon glyphicon-remove pull-right");
    }
    $("#alert-modal-box").modal({backdrop: false});
    setTimeout(function(){
        $("#alert-modal-box").modal("hide");
    }, 3000);
}

function showConfirmBox(obj, id, callBack){
    $('#confirm-modal-box').off().modal({ backdrop: 'static', keyboard: false })
        .one('click', '#confirm-delete', function (e) {
            callBack(obj, id);
        })
}

function showConfirmPaymentBox(obj, id, callBack){
    $('#payment-modal-box').off().modal({ backdrop: 'static', keyboard: false })
        .one('click', '#confirm-payout', function (e) {
            callBack(obj, id);
        })
}

function refreshPayout(obj){
    main_loading.show();
    // TODO: update payout status from users
    $.ajax({
        method: "GET",
        url: adminURL + "updatePayout",
        dataType: "json",
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        //todo: update status
    }).fail(function () {
        showModalBox("Failed to proceed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function manualPayment(obj, id) {
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "manualPayment/" + id,
        dataType: "json",
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            $(obj).parent().find("button").fadeOut("slow");
            $("#status-" + id).text("Success").css("color", "darkgreen");
            showModalBox("Proceed Successfully !", 1);
        }
    }).fail(function () {
        showModalBox("Failed to proceed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function processPayment(obj, id) {
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "processPayment/" + id,
        dataType: "json",
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            $(obj).parent().find("button").fadeOut("slow");
            $("#status-" + id).text("Payout ...").css("color", "deepskyblue");
            showModalBox("Proceed Successfully !", 1);
        }
    }).fail(function () {
        showModalBox("Failed to proceed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelPayment(obj, id) {
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "cancelPayment/" + id,
        dataType: "json",
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            $(obj).parent().find("button").fadeOut("slow");
            $("#status-" + id).text("Cancel").css("color", "red");
            showModalBox("Proceed Successfully !", 1);
        }
    }).fail(function () {
        showModalBox("Failed to proceed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function addNewAcademyRow(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertAcademy",
        dataType: "json",
        data: {
            level: $("#cell-new-level").val(),
            token: $("#cell-new-token").val(),
            rate: $("#cell-new-rate").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-level" type="text" value="' + rs.level + '" disabled>',
                '<input id="cell-' + rs.id + '-token" type="text" value="' + rs.buildingToken + '" disabled>',
                '<input id="cell-' + rs.id + '-rate" type="text" value="' + rs.bonusRate + '" disabled>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedAcademyRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="deleteSelectedAcademyRow(this, ' + rs.id + ', deleteSelectedAcademyRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedAcademyRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedAcademyRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-level").val("");
            $("#cell-new-token").val("");
            $("#cell-new-rate").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editSelectedAcademyRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['level'] = $("#cell-" + id + "-level").val();
    rowData['token'] = $("#cell-" + id + "-token").val();
    rowData['rate'] = $("#cell-" + id + "-rate").val();
    backupRows['academy' + id] = rowData;
}

function saveSelectedAcademyRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateAcademy",
        dataType: "json",
        data: {
            id: id,
            level: $("#cell-" + id + "-level").val(),
            token: $("#cell-" + id + "-token").val(),
            rate: $("#cell-" + id + "-rate").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['academy' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedAcademyRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "academyConfig/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedAcademyRow(obj, id){
    $("#cell-" + id + "-level").val(backupRows['academy' + id]['level']);
    $("#cell-" + id + "-token").val(backupRows['academy' + id]['token']);
    $("#cell-" + id + "-rate").val(backupRows['academy' + id]['rate']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['academy' + id];
}

function addNewCompassRow(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertCompass",
        dataType: "json",
        data: {
            multiple: $("#cell-new-multiple").val(),
            rate: $("#cell-new-rate").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-multiple" type="text" value="' + rs.multiple + '" disabled>',
                '<input id="cell-' + rs.id + '-rate" type="text" value="' + rs.rate + '" disabled>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedCompassRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="deleteSelectedCompassRow(this, ' + rs.id + ', deleteSelectedCompassRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedCompassRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedCompassRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-multiple").val("");
            $("#cell-new-rate").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editSelectedCompassRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['multiple'] = $("#cell-" + id + "-multiple").val();
    rowData['rate'] = $("#cell-" + id + "-rate").val();
    backupRows['compass' + id] = rowData;
}

function saveSelectedCompassRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateCompass",
        dataType: "json",
        data: {
            id: id,
            multiple: $("#cell-" + id + "-multiple").val(),
            rate: $("#cell-" + id + "-rate").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['compass' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedCompassRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "compassRate/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedCompassRow(obj, id){
    $("#cell-" + id + "-multiple").val(backupRows['compass' + id]['multiple']);
    $("#cell-" + id + "-rate").val(backupRows['compass' + id]['rate']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['compass' + id];
}

/*function addNewDailyRewardRow(){
 $.ajax({
 method: "POST",
 url: adminURL + "insertReward",
 dataType: "json",
 data: {
 name: $("#cell-new-name").val(),
 rate: $("#cell-new-rate").val(),
 min: $("#cell-new-min").val(),
 max: $("#cell-new-max").val(),
 _token: CSRF_TOKEN
 }
 }).done(function(rs){
 if(rs.error){
 showModalBox(rs.message, 0);
 } else {
 showModalBox("Insert Successfully !", 1);
 tbl_main.row.add([
 '<input id="cell-' + rs.id + '-name" type="text" value="' + rs.name + '" disabled>',
 '<input id="cell-' + rs.id + '-rate" type="text" value="' + rs.rate + '" disabled>',
 '<input id="cell-' + rs.id + '-min" type="text" value="' + rs.minValue + '" disabled>',
 '<input id="cell-' + rs.id + '-max" type="text" value="' + rs.maxValue + '" disabled>',
 '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
 '<button class="btn btn-sm btn-info" onclick="editSelectedDailyRewardRow(this, ' + rs.id + ');">Edit</button> ' +
 '<button class="btn btn-sm btn-danger" onclick="deleteSelectedDailyRewardRow(this, ' + rs.id + ', deleteSelectedDailyRewardRow);">Delete</button>' +
 '</div>' +
 '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
 '<button class="btn btn-sm btn-primary" onclick="saveSelectedDailyRewardRow(this, ' + rs.id + ');">Save</button> ' +
 '<button class="btn btn-sm btn-default" onclick="cancelSelectedDailyRewardRow(this, ' + rs.id + ');">Cancel</button>' +
 '</div>'
 ]).draw();
 $("#cell-new-name").val("");
 $("#cell-new-rate").val("");
 $("#cell-new-min").val("");
 $("#cell-new-max").val("");
 }
 }).fail(function(){
 showModalBox("Insert Failed. Please try again !", 0);
 }).always(function(){

 });
 }*/

function editSelectedDailyRewardRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['name'] = $("#cell-" + id + "-name").val();
    rowData['rate'] = $("#cell-" + id + "-rate").val();
    rowData['min'] = $("#cell-" + id + "-min").val();
    rowData['max'] = $("#cell-" + id + "-max").val();
    backupRows['reward' + id] = rowData;
}

function saveSelectedDailyRewardRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateReward",
        dataType: "json",
        data: {
            id: id,
            name: $("#cell-" + id + "-name").val(),
            rate: $("#cell-" + id + "-rate").val(),
            min: $("#cell-" + id + "-min").val(),
            max: $("#cell-" + id + "-max").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['reward' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedDailyRewardRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "dailyRewardConfig/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedDailyRewardRow(obj, id){
    $("#cell-" + id + "-name").val(backupRows['reward' + id]['name']);
    $("#cell-" + id + "-rate").val(backupRows['reward' + id]['rate']);
    $("#cell-" + id + "-min").val(backupRows['reward' + id]['min']);
    $("#cell-" + id + "-max").val(backupRows['reward' + id]['max']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['reward' + id];
}

function addNewDockRow(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertDock",
        dataType: "json",
        data: {
            level: $("#cell-new-level").val(),
            token: $("#cell-new-token").val(),
            ship: $("#cell-new-ship").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-level" type="text" value="' + rs.level + '" disabled>',
                '<input id="cell-' + rs.id + '-token" type="text" value="' + rs.buildingToken + '" disabled>',
                '<input id="cell-' + rs.id + '-ship" type="text" value="' + rs.bonusShip + '" disabled>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedDockRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, ' + rs.id + ', deleteSelectedDockRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedDockRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedDockRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-level").val("");
            $("#cell-new-token").val("");
            $("#cell-new-ship").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editSelectedDockRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['level'] = $("#cell-" + id + "-level").val();
    rowData['token'] = $("#cell-" + id + "-token").val();
    rowData['ship'] = $("#cell-" + id + "-ship").val();
    backupRows['dock' + id] = rowData;
}

function saveSelectedDockRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateDock",
        dataType: "json",
        data: {
            id: id,
            level: $("#cell-" + id + "-level").val(),
            token: $("#cell-" + id + "-token").val(),
            ship: $("#cell-" + id + "-ship").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['dock' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedDockRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "dockConfig/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedDockRow(obj, id){
    $("#cell-" + id + "-level").val(backupRows['dock' + id]['level']);
    $("#cell-" + id + "-token").val(backupRows['dock' + id]['token']);
    $("#cell-" + id + "-ship").val(backupRows['dock' + id]['ship']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['dock' + id];
}

function addNewFactoryRow(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertFactory",
        dataType: "json",
        data: {
            level: $("#cell-new-level").val(),
            token: $("#cell-new-token").val(),
            time: $("#cell-new-time").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-level" type="text" value="' + rs.level + '" disabled>',
                '<input id="cell-' + rs.id + '-token" type="text" value="' + rs.buildingToken + '" disabled>',
                '<input id="cell-' + rs.id + '-time" type="text" value="' + rs.reducePercent + '" disabled>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedFactoryRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, ' + rs.id + ', deleteSelectedFactoryRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedFactoryRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedFactoryRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-level").val("");
            $("#cell-new-token").val("");
            $("#cell-new-time").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editSelectedFactoryRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['level'] = $("#cell-" + id + "-level").val();
    rowData['token'] = $("#cell-" + id + "-token").val();
    rowData['time'] = $("#cell-" + id + "-time").val();
    backupRows['dock' + id] = rowData;
}

function saveSelectedFactoryRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateFactory",
        dataType: "json",
        data: {
            id: id,
            level: $("#cell-" + id + "-level").val(),
            token: $("#cell-" + id + "-token").val(),
            time: $("#cell-" + id + "-time").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['factory' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedFactoryRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "factoryConfig/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedFactoryRow(obj, id){
    $("#cell-" + id + "-level").val(backupRows['factory' + id]['level']);
    $("#cell-" + id + "-token").val(backupRows['factory' + id]['token']);
    $("#cell-" + id + "-time").val(backupRows['factory' + id]['time']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['factory' + id];
}

function addNewLevelRow(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertLevel",
        dataType: "json",
        data: {
            level: $("#cell-new-level").val(),
            token: $("#cell-new-token").val(),
            from: $("#cell-new-from").val(),
            to: $("#cell-new-to").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-level" type="text" value="' + rs.level + '" disabled>',
                '<input id="cell-' + rs.id + '-token" type="text" value="' + rs.buildingToken + '" disabled>',
                '<input id="cell-' + rs.id + '-from" type="text" value="' + rs.fromExp + '" disabled>',
                '<input id="cell-' + rs.id + '-to" type="text" value="' + rs.toExp + '" disabled>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedLevelRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, ' + rs.id + ', deleteSelectedLevelRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedLevelRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedLevelRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-level").val("");
            $("#cell-new-token").val("");
            $("#cell-new-from").val("");
            $("#cell-new-to").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editSelectedLevelRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['level'] = $("#cell-" + id + "-level").val();
    rowData['token'] = $("#cell-" + id + "-token").val();
    rowData['from'] = $("#cell-" + id + "-from").val();
    rowData['to'] = $("#cell-" + id + "-to").val();
    backupRows['level' + id] = rowData;
}

function saveSelectedLevelRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateLevel",
        dataType: "json",
        data: {
            id: id,
            level: $("#cell-" + id + "-level").val(),
            token: $("#cell-" + id + "-token").val(),
            from: $("#cell-" + id + "-from").val(),
            to: $("#cell-" + id + "-to").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
            delete backupRows['level' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function deleteSelectedLevelRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "levelConfig/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function cancelSelectedLevelRow(obj, id){
    $("#cell-" + id + "-level").val(backupRows['level' + id]['level']);
    $("#cell-" + id + "-token").val(backupRows['level' + id]['token']);
    $("#cell-" + id + "-from").val(backupRows['level' + id]['from']);
    $("#cell-" + id + "-to").val(backupRows['level' + id]['to']);
    $(obj).parent().parent().parent().find("input").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['level' + id];
}

function goToDetailAdvertisement(id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "advertisementDetail",
        data: {
            id: id,
            _token: CSRF_TOKEN
        }
    }).done(function(data){
        $(".content").html(data);
        var main_table = $("#main-table");
        if(main_table.length) {
            tbl_main = main_table.DataTable({
                "columns": [
                    {"orderDataType": "dom-text", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-select"},
                    null
                ],
                "columnDefs": [ { "targets": 4, "orderable": false } ]
            });
        }
        var sub_table = $("#sub-table");
        if(sub_table.length) {
            tbl_sub = sub_table.DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        }
        $(document).find("input[id^=cell-][id$=-from]").datepicker({
            format: 'yyyy-mm-dd 00:00:00'
        });
        $(document).find("input[id^=cell-][id$=-to]").datepicker({
            format: 'yyyy-mm-dd 23:59:59'
        });
        $("#browse-image").change(function(){
            loadAdvertiseImage(this);
        });
        $("input[name=dealExpiration]").datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        });
    }).fail(function(){
        showModalBox("Operation failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function goBackToDetailAdvertisement(id){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "question",
        data: {
            id: id,
            _token: CSRF_TOKEN
        }
    }).done(function(data){
        $(".sub-content").html(data);
        var main_table = $("#main-table");
        if(main_table.length) {
            tbl_main = main_table.DataTable({
                "columns": [
                    {"orderDataType": "dom-text", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-select"},
                    null
                ],
                "columnDefs": [ { "targets": 4, "orderable": false } ]
            });
        }
        var sub_table = $("#sub-table");
        if(sub_table.length) {
            tbl_sub = sub_table.DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        }
        $(document).find("input[id^=cell-][id$=-from]").datepicker({
            format: 'yyyy-mm-dd 00:00:00'
        });
        $(document).find("input[id^=cell-][id$=-to]").datepicker({
            format: 'yyyy-mm-dd 23:59:59'
        });
    }).fail(function(){
        showModalBox("Operation failed. Please try again !", 0);
    }).always(function () {
        $("#sub-content-loading-screen").hide();
    });
}

function loadAdvertiseImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgString = e.target.result;
            $("#show-image").attr("src", imgString);
            $("#send-image").val(imgString.substr(imgString.indexOf(",") + 1));
            createThumbnailImage(imgString);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function loadAdvertiseLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgString = e.target.result;
            $("#show-logo").attr("src", imgString);
            $("#send-logo").val(imgString.substr(imgString.indexOf(",") + 1));
            createThumbnailImage(imgString);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function loadMultipleAdvertiseImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var imgString = e.target.result;
            var showImage = "show-image-";
            var sendImage = "send-image-";
            var removeImage = "remove-image-";
            var difficult = "difficult-";
            var stage = 0;
            switch (input.id) {
                case "browse-image-1":
                    stage = 1;
                    break;
                case "browse-image-2":
                    stage = 2;
                    break;
                case "browse-image-3":
                    stage = 3;
                    break;
                case "browse-image-4":
                    stage = 4;
                    break;
                default:
            }
            $("#" + showImage + stage).attr("src", imgString);
            $("#" + sendImage + stage).val(imgString.substr(imgString.indexOf(",") + 1));
            $("#" + removeImage + stage).prop("disabled", false);
            $("select[name=" + difficult + stage + "]").prop("disabled", false);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function createThumbnailImage(image){
    var img = new Image();
    img.onload = function() {
        mainCanvas = document.createElement("canvas");
        mainCanvas.width = 167;
        mainCanvas.height = 200;
        var ctx = mainCanvas.getContext("2d");
        ctx.drawImage(img, 0, 0, mainCanvas.width, mainCanvas.height);
        var thumbString = mainCanvas.toDataURL();
        $("#thumb-image").val(thumbString.substr(thumbString.indexOf(",") + 1));
    };
    img.src = image;
}

function insertCurrentAdvertisement(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertAdvertisement",
        dataType: "json",
        data: $("#detail-form").serialize() + "&_token=" + CSRF_TOKEN
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            $("#detail-form").find("input[type=text]").val("").end().find("select").val(0);
            $("#send-image").val("");
            $("#show-image").attr("src", "");
            $("#browse-image").val("");
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function editCurrentAdvertisement(){
    $("#detail-form").find("input[type=text],select").prop("disabled", false);
    $("#browse-image").prop("disabled", false);
    $("#advertise-operation-edit").fadeOut("slow");
    setTimeout(function(){
        $("#advertise-operation-save").fadeIn("slow");
    }, 1000);
}

function saveCurrentAdvertisement(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "saveAdvertisement",
        dataType: "json",
        data: $("#detail-form").serialize() + "&_token=" + CSRF_TOKEN
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#detail-form").find("input[type=text],select").prop("disabled", true);
            $("#browse-image").prop("disabled", true);
            $("#advertise-operation-save").fadeOut("slow");
            setTimeout(function(){
                $("#advertise-operation-edit").fadeIn("slow");
            }, 1000);
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function addNewQuestionRow(advertiseId){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertQuestion",
        dataType: "json",
        data: {
            advertiseId: advertiseId,
            title: $("#cell-new-title").val(),
            from: $("#cell-new-from").val(),
            to: $("#cell-new-to").val(),
            status: $("#cell-new-status").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            if(rs.status == 0){
                var statusText = '<option value="0" selected>Inactive</option>' +
                    '<option value="1">Active</option>';
            }
            if(rs.status == 1){
                var statusText = '<option value="0">Inactive</option>' +
                    '<option value="1" selected>Active</option>';
            }
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-title" type="text" value="' + rs.title + '" disabled>',
                '<input id="cell-' + rs.id + '-from" type="text" value="' + rs.dateFrom + '" disabled>',
                '<input id="cell-' + rs.id + '-to" type="text" value="' + rs.dateTo + '" disabled>',
                '<select id="cell-' + rs.id + '-status" disabled>' + statusText + '</select>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedQuestionRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, ' + rs.id + ', deleteSelectedQuestionRow);">Delete</button> ' +
                '<button class="btn btn-sm btn-primary" onclick="goToDetailQuestion(' + rs.id + ', ' + rs.advertiseId + ');">Detail</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedQuestionRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedQuestionRow(this, ' + rs.id + ');">Cancel</button> ' +
                '<button class="btn btn-sm btn-primary" onclick="goToDetailQuestion(' + rs.id + ', ' + rs.advertiseId + ');">Detail</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-title").val("");
            $("#cell-new-status").val(0);
            $("#cell-new-from").val("");
            $("#cell-new-to").val("");
            $(document).find("input[id^=cell-][id$=-from]").datepicker({
                format: 'yyyy-mm-dd 00:00:00'
            });
            $(document).find("input[id^=cell-][id$=-to]").datepicker({
                format: 'yyyy-mm-dd 23:59:59'
            });
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        $("#sub-content-loading-screen").hide();
    });
}

function editSelectedQuestionRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input,select").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['title'] = $("#cell-" + id + "-title").val();
    rowData['from'] = $("#cell-" + id + "-from").val();
    rowData['to'] = $("#cell-" + id + "-to").val();
    rowData['status'] = $("#cell-" + id + "-status").val();
    backupRows['question' + id] = rowData;
}

function saveSelectedQuestionRow(obj, id){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateQuestion",
        dataType: "json",
        data: {
            id: id,
            title: $("#cell-" + id + "-title").val(),
            from: $("#cell-" + id + "-from").val(),
            to: $("#cell-" + id + "-to").val(),
            status: $("#cell-" + id + "-status").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input,select").prop("disabled", true).end().css("background-color", "");
            delete backupRows['question' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        $("#sub-content-loading-screen").hide();
    });
}

function deleteSelectedQuestionRow(obj, id){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "question/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        $("#sub-content-loading-screen").hide();
    });
}

function cancelSelectedQuestionRow(obj, id){
    $("#cell-" + id + "-title").val(backupRows['question' + id]['title']);
    $("#cell-" + id + "-from").val(backupRows['question' + id]['from']);
    $("#cell-" + id + "-to").val(backupRows['question' + id]['to']);
    $("#cell-" + id + "-status").val(backupRows['question' + id]['status']);
    $(obj).parent().parent().parent().find("input,select").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['question' + id];
}

function goToDetailQuestion(questionId, advertiseId){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "questionAnswer",
        data: {
            questionId: questionId,
            advertiseId: advertiseId,
            _token: CSRF_TOKEN
        }
    }).done(function(data){
        $(".sub-content").html(data);
        var main_table = $("#main-table");
        if(main_table.length) {
            tbl_main = main_table.DataTable({
                "columns": [
                    {"orderDataType": "dom-text", type: 'string'},
                    {"orderDataType": "dom-select"},
                    null
                ],
                "columnDefs": [ { "targets": 2, "orderable": false } ]
            });
        }
        var sub_table = $("#sub-table");
        if(sub_table.length) {
            tbl_sub = sub_table.DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        }
    }).fail(function(){
        showModalBox("Operation failed. Please try again !", 0);
    }).always(function () {
        $("#sub-content-loading-screen").hide();
    });
}

function addNewQuestionAnswerRow(questionId){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertQuestionAnswer",
        dataType: "json",
        data: {
            questionId: questionId,
            title: $("#cell-new-title").val(),
            correct: $("#cell-new-correct").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            if(rs.correct == 0){
                var statusText = '<option value="0" selected>False</option>' +
                    '<option value="1">True</option>';
            }
            if(rs.correct == 1){
                var statusText = '<option value="0">False</option>' +
                    '<option value="1" selected>True</option>';
            }
            tbl_main.row.add([
                '<input id="cell-' + rs.id + '-title" type="text" value="' + rs.title + '" disabled>',
                '<select id="cell-' + rs.id + '-correct" disabled>' + statusText + '</select>',
                '<div id="operation-' + rs.id + '-edit" style="position: absolute">' +
                '<button class="btn btn-sm btn-info" onclick="editSelectedQuestionAnswerRow(this, ' + rs.id + ');">Edit</button> ' +
                '<button class="btn btn-sm btn-danger" onclick="showConfirmBox(this, ' + rs.id + ', deleteSelectedQuestionAnswerRow);">Delete</button>' +
                '</div>' +
                '<div id="operation-' + rs.id + '-save" style="position: absolute; display: none">' +
                '<button class="btn btn-sm btn-primary" onclick="saveSelectedQuestionAnswerRow(this, ' + rs.id + ');">Save</button> ' +
                '<button class="btn btn-sm btn-default" onclick="cancelSelectedQuestionAnswerRow(this, ' + rs.id + ');">Cancel</button>' +
                '</div>'
            ]).draw();
            $("#cell-new-title").val("");
            $("#cell-new-correct").val(0);
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function(){
        $("#sub-content-loading-screen").hide();
    });
}

function editSelectedQuestionAnswerRow(obj, id){
    var editButton = $(obj);
    editButton.parent().parent().parent().find("input,select").prop("disabled", false).end().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['title'] = $("#cell-" + id + "-title").val();
    rowData['correct'] = $("#cell-" + id + "-correct").val();
    backupRows['questionAnswer' + id] = rowData;
}

function saveSelectedQuestionAnswerRow(obj, id){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateQuestionAnswer",
        dataType: "json",
        data: {
            id: id,
            title: $("#cell-" + id + "-title").val(),
            correct: $("#cell-" + id + "-correct").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $(obj).parent().parent().parent().find("input,select").prop("disabled", true).end().css("background-color", "");
            delete backupRows['questionAnswer' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        $("#sub-content-loading-screen").hide();
    });
}

function deleteSelectedQuestionAnswerRow(obj, id){
    $("#sub-content-loading-screen").show();
    $.ajax({
        method: "DELETE",
        url: adminURL + "questionAnswer/" + id,
        data: {_token: CSRF_TOKEN}
    }).done(function (rs) {
        tbl_main.row($(obj).parent().parent().parent()).remove().draw();
        showModalBox("Delete Successfully !", 1);
    }).fail(function () {
        showModalBox("Delete Failed. Please try again !", 0);
    }).always(function () {
        $("#sub-content-loading-screen").hide();
    });
}

function cancelSelectedQuestionAnswerRow(obj, id){
    $("#cell-" + id + "-title").val(backupRows['questionAnswer' + id]['title']);
    $("#cell-" + id + "-correct").val(backupRows['questionAnswer' + id]['correct']);
    $(obj).parent().parent().parent().find("input,select").prop("disabled", true).end().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['questionAnswer' + id];
}

function goToDetailUser(id){
    stopUserWorker();
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "userDetail",
        data: {
            id: id,
            _token: CSRF_TOKEN
        }
    }).done(function(data){
        $(".content").html(data);
        var main_table = $("#main-table");
        if(main_table.length) {
            tbl_main = main_table.DataTable({
                "columns": [
                    {"orderDataType": "dom-text", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-text-numeric", type: 'string'},
                    {"orderDataType": "dom-select"},
                    null
                ],
                "columnDefs": [ { "targets": 4, "orderable": false } ]
            });
        }
        var sub_table = $("#sub-table");
        if(sub_table.length) {
            tbl_sub = sub_table.DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "autoWidth": false
            });
        }
        $(document).find("input[id^=cell-][id$=-from]").datepicker({
            format: 'yyyy-mm-dd 00:00:00'
        });
        $(document).find("input[id^=cell-][id$=-to]").datepicker({
            format: 'yyyy-mm-dd 23:59:59'
        });
    }).fail(function(){
        showModalBox("Operation failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function editCurrentUser(){
    $("#user-form").find("input:not([name$=-disabled]),select").prop("disabled", false);
    $("#user-operation-edit").fadeOut("slow");
    setTimeout(function(){
        $("#user-operation-save").fadeIn("slow");
    }, 1000);
}

function saveCurrentUser(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "updateUser",
        dataType: "json",
        data: $("#user-form").serialize() + "&_token=" + CSRF_TOKEN
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $("#user-form").find("input:not([name$=-disabled]),select").prop("disabled", true);
            $("#user-operation-save").fadeOut("slow");
            setTimeout(function(){
                $("#user-operation-edit").fadeIn("slow");
            }, 1000);
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function goToDetailTelevisionEventAdvertisement(id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "televisionEventAdvertisementDetail",
        data: {
            id: id,
            _token: CSRF_TOKEN
        }
    }).done(function(data){
        $(".content").html(data);
        $(document).find("input[name=start]").datepicker({
            format: 'yyyy-mm-dd 00:00:00'
        });
        $(document).find("input[name=end]").datepicker({
            format: 'yyyy-mm-dd 23:59:59'
        });
        $("#browse-logo").change(function(){
            loadAdvertiseLogo(this);
        });
        $("input[id^=browse-image]").change(function(){
            loadMultipleAdvertiseImage(this);
        });
    }).fail(function(){
        showModalBox("Operation failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function insertCurrentTelevisionEventAdvertisement(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "insertTelevisionEventAdvertisement",
        dataType: "json",
        data: $("#detail-form").serialize() + "&_token=" + CSRF_TOKEN +
            "&difficult-1=" + $("select[name=difficult-1]").val() +
            "&difficult-2=" + $("select[name=difficult-2]").val() +
            "&difficult-3=" + $("select[name=difficult-3]").val() +
            "&difficult-4=" + $("select[name=difficult-4]").val()
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Insert Successfully !", 1);
            $("#detail-form").find("input[type=text]").val("").end().find("select").val(0);
            $("input[id^=send-image-]").val("");
            $("img[id^=show-image-]").attr("src", "");
            $("input[id^=browse-image-]").val("");
            $("select[name^=difficult-]").val(0).prop("disabled", true);
        }
    }).fail(function(){
        showModalBox("Insert Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function editCurrentTelevisionEventAdvertisement(){
    $(document).find("input,textarea").prop("disabled", false);
    $("#detail-form").find("select").prop("disabled", false);
    $("img[id^=show-image]").each(function(){
        if ($(this).attr("src") != "") {
            $(this).parent().find("select,button").prop("disabled", false);
        }
    });
    $("#advertise-operation-edit").fadeOut("slow");
    setTimeout(function(){
        $("#advertise-operation-save").fadeIn("slow");
    }, 1000);
}

function saveCurrentTelevisionEventAdvertisement(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "saveTelevisionEventAdvertisement",
        dataType: "json",
        data: $("#detail-form").serialize() + "&_token=" + CSRF_TOKEN +
            "&difficult-1=" + $("select[name=difficult-1]").val() +
            "&difficult-2=" + $("select[name=difficult-2]").val() +
            "&difficult-3=" + $("select[name=difficult-3]").val() +
            "&difficult-4=" + $("select[name=difficult-4]").val()
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Update Successfully !", 1);
            $(document).find("input,select,textarea").prop("disabled", true);
            $("button[id^=remove-image-]").prop("disabled", true);
            $("#advertise-operation-save").fadeOut("slow");
            setTimeout(function(){
                $("#advertise-operation-edit").fadeIn("slow");
            }, 1000);
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function () {
        main_loading.hide();
    });
}

function removeEventAdvertiseImage(id) {
    $("#send-image-" + id).val("remove");
    $("#browse-image-" + id).val("");
    $("#show-image-" + id).prop("src", "");
    $("select[name=difficult-" + id + "]").val(0).prop("disabled", true);
    $("#remove-image-" + id).prop("disabled", true);
}

function editSelectedLeaderboardRow(obj, id){
    var editButton = $(obj);
    var winComboBox = $("#cell-" + id + "-win");
    var valueTextbox = $("#cell-" + id + "-value");
    var publishComboBox = $("#cell-" + id + "-publish");
    winComboBox.prop("disabled", false);
    valueTextbox.prop("disabled", false);
    publishComboBox.prop("disabled", false);
    editButton.parent().parent().parent().css("background-color", "whitesmoke");
    $("#operation-" + id + "-edit").fadeOut("slow");
    $("#operation-" + id + "-save").fadeIn("slow");
    var rowData = [];
    rowData['win'] = winComboBox.val();
    rowData['value'] = valueTextbox.val();
    rowData['publish'] = publishComboBox.val();
    backupRows['leaderboard' + id] = rowData;
}

function saveSelectedLeaderboardRow(obj, id){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "saveEventLeaderboard",
        dataType: "json",
        data: {
            id: id,
            win: $("#cell-" + id + "-win").val(),
            value: $("#cell-" + id + "-value").val(),
            publish: $("#cell-" + id + "-publish").val(),
            advertiseId: $("#advertiseList").val(),
            _token: CSRF_TOKEN
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            if(rs.entirePublish){
                $("#entirePublish").prop("disabled", false);
            } else {
                $("#entirePublish").prop("disabled", true);
            }
            showModalBox("Update Successfully !", 1);
            $("#operation-" + id + "-edit").fadeIn("slow");
            $("#operation-" + id + "-save").fadeOut("slow");
            $("#cell-" + id + "-win").prop("disabled", true);
            $("#cell-" + id + "-value").prop("disabled", true);
            $("#cell-" + id + "-publish").prop("disabled", true);
            $(obj).parent().parent().parent().css("background-color", "");
            delete backupRows['leaderboard' + id];
        }
    }).fail(function(){
        showModalBox("Update Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function cancelSelectedLeaderboardRow(obj, id){
    var winComboBox = $("#cell-" + id + "-win");
    var valueTextbox = $("#cell-" + id + "-value");
    var publishComboBox = $("#cell-" + id + "-publish");
    winComboBox.val(backupRows['leaderboard' + id]['win']);
    valueTextbox.val(backupRows['leaderboard' + id]['value']);
    publishComboBox.val(backupRows['leaderboard' + id]['publish']);
    winComboBox.prop("disabled", true);
    valueTextbox.prop("disabled", true);
    publishComboBox.prop("disabled", true);
    $(obj).parent().parent().parent().css("background-color", "");
    $("#operation-" + id + "-edit").fadeIn("slow");
    $("#operation-" + id + "-save").fadeOut("slow");
    delete backupRows['leaderboard' + id];
}

function publishEntireEventLeaderboard(){
    main_loading.show();
    $.ajax({
        method: "POST",
        url: adminURL + "publishEntireEventLeaderboard",
        dataType: "json",
        data: {
            _token: CSRF_TOKEN,
            advertiseId: $("#advertiseList").val()
        }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
            $("#entirePublish").prop("disabled", true);
        } else {
            showModalBox("Publish Entire Leaderboard Successfully !", 1);
            tbl_main.$("select[id^=cell-][id$=-publish]").val(1);
        }
    }).fail(function(){
        showModalBox("Publish Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function toggleAutoApproveBankStatus(obj){
    main_loading.show();
    var defaultBankStatus;
    if (obj.checked) {
        defaultBankStatus = 1;
    } else {
        defaultBankStatus = 2;
    }
    $.ajax({
        method: "POST",
        url: adminURL + "autoApproveBankStatus",
        dataType: "json",
        data: { status: defaultBankStatus, _token: CSRF_TOKEN }
    }).done(function(rs){
        if(rs.error){
            showModalBox(rs.message, 0);
        } else {
            showModalBox("Auto Approve Bank Status Successfully !", 1);
            tbl_main.$("select[id^=cell-][id$=-publish]").val(1);
        }
    }).fail(function(){
        showModalBox("Toggle Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function exportGameRecord(){
    window.location = adminURL + "exportGameRecord?search=" + $("#game-search").val() + "&date=" + $("#game-date").val();
}

function exportEventGameRecord(){
    window.location = adminURL + "exportEventGameRecord?id=" + $("#event-id").val() + "&search=" + $("#event-search").val() + "&date=" + $("#event-date").val();
}

function getEventTime(obj) {
    $.ajax({
        method: "POST",
        url: adminURL + "getEventTime",
        dataType: "json",
        data: { id: $(obj).val() }
    }).done(function(rs){
        if(!rs.status){
            showModalBox(rs.message, 0);
        } else {
            var eventDate = $("#event-date");
            var splitDate = rs.date.split(" ~ ");
            eventDate.val(rs.date);
            eventDate.daterangepicker({
                format: "YYYY-MM-DD",
                separator: " ~ ",
                startDate: splitDate[0],
                endDate: splitDate[1]
            });
        }
    }).fail(function(){
        showModalBox("Get Failed. Please try again !", 0);
    }).always(function(){
        main_loading.hide();
    });
}

function changeCheckboxToSwitchery() {
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
    var elemsLength = elems.length;
    for (var i = 0; i < elemsLength; i++) {
        new Switchery(elems[i]);
    }
}

function updateAdvertiseStatus(obj) {
    $.ajax({
        method: "POST",
        url: adminURL + "updateAdvertiseStatus",
        dataType: "json",
        data: { id: obj.name, status: obj.checked }
    }).done(function(rs){
        if (!rs.status) {
            var parent = $(obj).parent();
            var check = !obj.checked ? 'checked' : '';
            parent.html('<input name="' + obj.name + '" type="checkbox" class="js-switch" onchange="updateAdvertiseStatus(this);" ' + check + '/>');
            new Switchery(document.querySelector('.js-switch:not([data-switchery=true])'));
        }
    }).fail(function(){
        var parent = $(obj).parent();
        var check = !obj.checked ? 'checked' : '';
        parent.html('<input name="' + obj.name + '" type="checkbox" class="js-switch" onchange="updateAdvertiseStatus(this);" ' + check + '/>');
        new Switchery(document.querySelector('.js-switch:not([data-switchery=true])'));
    });
}

function updateEventAdvertiseStatus(obj) {
    $.ajax({
        method: "POST",
        url: adminURL + "updateEventAdvertiseStatus",
        dataType: "json",
        data: { id: obj.name, status: obj.checked }
    }).done(function(rs){
        if (!rs.status) {
            var parent = $(obj).parent();
            var check = !obj.checked ? 'checked' : '';
            parent.html('<input name="' + obj.name + '" type="checkbox" class="js-switch" onchange="updateEventAdvertiseStatus(this);" ' + check + '/>');
            new Switchery(document.querySelector('.js-switch:not([data-switchery=true])'));
        }
    }).fail(function(){
        var parent = $(obj).parent();
        var check = !obj.checked ? 'checked' : '';
        parent.html('<input name="' + obj.name + '" type="checkbox" class="js-switch" onchange="updateEventAdvertiseStatus(this);" ' + check + '/>');
        new Switchery(document.querySelector('.js-switch:not([data-switchery=true])'));
    });
}