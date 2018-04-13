
(function ($) {
    var user_token = '';
    var mainApp = {
        plugin: function(){
            $('#forget_password').validate({
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
                    var email = $.App.getFormInput(form);
                    arr = ['verify_email', email];
                    $.App.handleServerRequest('userHub', arr, function (response) { 
                            if(!isNaN(response)){
                                user_token = response;
                                $('#forget_password').addClass('display-hide');
                                $('#reset_password').removeClass('display-hide');
                            }else{
                                var res = response.split(',')                 
                                $.App.Alert(res[0],res[1]);
                            } 
                    });
                }
            });

            $('#reset_password').validate({
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
                    var pass = $.App.getFormInput(form);
                    pass.id = user_token;
                    arr = ['update_password', pass];
                    $.App.handleServerRequest('userHub', arr, function (response) { 
                        if(isNaN(response)){                           
                            var res = response.split(',');                 
                            $.App.Alert(res[0],res[1]);
                            $('body').on('click', '.sweet-alert button.confirm', function () {
                                if(res) window.location.href = 'login.php';
                            });
                        } 
                    });                 
                },
                rules: {
                    password : {
                        required: true,
                        minlength: 5
                    },
                    confirm_password : {
                        equalTo: "#password",
                    },

                },
                messages: {
                    password: { required:"Password is required", minlength:"Password must be up to 5 character"}, 
                    confirm_password: { equalTo:"Password do not match"},
                    
                }
            });
        }
    }
      
    $(document).ready(function () {
        mainApp.plugin();
    });

}(jQuery));



