function background(t){$.ajax({url:"/ajax/background",type:"get",cache:!0,data:{date:t},beforeSend:function(){$("header").show(),$("body").css("backgroundImage","none"),$("#copyright").html("Please wait..."),$("#copyrightlink").attr("href","#")},success:function(t){$("body").css("backgroundImage","url('/background/"+moment(t.background_date).format("YYYYMMDD")+".jpg')").waitForImages({waitForAll:!0,finished:function(){$("header").hide()}}),$("#copyright").html(t.copyright),$("#copyrightlink").attr("href",t.copyrightlink)}})}var min=0,max=4;$(document).ready(function(){$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),$('[data-tooltip="true"]').tooltip(),$("[data-mask]").inputmask(),$("header").hide(),background(min)}),$("form").submit(function(){$("[data-mask]").inputmask("remove"),$("#member_id").val(parseInt($("#member_id").val()))}),$("#previous").click(function(){$date=parseInt($(this).attr("data-selected"),10)<max?parseInt($(this).attr("data-selected"),10)+1:min,$(this).attr("data-selected",$date),$("#next").attr("data-selected",$date),background($date)}),$("#next").click(function(){$date=parseInt($(this).attr("data-selected"),10)>min?parseInt($(this).attr("data-selected"),10)-1:max,$("#previous").attr("data-selected",$date),$(this).attr("data-selected",$date),background($date)});