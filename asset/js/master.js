if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

var appPath = window.location.pathname.substr(1).split("/")[0], profileUpdate = false,
    appUrl = window.location.origin + "/" + appPath + "/", lastDuration, idleTimeout, userInfo, $scope = {};
    $scope.NewRecord = {};
$.App = {};
$.App.options = {
    colors: {
        red: '#F44336',
        pink: '#E91E63',
        purple: '#9C27B0',
        deepPurple: '#673AB7',
        indigo: '#3F51B5',
        blue: '#2196F3',
        lightBlue: '#03A9F4',
        cyan: '#00BCD4',
        teal: '#009688',
        green: '#4CAF50',
        lightGreen: '#8BC34A',
        lime: '#CDDC39',
        yellow: '#ffe821',
        amber: '#FFC107',
        orange: '#FF9800',
        deepOrange: '#FF5722',
        brown: '#795548',
        grey: '#9E9E9E',
        blueGrey: '#607D8B',
        black: '#000000',
        white: '#ffffff'
    },
    leftSideBar: {
        scrollColor: 'rgba(0,0,0,0.5)',
        scrollWidth: '8px',
        scrollAlwaysVisible: false,
        scrollBorderRadius: '0',
        scrollRailBorderRadius: '0',
        scrollActiveItemWhenPageLoad: true,
        breakpointWidth: 1117
    },
    dropdownMenu: {
        effectIn: 'fadeIn',
        effectOut: 'fadeOut'
    }
}
$.App.base_pages = new Array();
$.App.leftSideBar = {
    activate: function () {
        var _this = this;
        var $body = $('body');
        var $overlay = $('.overlay');

        //Close sidebar
        $(window).click(function (e) {
            var $target = $(e.target);
            if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

            if (!$target.hasClass('bars') && _this.isOpen() && $target.parents('#leftsidebar').length === 0) {
                if (!$target.hasClass('js-right-sidebar')) $overlay.fadeOut();
                $body.removeClass('overlay-open');
            }
        });

        $.each($('.menu-toggle.toggled'), function (i, val) {
            $(val).next().slideToggle(0);
        });

        //When page load
        $.each($('.menu .list li.active'), function (i, val) {
            var $activeAnchors = $(val).find('a:eq(0)');

            $activeAnchors.addClass('toggled');
            $activeAnchors.next().show();
        });

        //Collapse or Expand Menu
        $('.menu-toggle').on('click', function (e) {
            var $this = $(this);
            var $content = $this.next();

            if ($($this.parents('ul')[0]).hasClass('list')) {
                var $not = $(e.target).hasClass('menu-toggle') ? e.target : $(e.target).parents('.menu-toggle');

                $.each($('.menu-toggle.toggled').not($not).next(), function (i, val) {
                    if ($(val).is(':visible')) {
                        $(val).prev().toggleClass('toggled');
                        $(val).slideUp();
                    }
                });
            }

            $this.toggleClass('toggled');
            $content.slideToggle(320);
        });

        //Set menu height
        _this.setMenuHeight();
        _this.checkStatuForResize(true);
        $(window).resize(function () {
            _this.setMenuHeight();
            _this.checkStatuForResize(false);
        });

        //Set Waves
        Waves.attach('.menu .list a', ['waves-block']);
        Waves.init();
    },
    setMenuHeight: function (isFirstTime) {
        if (typeof $.fn.slimScroll != 'undefined') {
            var configs = $.App.options.leftSideBar;
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

            //Scroll active menu item when page load, if option set = true
            if ($.App.options.leftSideBar.scrollActiveItemWhenPageLoad) {
                var activeItemOffsetTop = $('.menu .list li.active')[0].offsetTop;
                if (activeItemOffsetTop > 150) $el.slimscroll({ scrollTo: activeItemOffsetTop + 'px' });
            }
        }
    },
    checkStatuForResize: function (firstTime) {
        var $body = $('body');
        var $openCloseBar = $('.navbar .navbar-header .bars');
        var width = $body.width();

        if (firstTime) {
            $body.find('.content, .sidebar').addClass('no-animate').delay(1000).queue(function () {
                $(this).removeClass('no-animate').dequeue();
            });
        }

        if (width < $.App.options.leftSideBar.breakpointWidth) {
            $body.addClass('ls-closed');
            $openCloseBar.fadeIn();
        }
        else {
            $body.removeClass('ls-closed');
            $openCloseBar.fadeOut();
        }
    },
    isOpen: function () {
        return $('body').hasClass('overlay-open');
    }
};
$.App.rightSideBar = {
    activate: function () {
        var _this = this;
        var $sidebar = $('#rightsidebar');
        var $overlay = $('.overlay');

        //Close sidebar
        $(window).click(function (e) {
            var $target = $(e.target);
            if (e.target.nodeName.toLowerCase() === 'i') { $target = $(e.target).parent(); }

            if (!$target.hasClass('js-right-sidebar') && _this.isOpen() && $target.parents('#rightsidebar').length === 0) {
                if (!$target.hasClass('bars')) $overlay.fadeOut();
                $sidebar.removeClass('open');
            }
        });

        $('body').on('click', '.js-right-sidebar', function () {
            $sidebar.toggleClass('open');
            if (_this.isOpen()) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
        });

        $('#profile_update').validate({
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
                console.log(form);
                var user_dt = {};
                $('input', $(form)).each(function (i, o) {
                    var inputText = $(o).val();
                    var nameAttr = $(o).attr('name');
                    user_dt[nameAttr] = inputText;
                });
                user_dt.uid = $.App.getUID();
                user_dt.dc = $.App.getCurrentDate();
                console.log(user_dt);
                
                arr = ['update_profile', user_dt];
                $.App.handleServerRequest('userHub', arr, function (response) {           
                    if (/^{/.test(response)/*$.type(JSON.parse(response)) == "object"*/) {
                        var res = JSON.parse(response);
                        res.pword = btoa(user_dt.pass);
                        sessionStorage.setItem("user_info",JSON.stringify(res));
                        profileUpdate = true;
                        return $.App.Alert("Profile updated successful","success");
                        // $.App.loadDefaultPage(res);
                    }
                });

            },
            messages: {
                uname: "Username is required",
                pass: "Password is required",
            }
        });

        $('#showPassword').on('change', function(){
            var $el = $(this), pfield = $('#pass');
            $el.is(':checked')? pfield.attr('type','text') : pfield.attr('type','password');
        });

        $('body').on('click', '.sweet-alert button.confirm', function () {
            if(profileUpdate) window.location.reload();
        });

    },
    isOpen: function () {
        return $('.right-sidebar').hasClass('open');
    }
}
var $searchBar = $('.search-bar');
$.App.search = {
    activate: function () {
        var _this = this;

        //Search button click event
        $('.js-search').on('click', function () {
            _this.showSearchBar();
        });

        //Close search click event
        $searchBar.find('.close-search').on('click', function () {
            _this.hideSearchBar();
        });

        //ESC key on pressed
        $searchBar.find('input[type="text"]').on('keyup', function (e) {
            if (e.keyCode == 27) {
                _this.hideSearchBar();
            }
        });
    },
    showSearchBar: function () {
        $searchBar.addClass('open');
        $searchBar.find('input[type="text"]').focus();
    },
    hideSearchBar: function () {
        $searchBar.removeClass('open');
        $searchBar.find('input[type="text"]').val('');
    }
}
$.App.navbar = {
    activate: function () {
        var $body = $('body');
        var $overlay = $('.overlay');

        //Open left sidebar panel
        $('.bars').on('click', function () {
            $body.toggleClass('overlay-open');
            if ($body.hasClass('overlay-open')) { $overlay.fadeIn(); } else { $overlay.fadeOut(); }
        });

        //Close collapse bar on click event
        $('.nav [data-close="true"]').on('click', function () {
            var isVisible = $('.navbar-toggle').is(':visible');
            var $navbarCollapse = $('.navbar-collapse');

            if (isVisible) {
                $navbarCollapse.slideUp(function () {
                    $navbarCollapse.removeClass('in').removeAttr('style');
                });
            }
        });
    }
}
$.App.input = {
    activate: function () {
        //On focus event
        $('.form-control').focus(function () {
            $(this).parent().addClass('focused');
        });

        //On focusout event
        $('.form-control').focusout(function () {
            var $this = $(this);
            if ($this.parents('.form-group').hasClass('form-float')) {
                if ($this.val() == '') { $this.parents('.form-line').removeClass('focused'); }
            }
            else {
                $this.parents('.form-line').removeClass('focused');
            }
        });

        //On label click
        $('body').on('click', '.form-float .form-line .form-label', function () {
            $(this).parent().find('input').focus();
        });

        //Not blank form
        $('.form-control').each(function () {
            if ($(this).val() !== '') {
                $(this).parents('.form-line').addClass('focused');
            }
        });
    }
}
$.App.select = {
    activate: function () {
        if ($.fn.selectpicker) { $('select:not(.ms)').selectpicker(); }
    }
}
$.App.dropdownMenu = {
    activate: function () {
        var _this = this;

        $('.dropdown, .dropup, .btn-group').on({
            "show.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                _this.dropdownEffectStart(dropdown, dropdown.effectIn);
            },
            "shown.bs.dropdown": function () {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectIn && dropdown.effectOut) {
                    _this.dropdownEffectEnd(dropdown, function () { });
                }
            },
            "hide.bs.dropdown": function (e) {
                var dropdown = _this.dropdownEffect(this);
                if (dropdown.effectOut) {
                    e.preventDefault();
                    _this.dropdownEffectStart(dropdown, dropdown.effectOut);
                    _this.dropdownEffectEnd(dropdown, function () {
                        dropdown.dropdown.removeClass('open');
                    });
                }
            }
        });

        //Set Waves
        Waves.attach('.dropdown-menu li a', ['waves-block']);
        Waves.init();
    },
    dropdownEffect: function (target) {
        var effectIn = $.App.options.dropdownMenu.effectIn, effectOut = $.App.options.dropdownMenu.effectOut;
        var dropdown = $(target), dropdownMenu = $('.dropdown-menu', target);

        if (dropdown.length > 0) {
            var udEffectIn = dropdown.data('effect-in');
            var udEffectOut = dropdown.data('effect-out');
            if (udEffectIn !== undefined) { effectIn = udEffectIn; }
            if (udEffectOut !== undefined) { effectOut = udEffectOut; }
        }

        return {
            target: target,
            dropdown: dropdown,
            dropdownMenu: dropdownMenu,
            effectIn: effectIn,
            effectOut: effectOut
        };
    },
    dropdownEffectStart: function (data, effectToStart) {
        if (effectToStart) {
            data.dropdown.addClass('dropdown-animating');
            data.dropdownMenu.addClass('animated dropdown-animated');
            data.dropdownMenu.addClass(effectToStart);
        }
    },
    dropdownEffectEnd: function (data, callback) {
        var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
        data.dropdown.one(animationEnd, function () {
            data.dropdown.removeClass('dropdown-animating');
            data.dropdownMenu.removeClass('animated dropdown-animated');
            data.dropdownMenu.removeClass(data.effectIn);
            data.dropdownMenu.removeClass(data.effectOut);

            if (typeof callback == 'function') {
                callback();
            }
        });
    }
}
/* Browser - Function ======================================================================================================
*  You can manage browser
*  
*/
var edge = 'Microsoft Edge';
var ie10 = 'Internet Explorer 10';
var ie11 = 'Internet Explorer 11';
var opera = 'Opera';
var firefox = 'Mozilla Firefox';
var chrome = 'Google Chrome';
var safari = 'Safari';
$.App.browser = {
    activate: function () {
        var _this = this;
        var className = _this.getClassName();

        if (className !== '') $('html').addClass(_this.getClassName());
    },
    getBrowser: function () {
        var userAgent = navigator.userAgent.toLowerCase();

        if (/edge/i.test(userAgent)) {
            return edge;
        } else if (/rv:11/i.test(userAgent)) {
            return ie11;
        } else if (/msie 10/i.test(userAgent)) {
            return ie10;
        } else if (/opr/i.test(userAgent)) {
            return opera;
        } else if (/chrome/i.test(userAgent)) {
            return chrome;
        } else if (/firefox/i.test(userAgent)) {
            return firefox;
        } else if (!!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/)) {
            return safari;
        }

        return undefined;
    },
    getClassName: function () {
        var browser = this.getBrowser();

        if (browser === edge) {
            return 'edge';
        } else if (browser === ie11) {
            return 'ie11';
        } else if (browser === ie10) {
            return 'ie10';
        } else if (browser === opera) {
            return 'opera';
        } else if (browser === chrome) {
            return 'chrome';
        } else if (browser === firefox) {
            return 'firefox';
        } else if (browser === safari) {
            return 'safari';
        } else {
            return '';
        }
    }
}
//=======================================================================================================================
var 
idleTimer = function (reset, duration) {
    var idleminute = 60000 * duration; //1 minute * duration
    lastDuration = duration;
    if (!reset) {
        idleTimeout = window.setTimeout(function () {
            //navigate to lock screen here
            $("[data-pagename='USER_LOCK']").trigger("click");//lock screen
            console.log('lock screen here')
        }, idleminute);
    }
    else {
        window.clearTimeout(idleTimeout);
        idleTimer(false, duration);
    }
},
getRandomColor = function () {
    var hex = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += hex[Math.floor(Math.random() * 16)];
    }
    return color;
},
smartColors = function () {
    return "#" + ((1 << 24) * Math.random() | 0).toString(16);
},
throttle = function (fn, frequency, scope) {
    //credits to remy sharp
    frequency = frequency || 250;
    var last, deferTimer;
    return function () {
        var context = scope || this,
            now = +new Date, args = arguments;
        if (last && last < (now + frequency)) {
            //forces event to run on an interval where interval = frequency
            clearTimeout(deferTimer);
            deferTimer = setTimeout(function () {
                last = now;
                fn.apply(context, args);
            }, frequency);
        } else {
            last = now;
            fn.apply(context, args);
        };
    };
},
debounce = function (fn, delay) {
    //this function uses javascript closure to generate the first and last trigger on a recurring event for performance management
    var timer = null, delay = delay || 250;
    return function () {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            fn.apply(context, args);
        }, delay);
    }
};
$.App.handleServerRequest = function (hubName,arr, cb) {
    console.log("App call (server)=> ", hubName, arr);
    var param = JSON.stringify(arr);
    $.post("auth/hub/"+ hubName +".php", {
        json: param
    }, function (data, status) {
        console.log("Server respond to (App)=> ", arr, status);
        // console.log("Server respond to (App)=> ", arr, status, data);
        cb(data);
    });
}
$.App.handleServerUpload = function (hubName,arr, cb) {
    console.log("App call (server upload )=> ", hubName, arr);
    var frmdata = arr[0].IMAGE_UPLOAD;
    $.ajax({
        url: "auth/hub/"+ hubName +".php",
        dataType: 'script',
        cache: false,
        contentType: false,
        processData: false,
        data: frmdata,                         // Setting the data attribute of ajax with file_data
        type: 'post',
        success: function (data)   // A function to be called if request succeeds
        {
            console.log("Server respond to (App)=> ", hubName, arr, data.responseText, data.status, data.statusText);
            cb(data);
        },
        error: function (data)   // A function to be called if request succeeds
        {
            console.log("Server respond to (App)=> ", hubName, arr, data.responseText, data.status, data.statusText);
            cb(data);
        }
    });
}
$.App.drawDataTable = function (options) {

    var table_data_0 = (typeof table_data == 'object') ? options.table_data : eval(options.table_data); // converts/parses rowData string to an Array Object 

    var table_data_1 = options.table_data_1 || options.table_header, table_id = options.table_id, action_view = options.action_view || 0, action_upload = options.action_upload, action_edit = options.action_edit || 0, action_delete = options.action_delete || 0,

    action_width = options.action_width, show_check_box = options.show_check_box, first_rec_index = options.first_rec_index || 0, fn_onEditable_Change = options.fn_onEditable_Change, action_select = options.action_select,

    fn_onSelect = options.fn_onSelect, action_update = options.action_update, action_issue = options.action_issue;

    var dset = table_data_0; // gets a global copy of the table_data_0 object so as not to tamper with d object itself

    var tab_drill_code = (table_data_1[0].hasOwnProperty("DT_DRILL_CODE")) ? table_data_1[0].DT_DRILL_CODE : null; //extracts the drill down code

    var tab_drill_parent = (table_data_1[0].hasOwnProperty("DT_DRILL_PARENT")) ? table_data_1[0].DT_DRILL_PARENT : null; //extracts the drill down modal number

    var tab_01_1_header = table_data_1[0].DT_HEADER; // extracts the string containing captions

    var tab_01_1_column = table_data_1[0].DT_COLUMN; // extracts the json string containing aoColumn (check datatable init)

    tab_01_1_column = (typeof tab_01_1_column == "object") ? tab_01_1_column : eval(tab_01_1_column); // converts/parses aoColumn string to an Array Object

    var tab_01_1_column_align_arr = (table_data_1[0].DT_HEADER_ALIGN) ? table_data_1[0].DT_HEADER_ALIGN.split(",") : ""; //array Of column alignment

    var tab_01_1_header_arr = (tab_01_1_header) ? tab_01_1_header.split(",") : ""; //array Of column captions

    var tab_01_1_hidden_arr = (table_data_1[0].DT_HEADER_HIDDEN_COLUMN_INDEX) ? table_data_1[0].DT_HEADER_HIDDEN_COLUMN_INDEX.split(",") : ""; //array Of hidden columns

    var tab_has_amount = (table_data_1[0].hasOwnProperty("DT_HEADER_AMOUNT_COLUMN_INDEX"));

    var tab_01_1_amount_arr = (table_data_1[0].hasOwnProperty("DT_HEADER_AMOUNT_COLUMN_INDEX")) ? table_data_1[0].DT_HEADER_AMOUNT_COLUMN_INDEX.split(",") : []; //array Of amount columns

    var tab_has_currency = (table_data_1[0].hasOwnProperty("DT_HEADER_CURRENCY_COLUMN_INDEX"));

    var tab_is_editable_arr = (table_data_1[0].hasOwnProperty("DT_EDITABLE_COLUMN") && (table_data_1[0].DT_EDITABLE_COLUMN != null)) ? table_data_1[0].DT_EDITABLE_COLUMN.split(",") : null; // indexes of editable columns

    var tab_01_1_currency_arr = (table_data_1[0].DT_HEADER_CURRENCY_COLUMN_INDEX) ? table_data_1[0].DT_HEADER_CURRENCY_COLUMN_INDEX.split(",") : null; //array Of currency columns

    var tab_is_customControl_arr = (table_data_1[0].hasOwnProperty("DT_CUSTOM_CONTROL") && (table_data_1[0].DT_CUSTOM_CONTROL != null)) ? $.parseJSON(table_data_1[0].DT_CUSTOM_CONTROL) : null; // custom control object

    var tab_01_1_column_defs = []; //Array to house the Dynamic Column Definition

    var tab_export_columns = [0]; //Array to house the export definition

    action_update = action_update || 0; action_issue = action_issue || 0;

    if ($('#' + table_id).hasClass("dataTable")) {

        $('#' + table_id).dataTable().fnDestroy()

        $('#' + table_id + ' thead tr').html('');

        $('#' + table_id + ' tbody').html('');

        $('#' + table_id + ' tfoot tr').html('');

    }

    //looping through aoColumn object to design the column definition properties such as searchable,visible,targets and the render function 

    var _togglers_append = '';

    var tableColumnToggler = $("#" + table_id + "_column_toggler");

    var oTable = $("#" + table_id);

    $.map(tab_01_1_column, function (data, i) {

        var visible = (tab_01_1_hidden_arr.indexOf(i.toString()) == -1); //any visible column index must not exist in DT_HEADER_HIDDEN_COLUMN_INDEX

        var searchable = ((data.mDataProp != null) && visible); // any searchable column must be visible and must be a data column i.e mDataProp is not null

        var isAmount = ($.inArray(i.toString(), tab_01_1_amount_arr) != -1);

        var isCurrency = ($.inArray(i.toString(), tab_01_1_currency_arr) != -1);

        var isEditable = (tab_is_editable_arr) ? ($.inArray(i.toString(), tab_is_editable_arr) != -1) : false;

        var isCustomCtrl = (tab_is_customControl_arr) ? ($.grep(tab_is_customControl_arr, function (ctrl) { return ctrl.INDEX == i; }).length) : false;

        if (data.mDataProp != null) _togglers_append += '<li role="presentation"><a role="menuitem" tabindex="-1" href="javascript:;"><label><input type="checkbox" ' + ((visible) ? "checked" : "") + ' data-column="' + i + '">' + tab_01_1_header_arr[i] + '</label></a></li>';//build column toggler checkboxes here

        var sortable = !((data.mDataProp == null) && visible), column = data.mDataProp;

        var render = function (data, type, row, meta) {

            var html, _html;

            //if (type == "exports" && meta.col == 0) return '<div class="text-center"><span>' + (meta.row + 1) + '</span></div>';

            if ($.isNumeric(data)) {

                html = '<div class="text-center"><span>' + data + '</span></div>';

            };

            if (row.hasOwnProperty("STATUS") || row.hasOwnProperty("status")) {

                var status = row.STATUS;

                if ((data == row.STATUS && data != null) ||(data == row.status && data != null)) {

                    html = "<div class='text-" + tab_01_1_column_align_arr[i] + "' style='color:#ffffff' ><span class='btn btn-xs ";

                    if (data == "APPROVED") { html += "bg-green-seagreen" }

                    if (data == "REJECTED") { html += "bg-red-thunderbird" }

                    if (data == "PAID" || data == "paid" ) { html += "bg-blue" }

                    if (data == "SAVED") { html += "bg-blue-soft" }

                    if (data == "CLOSED") { html += "bg-red" }

                    if (data == "PENDING") { html += "bg-red-flamingo" }

                    if (data == "PENDING PAYMENT") { html += "bg-red-flamingo" }

                    if (data == "CONFIRMED") { html += "bg-green-jungle" }

                    if (data == "PAID") { html += "bg-green-jungle" }

                    if (data == "ACCEPTED") { html += "bg-blue-madison" }

                    if (data == "EN ROUTE") { html += "bg-red-haze" }

                    if (data == "IN TRANSIT") { html += "bg-blue-dark" }

                    if (data == "DELIVERED") { html += "bg-yellow-gold" }

                    if (data == "AWAITING APPROVAL") { html += "bg-yellow-casablanca" }

                    if (data == "OPEN") { html += "bg-green-sharp" }

                    html += "'>" + data.toUpperCase() + "</span></div>";

                    return html;

                }

            }

            if (row.hasOwnProperty("RATING")) {

                if (data == row.RATING && data != null) {

                    html = "<div class='text-" + tab_01_1_column_align_arr[i] + "' style='color:#ffffff' ><span class='badge ";

                    if (data == "1") { html += "badge-danger" }

                    if (data == "2") { html += "badge-danger" }

                    if (data == "3") { html += "badge-info" }

                    if (data == "4") { html += "badge-warning" }

                    if (data == "5") { html += "badge-success" }

                    html += "'>" + data + "</span></div>";

                    return html;

                }

            }

            if (row.hasOwnProperty("APPROVAL_STATUS")) {

                var status = row.APPROVAL_STATUS;

                if (data == status && data != null) {

                    html = "<div data-action='view_approval' class='text-" + tab_01_1_column_align_arr[i] + "' style='color:#ffffff' ><span class='btn btn-xs ";

                    if (data.toLowerCase() == "approved") { html += "bg-green-seagreen" }

                    if (data.toLowerCase() == "authorised") { html += "bg-blue-madison" }

                    if (data.toLowerCase() == "pending") { html += "purple-soft" }

                    if (data.toLowerCase() == "rejected") { html += "bg-red-sunglo" }

                    html += "'>" + data + "</span></div>";

                    return html;

                }

            }

            if (isEditable) {

                var isNegative = ($.isNumeric(data) && data < 0) ? 'font-red-thunderbird negative' : '';

                html = '<div class="text-right ' + isNegative + '"><input type="text" data-col="' + column + '" style="width: 105px;border-radius:3px;text-align:right;" data-action="EDIT" value="' + $.App.addCommas(data.toFixed(0)).replace("-", "") + '" /><i class="fa fa-warning" style="color:red;cursor:pointer;display:none;" ></div>';

                return html;

            }

            if (isCustomCtrl) {

                var ctrl = $.grep(tab_is_customControl_arr, function (_ctrl) { return _ctrl.INDEX == i; })[0];

                html = '<div class="text-center">' + ctrl.CONTROL + '</div>';

                return html;

            }

            if (isAmount && data != undefined) {

                if (isCurrency) {

                    var curr_code = (row.CURRENCY_CODE == "N") ? '&#x20a6;' : row.CURRENCY_CODE;

                    var isNegative = (data < 0) ? 'font-red-thunderbird negative' : '';

                    var formated_amount = (data < 0) ? '(' + curr_code + $.App.addCommas(Math.abs(data)) + ')' : curr_code + $.App.addCommas(Math.abs(data))

                    html = '<div class="text-right ' + isNegative + '"><span>' + formated_amount + '</span></div>';

                } else {

                    var isNegative = (data < 0) ? 'font-red-thunderbird negative' : '';

                    html = '<div class="text-right ' + isNegative + '"><span>' + $.App.addCommas(Math.abs(data)) + '</span></div>'

                }

            }

            else {

                html = '<div class="text-' + tab_01_1_column_align_arr[i] + '"><span>' + (data || "") + '</span></div>';

            }

            ((column != null) && options.hasOwnProperty("fnColumnCallback") && typeof options.fnColumnCallback == "function") && (_html = options.fnColumnCallback(row, data, column, tab_01_1_column_align_arr[i]));

            return _html || html;

        };

        var obj_def = {

            "targets": [i],

            "visible": visible,

            "searchable": searchable,

            "render": render,

            "sortable": sortable

        }

        searchable && tab_export_columns.push(i);

        tab_01_1_column_defs.push(obj_def);

    });

    //tab_export_columns.push(':visible');

    var row_append = '';

    var foot_append = '';

    var lastcolumn = tab_01_1_header_arr.length - tab_01_1_hidden_arr.length - 1;

    var lheader = tab_01_1_header_arr.length;

    for (var i = 0; i < lheader; i++) {

        var isAmount = ($.inArray(i.toString(), tab_01_1_amount_arr) != -1);

        if (i == 0) { row_append = row_append + "<th style='width:4px' class='text-" + tab_01_1_column_align_arr[i] + "'>" + tab_01_1_header_arr[i] + "</th>" };

        if (i > 0 && i < lheader - 1) { row_append = row_append + "<th class='text-" + tab_01_1_column_align_arr[i] + "'>" + tab_01_1_header_arr[i] + "</th>" };

        if (i == lheader - 1 && !show_check_box) { row_append = row_append + "<th style='width:" + action_width + "px' class='text-" + tab_01_1_column_align_arr[i] + "'>" + tab_01_1_header_arr[i] + "</th>" };

        if (i == lheader - 1 && show_check_box) { row_append = row_append + "<th class='sorting_disabled' style='width:4px' class='text-" + tab_01_1_column_align_arr[i] + "'><input type='checkbox' class='group-checkable'/></th>" }

        foot_append = foot_append + "<th data-findex='" + i + "' ></th>";

    }

    tableColumnToggler.html(_togglers_append);

    $("input", tableColumnToggler).iCheck({ checkboxClass: 'icheckbox_flat-blue' });

    oTable.find("thead").html("<tr>" + row_append + "</tr>");

    oTable.find("tfoot").html("<tr>" + foot_append + "</tr>");

    oTable.find('thead input:checkbox').iCheck({ checkboxClass: 'icheckbox_flat-blue' });

    var idisplaylength = (oTable.data('idisplaylength') != undefined) ? oTable.data('idisplaylength') : 10;

    var _oTable = oTable.dataTable({

        "buttons": [

       { extend: 'print', className: 'btn green', text: "<i class=\"material-icons\">print</i> PRINT", exportOptions: { orthogonal: "exports", columns: tab_export_columns } },

       { extend: 'pdf', className: 'btn red-thunderbird', text: "<i class=\"material-icons\">picture_as_pdf</i> PDF", exportOptions: { orthogonal: "exports", columns: tab_export_columns } },

       { extend: 'csv', className: 'btn yellow', text: "<i class=\"material-icons\">grid_on</i> CSV", exportOptions: { orthogonal: "exports", columns: tab_export_columns } },

        ],

        "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

        "aaData": table_data_0,

        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],

        "iDisplayLength": idisplaylength,

        "aoColumns": tab_01_1_column,

        "order": [[options.order || 0, "asc"]],

        "pagingType": "bootstrap_full_number",

        "autoWidth": true,

        "bDestroy": true,

        "deferRender": true,

        "stateSave": false,

        "language": {

            "info": "Showing _START_ to _END_ of _TOTAL_ records",

            "paginate": {

                "previous": "Prev",

                "next": "Next",

                "last": "Last",

                "first": "First"

            },

        },

        "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

            var index = iDisplayIndexFull + 1 + first_rec_index;

            lastcolumn = $("td", nRow).length - 1;

            dset[iDisplayIndexFull] = _.clone(dset[iDisplayIndexFull]); //create a new instance of object to avoid pointer access

            $.extend(true, dset[iDisplayIndexFull], { "CheckState": false });

            var data_index = _.findIndex(dset, aData);

            if (show_check_box == true) {

                var param;

                param = (action_upload == 9) ? aData.TXN_APPROVAL_ID : aData.TXN_ID

                $('td:eq(' + lastcolumn + ')', nRow).html('<div class="text-center"><span><input type="checkbox" data-id="' + param + '"/></span></div>');

                $('td:eq(0)', nRow).html('<div class="text-center sn" data-sn="' + data_index + '"><span>' + index + '</span></div>');

            }

            else {

                $('td:eq(0)', nRow).html('<div class="text-center sn" data-sn="' + data_index + '"><span>' + index + '</span></div>');

            }



            if (!$('td:eq(' + lastcolumn + ') ins.iCheck-helper', nRow).length) {

                $('td:eq(' + lastcolumn + ') input:checkbox', nRow).iCheck({ checkboxClass: 'icheckbox_flat-blue' });

                if ($('th input:checkbox.group-checkable:checked').length && !$('td:eq(' + lastcolumn + ') input:checkbox:checked', nRow).length) {

                    $('td:eq(' + lastcolumn + ') input:checkbox', nRow).iCheck('toggle')

                } else {

                    //$('td:eq(' + lastcolumn + ') input:checkbox', nRow).iCheck('uncheck')

                }

            }

            (options.hasOwnProperty("fnRowCallback") && typeof options.fnRowCallback == "function") && options.fnRowCallback(nRow, aData, iDisplayIndex, iDisplayIndexFull);

            return nRow;

        },

        "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {

            var api = this.api();

            var html = '';

            $.map(tab_01_1_amount_arr, function (index) {

                if (tab_has_amount != '' && tab_01_1_column[index].mDataProp.toUpperCase().indexOf('BALANCE') < 0) {

                    var tot_amount = 0.0000;

                    var mdataProp = tab_01_1_column[index].mDataProp;

                    aaData.forEach(function (x) {

                        tot_amount += (x[mdataProp]);

                    });

                    var isNegative = (tot_amount < 0) ? 'font-red-thunderbird negative' : '';

                    if (tab_01_1_currency_arr && (tab_01_1_currency_arr.indexOf(index.toString()) != -1)) {

                        var formated_amount = (tot_amount < 0) ? '(' + Base_Currency_Code + $.App.addCommas(Math.abs(tot_amount.toFixed(0))) + ')' : Base_Currency_Code + $.App.addCommas(tot_amount.toFixed(0))

                        html = '<div class="text-right ' + isNegative + '"><span>' + formated_amount + '</span></div>';

                    } else {

                        tot_amount = (tot_amount < 0) ? '(' + $.App.addCommas(tot_amount.toFixed(0)) + ')' : $.App.addCommas(tot_amount.toFixed(0));

                        html = '<div class="text-right ' + isNegative + '"><span>' + tot_amount + '</span></div>';

                    }

                    $(api.column(index).footer()).html(html);

                }

            });

            (options.hasOwnProperty("fnRowCallback") && typeof options.fnRowCallback == "function") && options.fnRowCallback(nRow, aaData, iStart, iEnd, aiDisplay);

        },

        "fnDrawCallback": function (oSettings) {

            var table = oTable.DataTable();

            var arr = [];

            table.rows({ search: 'applied' }).data().each(function (value, index) {

                arr.push(value);

            });

            $.map(tab_01_1_amount_arr, function (index) {

                var html = '';

                if (tab_has_amount != '' && tab_01_1_column[index].mDataProp.toUpperCase().indexOf('BALANCE') < 0) {

                    var mdataProp = tab_01_1_column[index].mDataProp;

                    var tot_amount = 0;

                    arr.forEach(function (x) {

                        tot_amount += (x[mdataProp]);

                    });

                    var isNegative = (tot_amount < 0) ? 'font-red-thunderbird negative' : '';

                    if (tab_01_1_currency_arr && (tab_01_1_currency_arr.indexOf(index.toString()) != -1)) {

                        var formated_amount = (tot_amount < 0) ? '(' + Base_Currency_Code + $.App.addCommas(Math.abs(tot_amount.toFixed(0))) + ')' : Base_Currency_Code + $.App.addCommas(tot_amount.toFixed(0));

                        html = '<div class="text-right ' + isNegative + '"><span>' + formated_amount + '</span></div>';

                    } else {

                        tot_amount = (tot_amount < 0) ? '(' + $.App.addCommas(tot_amount.toFixed(0)) + ')' : $.App.addCommas(tot_amount.toFixed(0));

                        html = '<div class="text-right ' + isNegative + '"><span>' + tot_amount + '</span></div>';

                    }

                    $("#" + table_id + " tfoot [data-findex='" + index + "']").html(html);

                }

            });

            (options.hasOwnProperty("fnDrawCallback") && typeof options.fnDrawCallback == "function") && options.fnDrawCallback(oSettings);

        },

        "columnDefs": tab_01_1_column_defs,

    });

    oTable.off("ifToggled", "tbody tr input:checkbox").on("ifToggled", "tbody tr input:checkbox", oTable, function () {

        var state = $(this).prop("checked");

        var index = $(".sn", $(this).closest("tr")).data("sn");

        dset[index].CheckState = state;

        (options.hasOwnProperty("fn_OnifToggled") && typeof options.fn_OnifToggled == "function") && options.fn_OnifToggled(state, index, dset);

    });

    oTable.off("ifToggled", "thead tr input:checkbox").on("ifToggled", "thead tr input:checkbox", oTable, function () {

        if (oTable.find('thead input:checkbox:checked').length) {

            oTable.find('td input:checkbox').iCheck('toggle')

        } else {

            oTable.find('td input:checkbox:checked').iCheck('uncheck')

        }

    });

    /* event handler */

    (options.hasOwnProperty("fnRowEventHandler") && typeof options.fnRowEventHandler == "function") && options.fnRowEventHandler(oTable);

    /* handle show/hide columns*/

    tableColumnToggler.off("ifToggled").on("ifToggled", "input[type='checkbox']", function () {

        /* Get the DataTables object again - this is not a recreation, just a get of the object */

        var iCol = parseInt($(this).attr("data-column"));

        var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;

        oTable.fnSetColumnVis(iCol, (bVis ? false : true));

    });

    setTimeout(function () {

        ($(".dt-buttons").length) && $("#btn_ct").before($(".dt-buttons"));

        if (!oTable.find("ins.iCheck-helper").length) {

            oTable.find('tbody input:checkbox').iCheck({ checkboxClass: 'icheckbox_flat-blue' });

        }

        if (!oTable.closest('.portlet-body,.modal-body').find("ins.iCheck-helper").length) {

            oTable.closest('.portlet-body,.modal-body').find("input:checkbox", tableColumnToggler).iCheck({ checkboxClass: 'icheckbox_flat-blue' });

        }

    }, 200);
    // console.log('colums', tab_01_1_column);
    // console.log('data', table_data_0);
    // console.log('colum def', tab_01_1_column_defs);
    return { dset: dset, oTable: _oTable };

};
$.App.loadRecord = function (rawData) {              
    // console.log(rawData);
    var page_access = {};
    var header = rawData.HEADERS, data_2 = rawData.SAVED;
    header[0].DT_COLUMN = eval(header[0].DT_COLUMN);
    if (page_access && !page_access.PAGE_DELETE && !header[0].DT_COLUMN[header[0].DT_COLUMN.length - 1].mDataProp) {
        header[0].DT_COLUMN.pop(); // remove check_box column
        var _dtHeader = header[0].DT_HEADER.split(","); _dtHeader.pop();
        var _dtHeaderAlign = header[0].DT_HEADER_ALIGN.split(","); _dtHeaderAlign.pop();
        header[0].DT_HEADER = _dtHeader.join(","); // remove check_box column caption
        header[0].DT_HEADER_ALIGN = _dtHeaderAlign.join(","); // remove check_box column alignment
    };
    var options = {
        table_data: data_2,
        table_data_1: header,
        table_id: "recordTable",
        first_rec_index: 0,
        show_check_box: 0,
        action_view: 1,
        action_edit: 1,
        action_delete: 1,
        fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if (aData.constructor == Object) {
                var $actions = $("<div/>").append('<div class="text-center"><button type="button" class="btn btn-info btn-circle waves-effect waves-circle waves-float" data-action="dbRequest" data-act-type="edit" ><i class="material-icons">edit</i></button></div>');
                $actions.append('<div class="text-center"><button type="button" class="btn btn-danger btn-circle waves-effect waves-circle waves-float" data-action="dbRequest" data-act-type="delete" ><i class="material-icons">delete</i></button></div>');
                var $div_actions = $("div", $actions);
                var width = (($div_actions.length / 5) * 210) + "px";
                $div_actions.css({ "display": "inline" });
                var actions_column = (this.show_check_box) ? 2 : 1;
                // console.log(actions_column)
                !header[0].DT_COLUMN[header[0].DT_COLUMN.length - actions_column].mDataProp && $('td:eq(' + ($('td', nRow).length - actions_column) + ')', nRow).css("width", "100px").html($actions.html()).addClass("text-center");
                (aData.hasOwnProperty("APPROVAL_PERMIT") && !aData.APPROVAL_PERMIT) &&
                $("input:checkbox", nRow).closest("div").replaceWith(
                    '<i class="fa fa-ban" style="color:red;margin-top:6px;font-size:1.8em;"></i>'
                 );
            }
        },
        fn_OnifToggled: function (state, index, dset) {
            var someChecked = dset.some(function (data) { return (data.CheckState == true) });
            (someChecked) ? $tmp_action_btn.show("medium") : $tmp_action_btn.hide("medium");
        },
    };
    $.App.drawDataTable(options).dset;
}
$.App.clearForm = function(form) {
            
    var elements = $(form)[0].elements;

    for(i=0; i<elements.length; i++) {

        field_type = elements[i].type.toLowerCase();

        switch(field_type) {

            case "text": 
            case "password": 
            case "textarea":
            case "hidden":   
            case "email":
            case "number":
                console.log(elements[i].value);
                elements[i].value = ""; 
                break;

            case "radio":
            case "checkbox":
                if (elements[i].checked) {
                    elements[i].checked = false; 
                }
                break;

            case "select-one":
            case "select-multi":
                elements[i].selectedIndex = 0;
                break;

            default: 
                break;
        }
    }
}
$.App.getFormInput = function(form) {
            
    var elements = $(form)[0].elements, dt = {};

    for(i=0; i<elements.length; i++) {

        field_type = elements[i].type.toLowerCase();

        switch(field_type) {

            case "text": 
            case "password": 
            case "textarea":
            case "hidden":   
            case "email":
            case "number":
                var val =  elements[i].value;
                var name =  elements[i].name;
                dt[name] = val;
                break;

            case "radio":
            case "checkbox":
                if (elements[i].checked) {
                    var val =  elements[i].value;
                    var name =  elements[i].name;
                    dt[name] = val;
                    //elements[i].checked = false; 
                }
                break;

            case "select-one":
            case "select-multi":
                var val =  elements[i].value;
                var name =  elements[i].name;
                dt[name] = val;
                break;

            default: 
                break;
        }
    }

    return dt;
}
$.App.loadDefaultPage = function(res){
    $.App.page_access_logic(res, function(pglist){
        var defid = pglist.filter(function(o,i,a){
            if(res.default_page_id == o.page_id){
                return o; //return default page obj;
            }
        });
        sessionStorage.setItem("user_info",JSON.stringify(res));
        if(defid != "" && defid.length > 0){
            window.location.href = appUrl + defid[0].page_name.toLowerCase() + '.php';
        }
    });
}
$.App.page_access_logic = function(data,cb){
    //fetch user page access list
    var arr = ['user_page_access', {"uid":data.uid}];
    $.App.handleServerRequest('userHub', arr, function (response) {
        // console.log("res ==> ", response)
        var page_list = JSON.parse(response);        
        //check user default page and route to it here ()
        if(page_list.length){           
            $.App.base_pages = page_list;
            (cb && typeof cb !== undefined) && cb(page_list);
        }else{
            $('.msg').removeClass('hidden').find('button').siblings('span').html('This user is not configure properly');
        }
    });            
} 
$.App.display_menu = function(list,user){  
     
    list = _.sortBy(list,'module_order');
    var types = _.uniq(list,'module_name');
    var context = {
        "fullname": user.fname + " " + user.lname,
        "email": user.email,
        "modules" : (function(dt){
            var mod = dt.map(function(o,i,a){
                var obj = {};
                obj.pages = _.where(list,{'module_name':o.module_name});
                obj.module = o.module_custom_name;
                obj.module_icon = o.module_icon;
                obj.has_module = (o.module_name != 'other')? true : false;
                return obj;
            });
            return mod;
        })(types),
    }
    var template = $('#dynamic-menu').html();
    var htmlstr =  Handlebars.compile(template)(context);
    $('#leftsidebar').html(htmlstr);

    $.App.leftSideBar.activate();

    //logout, lock and unkock
    $('.logout').on('click', function () {
        //get user seesion data and end it here;
        $.App.handleServerRequest('userHub', ['logout_user'], function (response) {
            console.log(response); 
            sessionStorage.clear();
            window.location.reload();
        });
    });
}
$.App.getPages = function(){  
    return $.App.base_pages;
}
$.App.getCurrentPage = function(){  
    return $.App.current_page;
}
$.App.setActive = function(page,cb){  
    var pages = $.App.getPages();
    var modPages = pages.map(function(o,i,a){
        o.page_class = (o.page_name == page)? "active" : "";
        o.module_class = (o.page_name == page)? "active" : "";
        o.module_toggle = (o.page_name == page)? "toggled" : "";
        return o;
    });    
    $.App.base_pages = modPages;
    (cb && typeof cb !== undefined) && cb($.App.base_pages);
}
$.App.setPageAcess = function(page,cb){  //set current page access
    var pages = $.App.getPages();
    var pgObj = pages.filter(function(o,i,a){
        if(o.page_name == page) return o;
    })[0];    
    //set access button
    $.App.current_page = pgObj;
        if($.App.current_page == undefined) {
            var user = sessionStorage.getItem("user_info");
            $.App.loadDefaultPage(JSON.parse(user));
        }
    console.log("current page ()=> ", $.App.current_page);
    (cb && typeof cb !== undefined) && cb($.App.current_page);
}
$.App.getUID = function(){
    var uid = sessionStorage.getItem("user_info") !== null ? JSON.parse(sessionStorage.getItem("user_info")).uid : "";
    return uid;
}
$.App.getUser = function(){
    var user = sessionStorage.getItem("user_info") !== null ? JSON.parse(sessionStorage.getItem("user_info")) : window.location.href = appUrl;
    return user;
}
$.App.Alert = function (msg, type, title) {
    window.scroll(0, 0);
    var settings = {
        title: title || 'Message',
        text: msg || '',
        type: (type !== undefined) ? type.toLowerCase() : 'info',
    }
    try {
        swal(settings);
    } catch (e) {
        console.log(e);
    }
}
$.App.Confirm = function (msg, cb) {
    window.scroll(0, 0);
    //var response = (action !== undefined) ? ", " + action + " it!" : "";
    swal({
        title: "Notice!!!",
        text: msg || "This action will make changes to your records.",
        type: "warning",
        showCancelButton: true,
        cancelButtonColor: "red!important",
        confirmButtonColor: "#DD6B55!important",
        confirmButtonText: "Ok",
        cancelButtonText: "Cancel",
        confirmButtonClass: "confirm-action",
        cancelButtonClass: "cancel-action",
        closeOnConfirm: true,
        closeOnCancel: true,
        reverseButtons: true,
    }, function (isConfirm, e) {
        if (isConfirm) {
            (cb && cb !== undefined && typeof (cb) == 'function') && cb(true);
        } else {
            (cb && cb !== undefined && typeof (cb) == 'function') && cb(false);
        }
    });
}
$.App.getAppPath = function(){
    return appPath;
}
$.App.getAppUrl = function(){
    return appUrl;
}
$.App.addCommas = function (nStr) {
    nStr += '';
    var x = nStr.split('.'),
    x1 = x[0],
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
};
$.App.isJSON = function (str) {
    try {
        return (typeof str == "object" || typeof JSON.parse(str) == "object");
    } catch (ex) {
        return true;
    }
};
$.App.throttle = function () {
    return throttle;
}();
$.App.debounce = function () {
    return debounce;
}();
$.App.getRandomColor = function(){
    return getRandomColor();
}
$.App.getSmartColors = function(){
    return smartColors();
}
$.App.startSession = function (duration) {
    idleTimer(false, duration)
}
$.App.getCurrentDate = function () {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();
    if (dd < 10) {
        dd = '0' + dd
    }
    if (mm < 10) {
        mm = '0' + mm
    }
    var currentTime = new Date();
    hour = currentTime.getHours();
    min = currentTime.getMinutes();
    sec = currentTime.getSeconds();
    today = yyyy + '-' + mm + '-' + dd + ' ' + hour + ':' + min + ':' + sec;
    return today
}
$.App.isNumberKey = function(evt){
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if(charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
}
$.App.isNumberOrPoint = function(evt){
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if(charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
        return false;
    }
    return true;
}
$.App.getCurrencyCode = function(){
    var curr_code = '&#x20a6;';
    return curr_code;
}
$.App.formatCurrency = function(m){
    return $.App.getCurrencyCode() + $.App.addCommas(Math.abs(m));
}
//==========================================================================================================================

$(function () {
    $.App.browser.activate();
    // $.App.leftSideBar.activate();
    $.App.rightSideBar.activate();
    $.App.navbar.activate();
    $.App.dropdownMenu.activate();
    $.App.input.activate();
    $.App.select.activate();
    $.App.search.activate();   
    //Copied from https://github.com/daneden/animate.css
    $.fn.extend({
        animateCss: function (animationName) {
            var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
            $(this).addClass('animated ' + animationName).one(animationEnd, function() {
                $(this).removeClass('animated ' + animationName);
            });
        }
    });

    String.prototype.capitalise = function () {
        var _str = this, arr_str = _str.split(" ");
        arr_str = arr_str.map(str => { return str.substr(0, 1).toUpperCase() + str.substr(1, (str.length - 1)).toLowerCase() });
        return arr_str.join(" ");
    }
    Number.prototype.format = function (type) {
        if (type == "short") {
            var cp = ["K", "M", "B", "T"], value = this,
                pv = (value.toString().indexOf("-") != -1) ? value.toString().length - 1 : value.toString().length, suf_index = Math.floor((pv - 4) / 3),
                div = Math.round(value / Math.pow(10, (suf_index * 3 + 3)));
            return (pv > 3) ? div + cp[suf_index] + ((div * Math.pow(10, (suf_index * 3 + 3)) != value) ? "+" : "") : value;
        } else {
            var nStr = this.toString(), x, x1, x2;
            nStr += '';
            x = nStr.split('.');
            x1 = x[0];
            x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }
    }
    String.prototype.formats = function () {
        var str = this, delim = "%s", args = Array.prototype.slice(arguments);
        if (str.indexOf(delim) == -1 || !args.length) return str;
        var fmts = str.split(delim);
        for (var i = 0; i < fmts.length; i++) {
            var _str = fmts[i];
            fmts[i] = _str.replace(delim, args[i])
        }
        return fmts.join("");
    }
    String.prototype.toCardinal = function () {
        var text = this, dic = { "one": "first", "two": "second", "three": "third", "five": "fifth", "eight": "eighth" },
                arr = text.split(" "),
                _text = arr[arr.length - 1];
        arr[arr.length - 1] = dic[_text] || ((_text.substr(-1) == "y") ? (_text.substr(0, _text.length - 1) + "ieth") : (_text + "th"));
        return arr.join(" ");
    };
    $.fn.getSelector = function () {
        if (this.length === 0) {
            return null;
        }
        var obj = {};
        $.each(this[0].attributes, function () {
            if (this.specified) {
                obj[this.name] = this.value;
            }
        });
        var attrs = obj;
        var selector = [];
        for (var attr in attrs) {
            switch (attr.toLowerCase()) {
                case "class":
                    var arr = attrs[attr].split(" ");
                    arr = arr.filter(function (el) { return (el.indexOf("{{") == -1 && el != "") });
                    (arr.length) && selector.push("." + arr.join("."));
                    break;
                case "id":
                    selector.push("#" + attrs[attr]);
                    break;
                default:
                    selector.push("[" + attr + "='" + attrs[attr] + "']");
            }
        }
        return selector.join("");
    };
    $.fn.UniqueID = function () {
        if (!$(this).length) return;
        var id = new Date().getTime();
        id = $(this).prop("tagName") + "_" + id;
        return $(this).attr("id", id).attr("id");
    };
    $.fn.btn = function (state) {
        var $el = $(this);
        if (!$el.length) return;
        if ($el.length > 1) {
            $el.each(function ($btn) { $.fn.btn.call($(this), state); });
        } else {
            var prevState = ($el.attr("data-state")) ? $el.attr("data-state") : $el.html();
            (state == "reset") ? $el.html($el.attr("data-state")).removeAttr("data-state").prop("disabled", false) : $el.attr("data-state", prevState).html(state).prop("disabled", true);
        }
        return $el;
    };
    $.fn.toggleCSS = function (class1, class2) {
        var $el = $(this);
        if (!$el.length) return;
        ($el.hasClass(class1)) ? $el.removeClass(class1).addClass(class2) : $el.removeClass(class2).addClass(class1);
        return $el;
    };
    $.fn.inViewPort = function (fullyInView) {
        var $el = $(this), $window = $('body'), windowTop = $window.scrollTop(),
            windowBottom = windowTop + $window.height(), elemTop = $el.offset().top, elemBottom = elemTop + $el.height();
        return (fullyInView) ? ((windowTop < elemTop) && (windowBottom > elemBottom)) : ((elemTop <= windowBottom) && (elemBottom >= windowTop));
    }
    $.fn.isVisible = function(){
        var $el = $(this);
        return $el.is(':visible');
    }
    $.fn.setClass = function (prefix, className) {
        var $elem = $(this), elemClass = $elem.attr("class");
        $elem.attr("class", elemClass.replace(elemClass.match(new RegExp(prefix + "\\S+"))[0], (prefix + className)));
        return $elem;
    };
    $.fn.cssImportant = function (cssText) {
        var $el = $(this);
        if (!$el.length) return;
        $el.attr('style', function (i, s) {
            s = s || "";
            (s && s.trim().substr(-1) != ";") && (s += "; ");
            console.log(s + cssText);
            return s + cssText;
        });
        return $el;
    };
    Handlebars.registerHelper({
        checkActive: function (pages) {
            var res = '';
            pages.forEach(function(o,i,a) {
                if(o.module_class == 'active'){
                    res = 'active';
                }
            });
            return res;
        },
        checkToggle: function(pages){
            var res = '';
            pages.forEach(function(o,i,a) {
                if(o.module_toggle == 'toggled'){
                    res = 'toggled';
                }
            });
            return res;
        },
        checkDisplay: function(pages){
            var res = '';
            pages.forEach(function(o,i,a) {
                if(o.module_class == 'toggled'){
                    res = 'block';
                }
            });
            return (res == '')? 'none': res;
        },
        inc: function (num) {
            return (num + 1)
        },
        isGrater: function (param1, param2) {
                return (param1 > param2 ? true : false)
        },
        isEqual: function (param1, param2) {
            return (param1 == param2 ? true : false)
        }, 
        formatTag: function(tags){
            if(tags == undefined) return '';
            if(tags.length){
                var str = tags.map(function(value,index){
                    return '#'+value.tag;
                });
                return str.join(' ');
            }
            return '';
        },
        buildUnit: function(unit_list){
            if(unit_list == undefined) return '';
            if(unit_list.length){
                return 'Hello';
            }
        },
        formatCurrency: function(m){
            return $.App.getCurrencyCode() + $.App.addCommas(Math.abs(m));
        },
        getColor: function(str){
            return $.App.getSmartColors();
        },
        addUnderscore: function(str){
            return str.replace(/[' ']/g,'_');
        },
        formatDate: function(d){
            return (d.length > 0)? moment(d).format('llll') : d;
        },
        getTotal: function(data){
            if(data.length == 0) return '';
            var total = 0;
            data.forEach(function(o,i){
                total += parseInt(o.total);
            });
            return $.App.formatCurrency(total);
        },
        multiply: function(a,b){
            return (!isNaN(a) && !isNaN(b))? a*b : 0;
        }
        
    });

});

