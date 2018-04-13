
(function ($) {
    var hubName = 'ticketHub', pageList = [], editableIndex = '', pgTemp = $('#unit_tb').html(), tbody = $('#unitTb tbody'), tr = $('#unitTb tbody').find('tr').eq(0);
    $scope.NewRecord.unit_list = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            console.log("user ()=> ", user)
            $.App.page_access_logic(user, function(res){
                $.App.setActive('ticket', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('ticket'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.load_dropdown('#event_drop',['event_list'],'Select Event');
                        mainPage.load_dropdown('#unit_drop',['unit_list'],'Select Unit');
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
                    var template = '{{#each data }}<option value="{{drop_value}}" data-custom="{{custom}}">{{drop_text}}</option>{{/each}}';   
                    $(el).html('<option value="" disabled selected>'+placeholder+'</option>').append(Handlebars.compile(template)({data:dt})); 
                    $('select:not(.ms)').selectpicker('refresh');
                }
            });   
        },
        load_unit_table: function(arr){
            if(arr.length){
                var context = { unit_list : arr};
                var htmlstr =  Handlebars.compile(pgTemp)(context);
                tbody.html(tr).append(htmlstr);
            }else{
               tbody.html(tr);
            }
            $('.count').text(arr.length);
        },
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['select'], function (response) {
                $scope.savedRecords = JSON.parse(response).map(function(o,i,a){
                    o.ticket_active = (o.ticket_active === 1) ? 'Yes':'No';
                    return o;
                });
                var rawData = {
                    "HEADERS": [{
                        "DT_HEADER": "S/N,Name, Code, Active, Posted By, Date Created, Action,",
                        "DT_COLUMN": [{ "mDataProp": "null" }, { "mDataProp": "ticket_name" }, { "mDataProp": "ticket_code" }, { "mDataProp": "ticket_active" }, 
                        { "mDataProp": "posted_by" }, { "mDataProp": "date_created" }, { "mDataProp": null }, { "mDataProp": null }],
                        "DT_HEADER_ALIGN": "center,center,left,center,left,center,center,",
                        "DT_DRILL_CODE": "", "DT_DRILL_PARENT": ""
                    }],
                    "SAVED": $scope.savedRecords
                }
                $.App.loadRecord(rawData);
            }); 
              
        },
        fetchUnitList: function(ticket,cb){
            $.App.handleServerRequest(hubName,['ticket_unit_list',{tid:ticket}], function (response) {
                (cb && typeof cb !== undefined) && cb(JSON.parse(response));
            });            
        },
        resetForm: function () {            
            mainPage.fetchRecord();
        },
        acc_pt: function($el,action){
            var a = {
                addUnit: function(){
                    var obj = {};
                    obj.unit_id = $('#unit_drop').val(); obj.unit_name = $('#unit_drop').find('option:selected').text(); obj.price = $('#unit_price').val();
                     obj.discount = $('#unit_discount').val(); obj.quantity = $('#unit_quantity').val();  
                     console.log(obj.unit_id)
                    if((obj.unit_id == null || obj.unit_id == '') || obj.quantity == 0|| obj.price == 0) return $.App.Alert("Fill in the required unit fields","warning");
                    switch($el.text()){
                        case 'Add Line':                                            
                            $scope.NewRecord.unit_list.push(obj);
                            mainPage.load_unit_table($scope.NewRecord.unit_list);
                            $('#unit_drop').val(''); 
                            $('#unit_price').val(''); $('#unit_discount').val(''); $('#unit_quantity').val('');
                        break;
                        case 'Update Line':
                            $scope.NewRecord.unit_list[editableIndex].unit_id = obj.name;
                            $scope.NewRecord.unit_list[editableIndex].price = obj.price;
                            $scope.NewRecord.unit_list[editableIndex].discount = obj.discount;
                            $scope.NewRecord.unit_list[editableIndex].quantity = obj.quantity;
                            mainPage.load_unit_table($scope.NewRecord.unit_list);
                            editableIndex = '';
                            $('#unit_drop').val(''); 
                            $('#unit_price').val(''); $('#unit_discount').val(''); $('#unit_quantity').val('');
                            $('[data-action="addUnit"]').text('Add Line');
                        break;
                    }
                    
                },
                editUnit: function(){
                    editableIndex = $el.closest('tr').data('index');
                    var obj = $scope.NewRecord.unit_list[editableIndex];
                    $('#unit_drop').val(obj.unit_id); $('#unit_price').val(obj.price); $('#unit_discount').val(obj.discount); $('#unit_quantity').val(obj.quantity);
                    $('[data-action="addUnit"]').text('Update Line');
                },
                delUnit: function(){
                    index = $el.closest('tr').data('index');
                    $scope.NewRecord.unit_list.splice(parseInt(index),1);
                    mainPage.load_unit_table($scope.NewRecord.unit_list);
                },
                setQuantity: function(){
                    var value = $el.find('option:selected').data('custom');
                    console.log(value);
                    $('#unit_quantity').val(value);
                },
                handleForm : function(){
                    var scope = {
                        event_id : $("#event_drop").selectpicker('val'), code : $("#code").val(), name : $("#name").val(), tactive : $('input[name="active"]:checked').val(),
                        unit_list : $scope.NewRecord.unit_list,
                        uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,
                    }
                    return scope;
                },
                clearForm: function(){
                    $scope.NewRecord.unit_list = [];
                    $.App.clearForm('form#ticket');
                    $('#active_no').prop('checked',true);
                    $('#event_drop').selectpicker('val',[]);
                    mainPage.load_unit_table($scope.NewRecord.unit_list);
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
                            console.log('form data ', obj);
                            if(obj.code.length == 0 || obj.name.length == 0 || obj.event_id.length == 0) return $.App.Alert("Empty field cannot be submitted","warning");
                            if(obj.unit_list.length == 0) return $.App.Alert("Add atleast a unit to ticket","info");
                            var arr = [$el.data("act-type"), obj];
                            if($el.data("act-type") == "update") {
                                obj.tid = $scope.NewRecord.ticket_id;
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
                            $scope.NewRecord.ticket_active == "Yes" ? $('#active_yes').prop('checked',true) : $('#active_no').prop('checked',true);                            
                            $('#code').val($scope.NewRecord.ticket_code); $('#name').val($scope.NewRecord.ticket_name); 
                            $('#event_drop').selectpicker('val',$scope.NewRecord.event_id);
                            //call server for page list
                            mainPage.fetchUnitList($scope.NewRecord.ticket_id, function(res){
                                $scope.NewRecord.unit_list = res;
                                mainPage.load_unit_table($scope.NewRecord.unit_list);
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
                                        {tid : $scope.NewRecord.ticket_id, uid: $.App.getUID(), pid: $.App.getCurrentPage().page_id,}];

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

            $('body').on('click', '#unit_drop', function(){
                var $el = $(this), action = $(this).data('main');
                mainPage.acc_pt($el,action)();
            });
            
        }
    }
      
    $(document).ready(function () {
        mainPage.page_event();
        mainPage.init();
    });

}(jQuery));



