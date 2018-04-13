
(function ($) {
    var hubName = 'eventHub';
    $scope.NewRecord.selectedPage = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('event', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('event'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.load_dropdown('#hall_drop',['hall_list'],'Select Event/Movie Hall');
                        mainPage.load_dropdown('#tag_drop',['event_category_list'],'Select Event/Movie Tags');
                        mainPage.fetchRecord();
                    }, 50);
                });
            });
        },
        load_dropdown: function(el,arr,placeholder){
            //fetch hall dropdowns            
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
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['select'], function (response) {
                // console.log("select => ",response);
                $scope.savedRecords = JSON.parse(response).map(function(o,i,a){
                    o.event_active = (o.event_active === 1) ? 'Yes':'No';
                    return o;
                });
                var rawData = {
                    "HEADERS": [{
                        "DT_HEADER": "S/N,Name,Code,Hall,Event Date,Event Time,Active,Posted By,Date Created,Action,",
                        "DT_COLUMN": [{ "mDataProp": "null" }, { "mDataProp": "event_name" }, { "mDataProp": "event_code" }, { "mDataProp": "hall_name" }, 
                        { "mDataProp": "event_date" }, { "mDataProp": "event_time" }, { "mDataProp": "event_active" }, { "mDataProp": "posted_by" }, { "mDataProp": "date_created" }, 
                        { "mDataProp": null }, { "mDataProp": null }],
                        "DT_HEADER_ALIGN": "center,left,center,left,center,center,center,center,center,center,",
                        "DT_DRILL_CODE": "", "DT_DRILL_PARENT": ""
                    }],
                    "SAVED": $scope.savedRecords
                }
                $.App.loadRecord(rawData);
            }); 
              
        },
        fetchEventTag: function(event_id,cb){
            $.App.handleServerRequest(hubName,['event_tag_list',{eid:event_id}], function (response) {
                (cb && typeof cb !== undefined) && cb(JSON.parse(response));
            });            
        },
        resetForm: function () {            
            mainPage.fetchRecord();
        },
        acc_pt: function($el,action){
            var a = {
                
                handleForm : function(){
                    var scope = {
                        code : $("#code").val(), name : $("#name").val(), desc : $("#desc").val(),
                        event_active : $('input[name="active"]:checked').val(), hall_id : $("#hall_drop").selectpicker('val'),
                        use_cap : $('input[name="use_cap"]:checked').val(), date : $("#date").val(), time : $("#time").val(), tags : $("#tag_drop").selectpicker('val'), 
                        uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,
                    }
                    return scope;
                },
                clearForm: function(){
                    $.App.clearForm('form');
                    $('input').val(''); $('textarea').val(''); $('.datepicker').val(''); $('.timepicker').val('');
                    $('#active_no').prop('checked',true); $('#cap_no').prop('checked',true);
                    $('#hall_drop').selectpicker('val',[]); $('#tag_drop').selectpicker('val',[]);
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
                            if(obj.code.length == 0 || obj.name.length == 0 || obj.hall_id.length == 0 || obj.date.length == 0 || obj.time.length == 0 ) 
                                return $.App.Alert("Empty field cannot be submitted","warning");
                            
                            var d = new Date(obj.date); nd = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
                            obj.date = nd;
                            obj.tags = (obj.tags == null)? [] : obj.tags;
                            console.log(obj);
                            var arr = [$el.data("act-type"), obj];
                            if($el.data("act-type") == "update"){
                                obj.eid = $scope.NewRecord.event_id;
                                obj.dc = $.App.getCurrentDate();
                            } 
                            $.App.handleServerRequest(hubName, arr, function (response) {
                                var res = response.split(',')                 
                                $.App.Alert(res[0],res[1]);
                                switch(res[1]){
                                    case 'success':
                                        a.clearForm('form');
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
                            $scope.NewRecord.event_active == "Yes" ? $('#active_yes').prop('checked',true) : $('#active_no').prop('checked',true); 
                            $scope.NewRecord.use_hall_capacity == 1 ? $('#cap_yes').prop('checked',true) : $('#cap_no').prop('checked',true);                           
                            $("#name").val($scope.NewRecord.event_name); $("#code").val($scope.NewRecord.event_code); $("#desc").val($scope.NewRecord.event_description);
                            $("#date").val($scope.NewRecord.event_date); $("#time").val($scope.NewRecord.event_time);
                            $("#hall_drop").selectpicker('val', $scope.NewRecord.hall_id); $("#capacity").val($scope.NewRecord.hall_capacity);                             
                            //call server for page list
                            mainPage.fetchEventTag($scope.NewRecord.event_id, function(res){
                                console.log(res);
                                if(res.length){
                                    var arr = res.map(function(o,i,a){
                                        return o.category_id;
                                    });
                                    $("#tag_drop").selectpicker('val', arr);
                                }else{
                                    $("#tag_drop").selectpicker('val', res);
                                }
                            });     
                            //set button
                            $('#save_btn,#clear_btn').addClass('display-hide'); $('#update_btn,#close_btn').removeClass('display-hide');
                            $('[data-page="form-view"]').click();
                        break;
                        case "delete":
                            var index = $el.closest('tr').find('td:first').find('div').data('sn'), actiontype = $el.data('action')
                            $scope.NewRecord = $scope.savedRecords[index];
                            var arr = [$el.data("act-type"), 
                                        {eid : $scope.NewRecord.event_id, uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,}];

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

            $('.btn-upload input').on('change', function () {
                var file_data = this.files[0];
                var imagefile = file_data.type;
                var match = ["image/jpeg", "image/png", "image/jpg"];
                if (!((imagefile == match[0]) || (imagefile == match[1]) || (imagefile == match[2]))) {
                    $.App.Alert('Please select a valid image','warning');
                    return false;
                }
                else {
                    var reader = new FileReader();
                    reader.onload = imageIsLoaded;
                    reader.readAsDataURL(this.files[0]);
                }

                function imageIsLoaded(e) {
                    $('#imgUpload').attr('src', e.target.result);
                    // localStorage.setItem("profilepic", e.target.result);
                    // handleProfile();
                };

                var form_data = new FormData();                  // Creating object of FormData class
                form_data.append("file", file_data);              // Appending parameter named file with properties of file_field to form_data
                form_data.append("uid", $.App.getUID());
                form_data.append("pid", $.App.getCurrentPage().page_id);

                var arr = [{ "IMAGE_UPLOAD": form_data }];
                return;
                $.App.handleServerUpload("upload", arr, function (response) {
                    console.log('upload done');   
                });

            });
            
        }
    }
      
    $(document).ready(function () {
        mainPage.page_event();
        mainPage.init();
    });

}(jQuery));



