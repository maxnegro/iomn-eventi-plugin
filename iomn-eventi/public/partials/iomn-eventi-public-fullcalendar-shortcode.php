<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Iomn_Eventi
 * @subpackage Iomn_Eventi/public/partials
 */
?>
<div id="fullCalModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                <h4 id="modalTitle" class="modal-title"></h4>
                <p><div id="modalBody"></div></p>
            </div>
            <div id="modalBody" class="modal-body"></div>
            <div class="modal-footer">
                <button  style="width: initial; " type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                <a class="btn btn-primary" role="button" id="eventUrl" target="_blank">Prenota</a>
            </div>
        </div>
    </div>
</div>
<div id="iomn_calendar"></div>
<script>
    jQuery(document).ready(function () {
        jQuery("#iomn_calendar").fullCalendar({
            contentHeight: 600,
            lang: 'it',
            theme: false,
            eventRender: function (event, element) {
                element.attr('href', 'javascript:void(0);');
                element.click(function() {
                    jQuery('#modalTitle').html(event.title);
                    jQuery('#modalBody').html(event.description);
                    jQuery('#eventUrl').attr('href',event.url);
                    jQuery('#fullCalModal').modal();
                });
                element.tooltip({
                  title: event.title
                });
            },
            eventSources: [
                {
                    url: '<?php echo get_feed_link('iomn-eventi-json'); ?>',
                    error: function() {
                        alert('there was an error while fetching events!');
                    },
                }
            ]
        })
    });
</script>
