function ajaxcancelreservation(postID,cancelCheck,notes)
{
  jQuery.ajax({
    type: 'POST',
    url: iomn_cancel_ajax.ajaxurl,
    data: {
      action: 'ajaxcancel',
      // acfname: name,
      acfpostid: postID,
      acfcheck: cancelCheck,
      acfnotes: notes
    },
    success: function(data) {
      if (data.isValid) {
        jQuery('#ajaxcancel-form').hide();
        jQuery('#ajaxcancelSubmit').hide();
        jQuery('#iomn-ajax-cancel-form')[0].reset();
        var id = '#ajaxcancel-response';
        jQuery(id).html('');
        jQuery(id).append(data.message);
        jQuery('#iomnCancelModal').on("hidden.bs.modal", function () {window.location.reload()});
      } else {
        var id = '#ajaxcancel-response';
        jQuery(id).html('');
        jQuery(id).append(data.message);
      }
    },
    error: function(MLHttpRequest, textStatus, errorThrown){
      alert(errorThrown);
    }
  });
}
