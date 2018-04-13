
(function ($) {
    var hubName = 'roleHub', pageList = [], pgTemp = $('#page_access_tb').html(), tbody = $('#accessTb tbody');
    $scope.NewRecord.selectedPage = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            console.log("user ()=> ", user)
            $.App.page_access_logic(user, function(res){
                $.App.setActive('role', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('role'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.load_dropdown('#role_drop',['page_list'],'Please select pages role can have access to');
                        mainPage.fetchRecord();
                    }, 50);
                });
            });
        },
        load_dropdown: function(el,arr,placeholder){
            //fetch role dropdowns            
            $.App.handleServerRequest(hubName, arr, function (response) {
                var dt = JSON.parse(response);        
                if(dt.length){           
                    dt = _.sortBy(dt,'drop_text');
                    pageList = dt; //set page to list
                    var template = '{{#each data }}<option value="{{drop_value}}">{{drop_text}}</option>{{/each}}';   
                    $(el).html('<option value="" disabled>'+placeholder+'</option>').append(Handlebars.compile(template)({data:dt})); 
                    $(el).selectpicker('refresh');
                }
            });   
        },
        load_access_table: function(arr){
            var no_record = '<tr><td colspan="7" class="text-center">No page access configure.</td></tr> ';
            if(arr.length){
                var context = { pages : arr};
                var htmlstr =  Handlebars.compile(pgTemp)(context);
                tbody.html(htmlstr);
            }else{
               tbody.html(no_record);
            }
        },
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['select'], function (response) {
                $scope.savedRecords = JSON.parse(response).map(function(o,i,a){
                    o.role_active = (o.role_active === 1) ? 'Yes':'No';
                    return o;
                });
                var rawData = {
                    "HEADERS": [{
                        "DT_HEADER": "S/N,Role, Active, Posted By, Date Created, Action,",
                        "DT_COLUMN": [{ "mDataProp": "null" }, { "mDataProp": "role_name" }, { "mDataProp": "role_active" }, 
                        { "mDataProp": "posted_by" }, { "mDataProp": "date_created" }, { "mDataProp": null }, { "mDataProp": null }],
                        "DT_HEADER_ALIGN": "center,left,center,left,center,center,",
                        "DT_DRILL_CODE": "", "DT_DRILL_PARENT": ""
                    }],
                    "SAVED": $scope.savedRecords
                }
                $.App.loadRecord(rawData);
            }); 
              
        },
        fetchRoleAccess: function(role,cb){
            $.App.handleServerRequest(hubName,['role_page_access',{rid:role}], function (response) {
                (cb && typeof cb !== undefined) && cb(JSON.parse(response));
            });            
        },
        resetForm: function () {            
            mainPage.fetchRecord();
        },
        acc_pt: function($el,action){
            var a = {
                addPages: function(){
                    var pages = $('#role_drop').selectpicker('val'), tempArr = [], appID = [];
                    if (pages == null) return;
                    if ($scope.NewRecord.selectedPage.length > 0) appID = $scope.NewRecord.selectedPage.map(function(o,i,a){ return o.page_id});
                    var arr = JSON.parse(JSON.stringify(pageList));
                    arr.filter(function(o,i,a){
                        if(_.indexOf(pages,""+o.page_id+"") !== -1 /*exist in pages*/ && _.indexOf(appID,o.page_id) == -1 /*not in selected app*/){
                            tempArr.push(o);
                        }
                    });
                    //load table
                    $scope.NewRecord.selectedPage = $scope.NewRecord.selectedPage.concat(tempArr);
                    mainPage.load_access_table($scope.NewRecord.selectedPage);
                },
                delAccess: function(){
                    index = $el.closest('tr').data('index');
                    $scope.NewRecord.selectedPage.splice(parseInt(index),1);
                    mainPage.load_access_table($scope.NewRecord.selectedPage);
                    //remove deleted pages from selected apps
                    if ($scope.NewRecord.selectedPage.length == 0){
                        $('#role_drop').selectpicker('val',[]);
                        return;
                    }                     
                    var dt = $scope.NewRecord.selectedPage.map(function(o,i,a){
                            return o.page_id;
                    });
                    $('#role_drop').selectpicker('val',dt);
                },
                handleForm : function(){
                    var scope = {
                        role_name : $("#role_name").val(),
                        role_active : $('input[name="active"]:checked').val(),
                        dpid: $('#accessTb input[name="default_page_id"]:checked').val(),
                        pages : $scope.NewRecord.selectedPage,
                        uid: $.App.getUID(),
                        pid: $.App.getCurrentPage().page_id,
                    }
                    return scope;
                },
                clearForm: function(){
                    $scope.NewRecord.selectedPage = [];
                    $.App.clearForm('form#role');
                    $('#active_no').prop('checked',true);
                    $('#role_drop').selectpicker('val',[]);
                    mainPage.load_access_table($scope.NewRecord.selectedPage);
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
                            if(obj.role_name.length == 0) return $.App.Alert("Empty field cannot be submitted","warning");
                            if(obj.pages.length == 0) return $.App.Alert("Add atleast a page to role access","info");
                            if(obj.dpid === undefined) return $.App.Alert("Choose a page as role default page","info");
                            var arr = [$el.data("act-type"), obj];
                            if($el.data("act-type") == "update") {
                                obj.rid = $scope.NewRecord.role_id;
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
                            $scope.NewRecord.role_active == "Yes" ? $('#active_yes').prop('checked',true) : $('#active_no').prop('checked',true);                            
                            $('#role_name').val($scope.NewRecord.role_name);
                            //call server for page list
                            mainPage.fetchRoleAccess($scope.NewRecord.role_id, function(res){
                                $scope.NewRecord.selectedPage = res;
                                mainPage.load_access_table($scope.NewRecord.selectedPage);
                                console.log($scope);
                            });
                            //set button
                            $('#save_btn,#clear_btn').addClass('display-hide'); $('#update_btn,#close_btn').removeClass('display-hide');
                            $('[data-page="form-view"]').click();
                        break;
                        case "delete":
                            var index = $el.closest('tr').find('td:first').find('div').data('sn'), actiontype = $el.data('action')
                            $scope.NewRecord = $scope.savedRecords[index];
                            var arr = [$el.data("act-type"), 
                                        {rid : $scope.NewRecord.role_id, uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,}];

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
            
        }
    }
      
    $(document).ready(function () {
        mainPage.page_event();
        mainPage.init();
    });

}(jQuery));



