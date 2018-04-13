
(function ($) {
    var hubName = 'unitHub';
    $scope.NewRecord.selectedPage = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('unit', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('unit'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.fetchRecord();
                    }, 50);
                });
            });
        },
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['select'], function (response) {
                // console.log("select => ",response);
                $scope.savedRecords = JSON.parse(response).map(function(o,i,a){
                    o.unit_active = (o.unit_active === 1) ? 'Yes':'No';
                    return o;
                });
                var rawData = {
                    "HEADERS": [{
                        "DT_HEADER": "S/N,Name,Code,Active,Quantity,Posted By,Date Created,Action,",
                        "DT_COLUMN": [{ "mDataProp": "null" }, { "mDataProp": "unit_name" }, { "mDataProp": "unit_code" }, { "mDataProp": "unit_active" }, 
                        { "mDataProp": "quantity" }, { "mDataProp": "posted_by" }, { "mDataProp": "date_created" }, { "mDataProp": null }, { "mDataProp": null }],
                        "DT_HEADER_ALIGN": "center,left,center,center,center,left,center,center,",
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
                        code : $("#code").val(), name : $("#name").val(), qty : $("#qty").val(), unit_active : $('input[name="active"]:checked').val(), 
                        uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,
                    }
                    return scope;
                },
                clearForm: function(){
                    $.App.clearForm('form#unit');
                    $('#active_no').prop('checked',true);
                    $('#cat_drop').selectpicker('val',[]);
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
                            if(obj.code.length == 0 || obj.name.length == 0 || obj.qty.length == 0 ) 
                                return $.App.Alert("Empty field cannot be submitted","warning");
                            console.log(obj); 
                            var arr = [$el.data("act-type"), obj];
                            if($el.data("act-type") == "update"){
                                obj.unit_id = $scope.NewRecord.unit_id;
                                obj.dc = $.App.getCurrentDate();
                            } 
                            $.App.handleServerRequest(hubName, arr, function (response) {
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
                            $scope.NewRecord.unit_active == "Yes" ? $('#active_yes').prop('checked',true) : $('#active_no').prop('checked',true);                            
                            $("#name").val($scope.NewRecord.unit_name); $("#code").val($scope.NewRecord.unit_code); $("#qty").val($scope.NewRecord.quantity);        
                            //set button
                            $('#save_btn,#clear_btn').addClass('display-hide'); $('#update_btn,#close_btn').removeClass('display-hide');
                            $('[data-page="form-view"]').click();
                        break;
                        case "delete":
                            var index = $el.closest('tr').find('td:first').find('div').data('sn'), actiontype = $el.data('action')
                            $scope.NewRecord = $scope.savedRecords[index];
                            var arr = [$el.data("act-type"), 
                                        {unit_id : $scope.NewRecord.unit_id, uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,}];

                            $.App.Confirm("Do you want to proceed with the following action?", (res)=>{
                                if(res){
                                    $.App.handleServerRequest(hubName, arr, function (response) {                                        
                                        var res = response.split(','); 
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
            
        }
    }
      
    $(document).ready(function () {
        mainPage.page_event();
        mainPage.init();
    });

}(jQuery));



