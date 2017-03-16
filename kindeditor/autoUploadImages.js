function df() {
    var haspicContainer = document.getElementById("kindeditor_has_pic");
    if (haspicContainer == null) {
        haspicContainer = document.createElement("div");
        haspicContainer.id = "kindeditor_has_pic";
        haspicContainer.innerHTML = "<input type='text' id='kindeditor_piclist' value='' style='display:none;'/><div id='kindeditor_upload_notice'><b>您有图片需要上传到服务器</b>&nbsp;&nbsp;<a href='javascript:uploadpic();' >上传</a></div><div id='kindeditor_confirm_notice'></div>";
        $(".ke-toolbar").after(haspicContainer);
    }
    var img = $(".ke-edit-iframe").contents().find("img");
    var piccount = 0;
    var sstr = "";
    $(img).each(function (i) {
        var that = $(this);
        //console.log(that.attr("src").indexOf(window.location.host));
        if ( (that.attr("src").indexOf("http://") >= 0 || that.attr("src").indexOf("https://") >= 0) && that.attr("src").indexOf(window.location.host) == -1 ) {
            piccount++;
            if (i == $(img).length - 1)
                sstr += that.attr("src");
            else
                sstr += that.attr("src") + "|";
        }
    });
    if(sstr.length > 2) $("#kindeditor_upload_notice").show();
    $("#kindeditor_piclist").val(sstr);
    document.getElementById("kindeditor_has_pic").style.display = (piccount > 0) ? "block" : "none";
}

function closeupload() {
    $("#kindeditor_confirm_notice").hide();
}

function uploadpic() {
    var piclist = encodeURI($("#kindeditor_piclist").val());
    if (piclist.length == 0) return false;
    $.ajax({
        dataType: "json",
        url: "/misc/kindeditor/php/autoUploadImages.php",           //config
        data: "pic=" + encodeURIComponent(piclist),
        type: "POST",
        beforeSend: function () {
            $("#kindeditor_confirm_notice").show();
            $("#kindeditor_upload_notice").hide();
            $("#kindeditor_confirm_notice").text("正在上传中...");
        },
        success: function (json)
        {
            if (0==json.error) {
                var str = new Array();
                str = json.url.split('|');
                var img = $(".ke-edit-iframe").contents().find("img");

                $(img).each(function (i) {
                    var that = $(this);
                    if (that.attr("src").indexOf("http://") >= 0 || that.attr("src").indexOf("https://") >= 0) {
                        that.attr("src", str[i]);
                        that.attr("data-ke-src", str[i]);
                    }
                });
                $("#kindeditor_confirm_notice").html(img.length + "张图片已经上传成功！&nbsp;&nbsp;<a href='javascript:closeupload();'>关闭</a>");
            }else{
                $("#kindeditor_confirm_notice").text("上传失败！");
            }
        }
    });
}
