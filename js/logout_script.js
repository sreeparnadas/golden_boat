/**
 * Created by bangl on 22-Mar-18.
 */
$(function () {

    $('body').on('click','#logout-button',function(event){
        console.log('logout working');
        var request=$.ajax({
            type:'post',
            url: site_url+"/base/logout",
            success: function(data, textStatus, xhr) {
                window.location.replace("http://127.0.0.1/golden_boat");
            }
        });// end of ajax
    });


});