
(function ($) {
    var mainApp = {
        plugin: function(){
            $('#sign_in').validate({
                highlight: function (input) {
                    $(input).parents('.form-line').addClass('error');
                },
                unhighlight: function (input) {
                    $(input).parents('.form-line').removeClass('error');
                },
                errorPlacement: function (error, element) {
                    $(element).parents('.input-group').append(error);
                },
                submitHandler: function(form){
                    var user_dt = {};
                    $('input', $(form)).each(function (i, o) {
                        var inputText = $(o).val();
                        var nameAttr = $(o).attr('name');
                        user_dt[nameAttr] = inputText;
                    });
                    if (user_dt.username != '' && user_dt.password != '') {
                        arr = ['login_auth', user_dt];
                        $.App.handleServerRequest('userHub', arr, function (response) {                           
                            
                            if (/^{/.test(response)/*$.type(JSON.parse(response)) == "object"*/) {
                                var res = JSON.parse(response);
                                res.pword = btoa(user_dt.password);
                                $.App.loadDefaultPage(res);
                            } else {
                                $('.msg').removeClass('hidden').find('button').siblings('span').html(response);
                            }

                        })

                    }
                },
                messages: {
                    username: "Username is required",
                    password: "Password is required",
                }
            });
        }
    }
      
    $(document).ready(function () {
        mainApp.plugin();
    });

}(jQuery));



