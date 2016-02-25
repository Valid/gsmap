$(document).ready(function () {
    $('.accordion-toggle').click(function(e){
        e.preventDefault();
        //Expand or collapse this panel
        $(this).next('.accordion-content').slideToggle('fast');
        //Hide the other panels
        $('.accordion-content').not($(this).next('.accordion-content')).slideUp('fast');
        return false;
    });
    $('.do-lookup-btn').click(function (e) {
        e.preventDefault();

        var $target = $(e.currentTarget);
        var $form = $target.parents('form');
        var address = $form.find('input[name="street"]').val();
        var city = $form.find('input[name="city"]').val();
        var state = $form.find('input[name="state"]').val();

        var url = '/gsmap/public/addressLookup/'+encodeURIComponent(address+' '+city+', '+state);
        $('.response-container')
            .removeClass('error')
            .html('looking up congressional district...')
        ;
        $.ajax({
            url: url
        }).done(function(response) {
            var data = JSON.parse(response);
            if (data.status != 'OK') {
                $('.response-container').addClass('error');
            }

            $('.response-container').html(data.message);
        });

        return false;
    });

});