$(document).ready(function() {
    $('.social-icon').click(function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        window.open(
            url,
            'socialShareWindow',
            'height=450, width=550, top=' + ($(window).height() / 2 - 275) + ', left=' + ($(window).width() / 2 - 225) + ', toolbar=0, location=0, menubar=0, directories=0, scrollbars=0'
        );
        return false;
    });
});