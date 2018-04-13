
(function ($) {
    var mainApp = {
        plugin: function(){
            $('#sign_up').validate({
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
                    var user_dt = $.App.getFormInput(form);
                    if (user_dt.username != '' && user_dt.password != '') {
                        arr = ['register_auth', user_dt];
                        $.App.handleServerRequest('userHub', arr, function (response) {  
                            var res = response.split(',')                 
                                $.App.Alert(res[0],res[1]);
                                switch(res[1]){
                                    case 'success':
                                        $.App.clearForm(form);
                                        break;
                                    default:
                                        break;
                                }  
                        });
                    }
                },
                rules: {
                    username : {
                        required: true,
                    },
                    firstname : {
                        required: true,
                    },
                    lastname : {
                        required: true,
                    },
                    password : {
                        required: true,
                        minlength: 5
                    },
                    confirm_password : {
                        equalTo: "#password",
                    },

                },
                messages: {
                    username: "Username is required", firstname: "First name is required", lastname: "Last name is required",
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



