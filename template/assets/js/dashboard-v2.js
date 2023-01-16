/*   
Template Name: Color Admin - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.4
Version: 1.7.0
Author: Sean Ngu
Website: http://www.seantheme.com/color-admin-v1.7/admin/
*/

var getMonthName = function(number) {
    var month = [];
    month[0] = "January";
    month[1] = "February";
    month[2] = "March";
    month[3] = "April";
    month[4] = "May";
    month[5] = "Jun";
    month[6] = "July";
    month[7] = "August";
    month[8] = "September";
    month[9] = "October";
    month[10] = "November";
    month[11] = "December";
    
    return month[number];
};

var getDate = function(date) {
    var currentDate = new Date(date);
    var dd = currentDate.getDate();
    var mm = currentDate.getMonth() + 1;
    var yyyy = currentDate.getFullYear();
    
    if (dd < 10) {
        dd = '0' + dd;
    }
    if (mm < 10) {
        mm = '0' + mm;
    }
    currentDate = yyyy+'-'+mm+'-'+dd;
    
    return currentDate;
};


var handleScheduleCalendar = function() {
    var monthNames = ["January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December"];
    var dayNames = ["S", "M", "T", "W", "T", "F", "S"];

    var now = new Date(),
        month = now.getMonth() + 1,
        year = now.getFullYear();
    var calendarTarget = $('#schedule-calendar');
    $.get(base_url+"apps/home/all_news", function(data){
	var all_news = jQuery.parseJSON(data);
	$(calendarTarget).calendar({
	    months: monthNames,
	    days: dayNames,
	    events: all_news,
	    popover_options:{
		placement: 'top',
		html: true
	    }
	});
	$(calendarTarget).find('td.event').each(function() {
	    var backgroundColor = $(this).css('background-color');
	    $(this).removeAttr('style');
	    $(this).find('a').css('background-color', backgroundColor);
	});
	$(calendarTarget).find('.icon-arrow-left, .icon-arrow-right').parent().on('click', function() {
	    $(calendarTarget).find('td.event').each(function() {
		var backgroundColor = $(this).css('background-color');
		$(this).removeAttr('style');
		$(this).find('a').css('background-color', backgroundColor);
	    });
	});
    });
    
    
};

var DashboardV2 = function () {
	"use strict";
    return {
        //main function
        init: function () {
            handleScheduleCalendar();
        }
    };
}();