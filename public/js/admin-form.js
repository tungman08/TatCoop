$("#genpassword").click(function(){$.ajax({url:"/ajax/password",type:"post",data:{_token:$("input[name='_token']").val()},beforeSend:function(){$("#new_password").val("⌛ กรุณารอสักครู่...")},success:function(a){$("#new_password").val(a)}})});