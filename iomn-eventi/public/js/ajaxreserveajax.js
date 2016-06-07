function ajaxformsendmail(postID,email,type)
{
  jQuery.ajax({
    type: 'POST',
    url: iomn_reserve_ajax.ajaxurl,
    data: {
      action: 'ajaxreserve_send_mail',
      // acfname: name,
      acfpostid: postID,
      acfemail: email,
      acftype: type // medici or tnfp
    },
    success: function(data) {
      if (data.isValid) {
        jQuery('#ajaxcontact-form').hide();
        jQuery('#ajaxSubmit').hide();
        jQuery('#iomn-ajax-form')[0].reset();
        var id = '#ajaxcontact-response';
        jQuery(id).html('');
        jQuery(id).append(data.message);
        jQuery('#iomnReserveModal').on("hidden.bs.modal", function () {window.location.reload()});
      } else {
        var id = '#ajaxcontact-response';
        jQuery(id).html('');
        jQuery(id).append(data.message);
      }
    },
    error: function(MLHttpRequest, textStatus, errorThrown){
      alert(errorThrown);
    }
  });
}
