$(function (){
    'use strict';

    //switch between login & sign up

    $('.login-page h1 span').click(function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        $('.login-page form').hide();
        $('.' + $(this).data('class')).fadeIn(100);
    });

    // trigger the select box

    $("select").selectBoxIt({
        autoWidth: false
    });
    //hide placeholder on form focus

    $('[placeholder]').focus(function() {
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    }).blur(function () {
        $(this).attr('placeholder', $(this).attr('data-text'));
    });

    // add asterisk on required field

    $('input').each(function(){
       if($(this).attr('required') === 'required') {
           $(this).after('<span class="asterisk">*</span>')
       }
    });

    // confirmation message
    $('.confirm').click(function () {
        return confirm('Are You Sure?');
    });

    $('.live').keyup(function () {
        $($(this).data('class')).text($(this).val());
    });


});