
(function ($) {
    var hubName = 'exploreHub', cont = $('#event_li').html(), contWrapper = $('#eventContent'), clist = $('#cart_list').html(), cbody = $('#cart_body'), 
    isVisible = false;
    var order_item = (sessionStorage.getItem('cart') !== null) ? JSON.parse(sessionStorage.getItem('cart')) : [], 
    events = [];
    var mainPage = {
        init: function(){
            var user = $.App.getUser();
            $.App.page_access_logic(user, function(res){
                $.App.setActive('explore', function(dt){ //set active page class
                    $.App.display_menu(dt,user); //handle menuu
                    $.App.setPageAcess('explore'); //check page access                                       
                    setTimeout(function () {                          
                       
                        mainPage.fetchRecord(function(res){
                            events = res;
                            mainPage.eventList(res);
                            mainPage.reloadCart(order_item);
                            $('.page-loader-wrapper').fadeOut();
                        }); //fetch event items list

                    }, 50);
                });
            });
        },
        fetchRecord: function(cb){
            $.App.handleServerRequest(hubName,['event_list'], function (res) {
                (cb && cb !== undefined && typeof cb == 'function') && cb(JSON.parse(res));
            });               
        },
        displayList: function (data, pageLength, startIndex) {
            //draw ui here and paginate
            contWrapper.html('');
            if (Array.isArray(data) && data.length > 0) {
                var context = { 
                    events : (function(dt){
                        if(dt && dt.length){
                            dt.forEach(function(o,i,a){
                                o.cindex = (startIndex + i); //add index for access tmplate
                                o.unit_list.forEach(function(v,k){
                                    var exist = mainPage.itemExist(order_item,o.tid,v.name);
                                    if(exist !== ''){
                                        v.total_count = parseInt(order_item[exist].qty);
                                    }
                                    v.nocount = (v.total_count >= 1)? false : true;         
                                });
                            });
                            return dt;
                        }
                    })(data)
                };
                console.log('context => ', context);
                var htmlstr =  Handlebars.compile(cont)(context);
                contWrapper.html(htmlstr);
            } else {
                contWrapper.html('<p>No event for now.</p>');
            }   
            return {
                paginate: function ($pagination) {
                    var tmp = "{{#pages}}<li class=\"pagination-items\" data-pg=\"{{this}}\"><a href=\"javascript:void(0);\">{{this}}</a></li>\n{{/pages}}", 
                    $pages = $(".pagination-items", $pagination);
                    $pages.length && $pages.remove();
                    $(".pagination-prev", $pagination).after(Handlebars.compile(tmp)({ pages: Array.from(new Array(pageLength)).map(function (el, i) { return i + 1 }) }));
                    $(".pagination-items:first", $pagination).addClass("active");
                }
            }
        },
        eventList: function (events) {
            var _pageLength, _lastIndex, _startIndex, currentPage = 1; pageSize = 6, $pagination = $("ul.pagination");

            if (events == undefined) events = [];

            pageData = function () {
                var dLength = events.length, pageLength = (dLength % pageSize == 0) ? dLength / pageSize : Math.floor(dLength / pageSize) + 1;
                startIndex = (currentPage - 1) * pageSize,
                lastIndex = (pageSize * currentPage) - 1;
                lastIndex = lastIndex < dLength ? lastIndex : dLength;
                (_pageLength = pageLength, _startIndex = startIndex, _lastIndex = lastIndex);
                $(".list-info").html(["Showing", !lastIndex ? startIndex : startIndex + 1, "to", lastIndex == dLength ? lastIndex : lastIndex + 1, "of", dLength, "events"].join(" "));
                return events.slice(startIndex, lastIndex + 1);
            };
            $(".pg-con").off("click").on("click", "li.pagination-items,li.pagination-prev,li.pagination-next", function () {
                var $el = $(this), currPg = $el.data("pg"),
                    $pagination = $el.closest('ul.pagination'),
                    $pages = $(".pagination-items", $pagination);
                if ($el.is(".pagination-items")) {
                    if ($el.hasClass("active")) return;
                    $pages.removeClass("active");
                    $el.addClass("active");
                    $el.siblings(".pagination-prev").toggleClass("disabled", (currPg == 1));
                    $el.siblings(".pagination-next").toggleClass("disabled", (currPg == _pageLength));
                    currentPage = currPg;
                    var __data = pageData();
                    mainPage.displayList(__data, _pageLength, _startIndex);
                    window.scrollTo(0, 0);
                } else {
                    if ($el.hasClass("disabled")) return;
                    $pages.filter("[data-pg=\"" + (currentPage + ($el.is(".pagination-prev") ? -1 : 1)) + "\"]").trigger("click");
                }
            });
            var __data = pageData();
            mainPage.displayList(__data, _pageLength, _startIndex).paginate($pagination);
        },
        plugin: function(){
            var height = ($(window).height() - ($('.legal').outerHeight() + $('.user-info').outerHeight() + $('.navbar').innerHeight()));
            var $el = $('.list');

            $el.slimscroll({
                height: height + "px",
                color: configs.scrollColor,
                size: configs.scrollWidth,
                alwaysVisible: configs.scrollAlwaysVisible,
                borderRadius: configs.scrollBorderRadius,
                railBorderRadius: configs.scrollRailBorderRadius
            });

        },
        itemExist: function(arr,tid,un){
            var res = '';
            if(arr.length){
                arr.forEach(function(o,i,a){
                    if(o.tid == tid && o.uname == un){
                        res = i;
                    }
                });
            }
            return res;
        },
        getTotal: function(data){
            if(data.length == 0) return '';
            var total = 0;
            data.forEach(function(o,i){
                total += parseInt(o.total);
            });
            return $.App.formatCurrency(total);
        },
        reloadCart: function(cart){
            //load cart
            cbody.html('');
            var context = { cart : cart };
                var htmlstr =  Handlebars.compile(clist)(context);
                cbody.html(htmlstr);

            (cart.length > 0)? $('.checkout').removeClass('display-hide') : $('.checkout').addClass('display-hide');
            (cart.length > 0)? $('.sub-total').html('Sub total:&nbsp;<b>' + mainPage.getTotal(cart) + '</b>') : $('.sub-total').html('No items in your cart.');

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
            if(cart.length){
                cart.forEach(function(o,i){
                    total += parseInt(o.qty);
                });
            }
            $('.label-count').html(total);

            //set cart
            sessionStorage.setItem('cart',JSON.stringify(cart));
        },
        acc_pt: function($el,action){
            var a = {
                viewTicket: function(){
                    isVisible = true;

                    $('div.ticket-detail-quant').addClass('display-hide');
                    var pindex = $el.closest('.event-item').data('index'),
                        qCont = $el.closest('div.ticket-detail').siblings('div.ticket-detail-quant');
                    cindex = $el.closest('li').data('index');
                        
                    var pobj = events[parseInt(pindex)], title = pobj.title;
                    var cobj = pobj.unit_list[parseInt(cindex)], name = cobj.name, price = $.App.formatCurrency(cobj.price); 

                    //check if already added to cart exist
                    var exist = mainPage.itemExist(order_item,pobj.tid,name);
                    
                    if(exist !== ''){
                       //set data
                        qCont.find('.title').html(name); qCont.find('.price').html(price); qCont.find('.qty-input').val(order_item[exist].qty);
                    }else{
                       //set data
                        qCont.find('.title').html(name); qCont.find('.price').html(price); qCont.find('.qty-input').val('0');
                    } 
                    qCont.removeClass('display-hide'); qCont.animateCss('zoomIn');

                }, 
                adjustQty: function(){
                    var index = $el.closest('.event-item').data('index'),
                     obj = events[parseInt(index)];
                    
                    var uname = $el.data('name'), qtyVal = $el.closest('.input-group-btn').siblings('input.qty-input').val();

                    var qty = ($el.data('pt') == 'add')? /*add*/((qtyVal != '') ? (parseInt(qtyVal) + 1) : 0) 
                                                    : /*sub*/ ((qtyVal != '') ? ((parseInt(qtyVal) != 0) ? (parseInt(qtyVal) - 1) : 0 ): 0);

                    if(qty.length == 0) return false;

                    var cart_item = {
                         eid : obj.eid, tid : obj.tid, ename: obj.title, 
                         uname : obj['unit_list'][cindex].name, qty : qty, 
                         uid : obj['unit_list'][cindex].uid, 
                         price : obj['unit_list'][cindex].price, 
                         total : (qty * obj['unit_list'][cindex].price), 
                         timestamp : new Date().getTime()
                     }

                     //set value
                     $el.closest('.input-group-btn').siblings('input.qty-input').val(cart_item.qty);

                    //display badge-count
                     qty >= 1 ? $el.closest('.event-item').find('[data-badge-name="'+ cart_item.uname.replace(/[' ']/g,'_') +'"]').find('.badge-count').html(cart_item.qty).removeClass('display-hide') 
                                : $el.closest('.event-item').find('[data-badge-name="'+ cart_item.uname.replace(/[' ']/g,'_') +'"]').find('.badge-count').html(cart_item.qty).addClass('display-hide');

                     //check if not exist
                     var exist = mainPage.itemExist(order_item,cart_item.tid,cart_item.uname);
                     if(exist !== ''){
                        if(qty >= 1 ){ order_item[exist] = cart_item }else{ order_item.splice(exist,1)};
                     }else if(qty != 0){
                        order_item.push(cart_item);
                     }                     

                     //reload cart
                     mainPage.reloadCart(order_item);
                },              
                addTicket: function(){
                    
                },
                delTicket: function(){
                    
                },
                handleCart : function(){
                    
                },
                emptyCart: function(){
                                        
                },
                closeCart: function(){
                    
                },
                closePopup: function(){
                    isVisible = false;
                    $('div.ticket-detail-quant').addClass('display-hide');
                    // $('input.qty-input').val('0');
                },
                dbRequest : function(){
                    switch($el.data("act-type")){                        
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

            $('body').on('keypress', '.qty-input', function(e){
                return $.App.isNumberKey(e);
            });

            $('body').on('keydown keyup', 'input.qty-input', function(e){
                    var $el = $(this),index = $el.closest('.event-item').data('index'),
                     obj = events[parseInt(index)];
                    
                    qty = parseInt($el.val()); //get val

                    if(qty.length == 0) return false;

                    var cart_item = {
                        eid : obj.eid, tid : obj.tid, ename: obj.title, 
                        uname : obj['unit_list'][cindex].name, qty : qty, 
                        uid : obj['unit_list'][cindex].uid, 
                        price : obj['unit_list'][cindex].price, 
                        total : (qty * obj['unit_list'][cindex].price), 
                        timestamp : new Date().getTime()
                    }

                     //display badge-count
                     qty >= 1 ? $el.closest('.event-item').find('[data-badge-name="'+ cart_item.uname.replace(/[' ']/g,'_') +'"]').find('.badge-count').html(cart_item.qty).removeClass('display-hide') 
                                : $el.closest('.event-item').find('[data-badge-name="'+ cart_item.uname.replace(/[' ']/g,'_') +'"]').find('.badge-count').html(cart_item.qty).addClass('display-hide');

                     //check if not exist
                     var exist = mainPage.itemExist(order_item,cart_item.tid,cart_item.uname);
                     if(exist !== ''){
                        if(qty >= 1 ){ order_item[exist] = cart_item }else{ order_item.splice(exist,1)};
                     }else{
                        order_item.push(cart_item);
                     }                     

                     //reload cart
                     mainPage.reloadCart(order_item);
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



