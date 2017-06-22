jQuery('#talkForm').on('submit', function(e){
    jQuery('#talkButton').addClass('loading').val('loading');
    e.preventDefault();
    if(jQuery('#name').length >= 1){
        var options = '';
        for (var i = jQuery('#selections').val().length - 1; i >= 0; i--) {
            options = options + '&'+jQuery('#selections').val()[i]+'=true';

        }
        var name = jQuery('#name').val();
        var uri = 'say.php?name='+name+options;
        jQuery('#audioPlayer').load(uri, function(){
            jQuery('#talkButton').removeClass('loading').val('Say Hi');
        });
    }


});
