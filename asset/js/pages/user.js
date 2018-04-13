
(function ($) {
    var hubName = 'userHub';
    $scope.NewRecord.selectedPage = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('user', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('user'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.load_dropdown('#role_drop',['role_list'],'Please select role to map user');
                        mainPage.fetchRecord();
                    }, 50);
                });
            });
        },
        load_dropdown: function(el,arr,placeholder){
            //fetch user dropdowns            
            $.App.handleServerRequest(hubName, arr, function (response) {
                var dt = JSON.parse(response);        
                if(dt.length){           
                    dt = _.sortBy(dt,'drop_text');
                    pageList = dt; //set page to list
                    var template = '{{#each data }}<option value="{{drop_value}}" data-page="{{default_page}}" data-page-id="{{default_page_id}}">{{drop_text}}</option>{{/each}}';   
                    $(el).html('<option value="" disabled>'+placeholder+'</option>').append(Handlebars.compile(template)({data:dt})); 
                    $(el).selectpicker('refresh');
                }
            });   
        },
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['select'], function (response) {
                // console.log("select => ",response);
                $scope.savedRecords = JSON.parse(response).map(function(o,i,a){
                    o.user_active = (o.user_active === 1) ? 'Yes':'No';
                    return o;
                });
                var rawData = {
                    "HEADERS": [{
                        "DT_HEADER": "S/N,User Name, Active, First Name, Last Name, Email, Phone Number, Posted By, Date Created, Action,",
                        "DT_COLUMN": [{ "mDataProp": "null" }, { "mDataProp": "user_name" }, { "mDataProp": "user_active" }, { "mDataProp": "user_first_name" }, { "mDataProp": "user_last_name" }, 
                        { "mDataProp": "user_email" }, { "mDataProp": "user_phone_number" }, 
                        { "mDataProp": "posted_by" }, { "mDataProp": "date_created" }, { "mDataProp": null }, { "mDataProp": null }],
                        "DT_HEADER_ALIGN": "center,left,center,left,left,left,center,center,center,center,",
                        "DT_DRILL_CODE": "", "DT_DRILL_PARENT": ""
                    }],
                    "SAVED": $scope.savedRecords
                }
                $.App.loadRecord(rawData);
            }); 
              
        },
        resetForm: function () {            
            mainPage.fetchRecord();
        },
        acc_pt: function($el,action){
            var a = {
                
                handleForm : function(){
                    var scope = {
                        uname : $("#username").val(), fname : $("#firstname").val(), lname : $("#lastname").val(), email : $("#email").val(), phone : $("#phone").val(),
                        pword : $("#password").val(), cpword : $("#confirm_password").val(),
                        rid : $("#role_drop").selectpicker('val'), user_active : $('input[name="active"]:checked').val(), 
                        cuid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,
                    }
                    return scope;
                },
                clearForm: function(){
                    $.App.clearForm('form#user');
                    $('#active_no').prop('checked',true);
                    $('#role_drop').selectpicker('val',[]);
                    $('#page_drop').selectpicker('val',[]);
                    $('#save_btn,#clear_btn').removeClass('display-hide'); $('#update_btn,#close_btn').addClass('display-hide');                   
                },
                closeForm: function(){
                    a.clearForm();
                    $('#save_btn,#clear_btn').removeClass('display-hide'); $('#update_btn,#close_btn').addClass('display-hide');
                    $('[data-page="grid-view"]').click();
                },
                dbRequest : function(){
                    switch($el.data("act-type")){
                        case "save":
                        case "update":
                            var obj = a.handleForm();
                            if(obj.uname.length == 0 || obj.fname.length == 0 || obj.lname.length == 0 || obj.email.length == 0 || obj.phone.length == 0 || obj.pword.length == 0 || obj.rid.length == 0 ) 
                                return $.App.Alert("Empty field cannot be submitted","warning");
                            if(obj.pword !== obj.cpword) 
                                return $.App.Alert("Password do not match","info");
                            console.log(obj); 
                            var arr = [$el.data("act-type"), obj];
                            if($el.data("act-type") == "update"){
                                obj.uid = $scope.NewRecord.user_id;
                                obj.dc = $.App.getCurrentDate();
                            } 
                            $.App.handleServerRequest(hubName, arr, function (response) {
                                // console.log(response)
                                var res = response.split(',')                 
                                $.App.Alert(res[0],res[1]);
                                switch(res[1]){
                                    case 'success':
                                        a.clearForm();
                                        mainPage.fetchRecord();
                                        break;
                                    default:
                                        break;
                                }       
                            });
                        break;
                        case "edit":
                            var index = $el.closest('tr').find('td:first').find('div').data('sn'), actiontype = $el.data('action')
                            $scope.NewRecord = $scope.savedRecords[index];
                            $scope.NewRecord.user_active == "Yes" ? $('#active_yes').prop('checked',true) : $('#active_no').prop('checked',true);                            
                            $("#username").val($scope.NewRecord.user_name); $("#firstname").val($scope.NewRecord.user_first_name); $("#lastname").val($scope.NewRecord.user_last_name);
                            $("#email").val($scope.NewRecord.user_email); $("#phone").val($scope.NewRecord.user_phone_number); 
                            $("#role_drop").selectpicker('val', $scope.NewRecord.role_id);
                            //call server for page list and set default page
                            $('#page').val($("#role_drop").find('option:selected').data('page'));
                            //set button
                            $('#save_btn,#clear_btn').addClass('display-hide'); $('#update_btn,#close_btn').removeClass('display-hide');
                            $('[data-page="form-view"]').click();
                        break;
                        case "delete":
                            var index = $el.closest('tr').find('td:first').find('div').data('sn'), actiontype = $el.data('action')
                            $scope.NewRecord = $scope.savedRecords[index];
                            var arr = [$el.data("act-type"), 
                                        {uid : $scope.NewRecord.user_id, cuid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,}];

                            $.App.Confirm("Do you want to proceed with the following action?", (res)=>{
                                if(res){
                                    $.App.handleServerRequest(hubName, arr, function (response) {
                                        
                                        var res = response.split(','); 
                                        console.log(res)                
                                        $.App.Alert(res[0],res[1]);
                                        switch(res[1]){
                                            case 'success':
                                                mainPage.fetchRecord();
                                                break;
                                            default:
                                                break;
                                        }       
                                    });
                                }
                            });

                        break;
                    }                    
                },
            }
            return a[action];
        },
        page_event: function(){

            $('body').on('click', '[data-action]', function(){
                var $el = $(this), action = $(this).data('action');
                mainPage.acc_pt($el,action)();
            });

            $('#role_drop').on('change', function(){
                var $el = $(this);
                if($el.val() == "") {
                    $('#page').selectpicker('val','');
                    return;
                }
                console.log($el.find('option:selected').data('page'));
                $('#page').val($el.find('option:selected').data('page'));
                // mainPage.load_dropdown('#page_drop',['role_page_access', {rid:$el.val()}],'Please user default page');
            })
            
        }
    }
      
    $(document).ready(function () {
        mainPage.page_event();
        mainPage.init();
    });

}(jQuery));



