
(function ($) {
    var hubName = 'my_ticketHub', cont = $('#event_li').html(), contWrapper = $('.eventContent'), tlist = $('#order_preview').html(), 
    cbody = $('#cart_body'), clist = $('#cart_list').html(),
    tdetail = $('#ticket_detail').html();
    isVisible = false, orderComplete = false;
    var order_item = (sessionStorage.getItem('cart') !== null) ? JSON.parse(sessionStorage.getItem('cart')) : [], 
     events = [];    
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('my_ticket', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('my_ticket'); //check page access                                       
                    setTimeout(function () {                            
                        $('.page-loader-wrapper').fadeOut(); 
                        mainPage.fetchRecord();
                    }, 50);
                });
            });
        },
        fetchRecord: function(){
            $.App.handleServerRequest(hubName,['order_list',{uid:$.App.getUID()}], function (response) {
                $scope.savedRecords = JSON.parse(response);
                mainPage.loadTable($scope.savedRecords);
            });            
        },
        loadTable: function(data){
            //load cart popup
            contWrapper.html('');
            var context = { order : data };
            var htmlstr =  Handlebars.compile(tlist)(context);
            contWrapper.html(htmlstr);

            //load cart popup
            cbody.html('');
            var context = { cart : order_item };
            var htmlstr =  Handlebars.compile(clist)(context);
            cbody.html(htmlstr);
        },
        resetForm: function () {            
            mainPage.fetchRecord();
        },
        acc_pt: function($el,action){
            var a = {
                viewDetail: function(){
                    var index = $el.closest('tr').data('index'), tr = $el.closest('tr');    
                    var $btn = $('button[data-action="viewDetail"]');             
                    //get ticket list
                    var ticket_list = $scope.savedRecords[index].unit_list;
                    if(!tr.next('.detail').is(':visible')){
                        $('.detail').remove(); $btn.find('i').html('view_list'); $btn.addClass('btn-primary').removeClass('btn-danger');
                        var context = { cart : ticket_list };
                        var htmlstr =  Handlebars.compile(tdetail)(context);
                        tr.after("<tr class='detail animated zoomIn'><td colspan='7'>"+ htmlstr +"</td></tr>");
                        $el.find('i').html('clear'); $el.removeClass('btn-primary').addClass('btn-danger');
                    }else{
                        tr.next('.detail').remove();
                        $el.find('i').html('view_list'); $el.addClass('btn-primary').removeClass('btn-danger');
                    }
                    
                }
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
        mainPage.init();
        mainPage.page_event();
        //Activate notification and task dropdown on top right menu
        (function() {
            $('.navbar-right .dropdown-menu .body .menu').slimscroll({
                height: '254px',
                color: 'rgba(0,0,0,0.5)',
                size: '4px',
                alwaysVisible: false,
                borderRadius: '0',
                railBorderRadius: '0'
            });
        }());

    });

}(jQuery));



