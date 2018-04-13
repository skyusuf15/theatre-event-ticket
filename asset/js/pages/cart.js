
(function ($) {
    var hubName = 'cartHub', cont = $('#event_li').html(), contWrapper = $('.eventContent'), tlist = $('#cart_preview').html(), cbody = $('#cart_body'), clist = $('#cart_list').html(),
    isVisible = false, orderComplete = false;
    var order_item = (sessionStorage.getItem('cart') !== null) ? JSON.parse(sessionStorage.getItem('cart')) : [], 
     events = [];    
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('cart', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('cart'); //check page access                                       
                    setTimeout(function () { 
                         //fetch event items list
                        $('.page-loader-wrapper').fadeOut();                        
                        mainPage.loadCart(order_item);
                    }, 50);
                });
            });
        },
        loadCart: function (data) {
            //load cart TABLE
            contWrapper.html('');
            var context = { cart : data };
            var htmlstr =  Handlebars.compile(tlist)(context);
            contWrapper.html(htmlstr);

            //load cart popup
            cbody.html('');
            var context = { cart : data };
                var htmlstr =  Handlebars.compile(clist)(context);
                cbody.html(htmlstr);

            (data.length > 0)? $('.checkout').removeClass('display-hide') : $('.checkout').addClass('display-hide');
            (data.length > 0)? $('.sub-total').html('Sub total:&nbsp;<b>' + mainPage.getTotal(data) + '</b>') : $('.sub-total').html('No items in your cart.');

            $('.navbar-right .dropdown-menu .body .menu').slimscroll({
                height: '254px',
                color: 'rgba(0,0,0,0.5)',
                size: '4px',
                alwaysVisible: false,
                borderRadius: '0',
                railBorderRadius: '0'
            });
                
            //compute count
            var total = 0;
            if(data.length){
                data.forEach(function(o,i){
                    total += parseInt(o.qty);
                });
            }
            $('.label-count').html(total);

            //caltotal
            (data.length > 0)? $('.amount').html('Total:&nbsp;<b>' + mainPage.getTotal(data) + '</b>') : $('.amount').html('Total:<b>' + $.App.formatCurrency('0.0') + '</b>');
            (data.length > 0)? $('#proceedPayment').removeClass('display-hide') : $('#proceedPayment').addClass('display-hide');

            sessionStorage.setItem('cart',JSON.stringify(data)); 
            console.log(data)         
            
        },
        plugin: function(){
            $('#payment_form').validate({
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
                        user_dt.uid = $.App.getUID();
                        user_dt.ticket_qty = order_item.length;
                        user_dt.total_amount = mainPage.computeTotal(order_item);
                        user_dt.order_list = order_item;

                        //call payment API and use a success callback to save order to DB

                    arr = ['order_ticket', user_dt];
                    $.App.handleServerRequest(hubName, arr, function (response) {
                        var res = response.split(',');                 
                        $.App.Alert(res[0],res[1]);
                        switch(res[1]){
                            case 'success':
                                $.App.clearForm(form);
                                orderComplete = true;
                                order_item = [];
                                sessionStorage.setItem('cart',JSON.stringify(order_item));
                                break;
                            default:
                                break;
                        }  
                    });
                },
                rules: {
                    card_name : {
                        required: true,
                    },
                    card_number : {
                        required: true,
                    },
                    cvc : {
                        required: true,
                        minlength: 3,
                        maxlength: 3
                    },
                    month : {
                        required: true,
                        minlength: 2,
                        maxlength: 2
                    },
                    year : {
                        required: true,
                        minlength: 4,
                        maxlength: 4
                    },
                    pin : {
                        required: true,
                        minlength: 4,
                        maxlength: 4
                    },

                },
                messages: {
                    card_name: "Card name is required", card_number: "Card number is required", cvc: "CVC is required",
                    month: { required:"Month is required", minlength:"Month must be up to 2 character e.g 01 i.e january", maxlength:"Month must be up to 2 character"}, 
                    year: { required:"Year is required", minlength:"Year must be up to 4 character e.g 1999", maxlength:"Year must be up to 4 character e.g 1999"},
                    confirm_password: { equalTo:"Password do not match"},
                    
                }
            });
        },
        getTotal: function(data){
            if(data.length == 0) return '';
            var total = 0;
            data.forEach(function(o,i){
                total += parseInt(o.total);
            });
            return $.App.formatCurrency(total);
        },
        computeTotal: function(data){
            if(data.length == 0) return '';
            var total = 0;
            data.forEach(function(o,i){
                total += parseInt(o.total);
            });
            return total;
        },
        acc_pt: function($el,action){
            var a = {
                delTicket: function(){
                    var $el = $(this),index = $el.closest('tr').data('index');                    
                    $.App.Confirm("Do you want to delete this item?", (res)=>{
                        if(res){
                            order_item.splice(parseInt(index),1);  
                            //reload cart
                            mainPage.loadCart(order_item);
                        }
                    });                 
                    
                }
            }
            return a[action];
        },
        page_event: function(){

            $('body').on('click', '[data-action]', function(){
                var $el = $(this), action = $(this).data('action');
                mainPage.acc_pt($el,action)();
            });

            $('body').on('keypress', '.qty-input,#card_number,#cvc,#month,#year,#pin', function(e){
                return $.App.isNumberKey(e);
            });

            $('body').on('change', 'input.qty-input', function(e){
                    var $el = $(this),index = $el.closest('tr').data('index'),
                    obj = order_item[parseInt(index)];
                    
                    qty = parseInt($el.val()); //get val

                    if(qty.length == 0) return false;

                    obj.qty = qty;
                    obj.total = (qty * obj.price);
                    obj.timestamp = new Date().getTime();

                    //update index
                    order_item[parseInt(index)] = obj;

                    console.log(order_item[parseInt(index)]);
                    
                     //reload cart
                     mainPage.loadCart(order_item);
            });

            $('body').on('click', '.sweet-alert button.confirm', function () {
                if(orderComplete) window.location.reload();
            });
                        
        }
    }
      
    $(document).ready(function () {
        mainPage.init();
        mainPage.page_event();
        mainPage.plugin();
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



