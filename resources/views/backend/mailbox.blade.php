<?php
/**
 * Created by IntelliJ IDEA.
 * User: spiritbomb
 * Date: 4/20/2016
 * Time: 3:33 PM
 */
?>

<section class="content-header">
    <h1 style="padding-bottom: 10px">Mailbox</h1>
    <div class="row">
        <div class="box box-primary box-body">
            <div class="col-xs-12">
                <div class="form-group">
                    <label>To <small>(Leave this field to send to all users)</small></label>
                    <input id="provider-json" type="text" placeholder="Leave this field to send to all users" class="form-control"/>
                    <input id="data-holder" type="hidden" name="id"/>
                </div>
                <div class="form-group">
                    <label>Title <small>(maxlength="255")</small></label>
                    <input class="form-control" type="text" name="title" placeholder="Enter title for message" maxlength="255" id="title"/>
                </div>
                <div class="form-group">
                    <label>Content <small>(maxlength="500")</small></label>
                    <textarea class="form-control" placeholder="Enter content for message" rows="5" maxlength="500" id="content"></textarea>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="clear">
                            Clear title and content after send successful
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit" onclick="return mailboxSubmit();">Send</button>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    var AutocompleteOptions = {
        data: {!! $users !!},
        getValue: "fullname",
        list: {
            match: {
                enabled: true
            },
            onSelectItemEvent: function() {
                var value = $("#provider-json").getSelectedItemData().id;
                $("#data-holder").val(value).trigger("change");
            }
        }
    };
    $(document).ready(function(){
        $("#provider-json").easyAutocomplete(AutocompleteOptions);
    });
    function mailboxSubmit(){
        var to = $.trim($("#provider-json").val());
        if(to != ""){
            var flag = false;
            for(var i = 0; i < AutocompleteOptions.data.length; ++i){
                if(AutocompleteOptions.data[i].fullname == to){
                    flag = true;
                    break;
                }
            }
            if(!flag) {
                showModalBox("User name incorrect", 0); return false;
            }
            to = $("#data-holder").val();
        }
        var title = $.trim($("#title").val());
        if(title == ""){
            showModalBox("Please enter title", 0); return false;
        }
        var content = $.trim($("#content").val());
        if(content == ""){
            showModalBox("Please enter content", 0); return false;
        }
        $.ajax({
            method: "POST",
            dataType: 'json',
            url: "/admin/mailbox/send",
            data: { to: to, title: title, content: content},
            success: function (data){
                if(document.getElementById('clear').checked){
                    $("#title").val("");
                    $("#content").val("");
                }
                if(data.s == 1) showModalBox("Message was sent", 1);
                else showModalBox(data.msg, 0);
            }
        });
        return false;
    }
</script>
