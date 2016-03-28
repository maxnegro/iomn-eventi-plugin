<?php
/**
* Renders plugin meta box.
*
* @link       http://photomarketing.it
* @since      1.0.0
*
* @author     Massimiliano Masserelli <info@photomarketing.it>
*/
wp_nonce_field('_iomn_eventi_nonce', 'iomn_eventi_nonce');
?>

<table>
  <caption>Date ed orari delle attività</caption>
  <tr class="iomn_ev_row">
    <td>
      <label for="iomn_ev_type">Tipo attività</label><br />
      <select name="iomn_ev_type[]" id="iomn_ev_type" value="<?php // echo iomn_get_meta('iomn_pre_data'); ?>">
        <option value="">Selezionare</option>
        <option value="preop">Preoperatorio</option>
        <option value="op">Operatorio</option>
        <option value="na">Generico</option>
      </select>
    </td>
    <td>
      <label for="iomn_ev_data"><?php _e('Data', 'evento'); ?></label><br>
      <input type="text" name="iomn_ev_data[]" class="iomn_ev_data" value="<?php // echo iomn_get_meta('iomn_pre_data'); ?>">
    </td><td>
      <label for="iomn_ev_dalle"><?php _e('Dalle', 'evento'); ?></label><br>
      <input type="text" name="iomn_ev_dalle[]" class="iomn_ev_dalle" value="<?php // echo iomn_get_meta('iomn_pre_dalle'); ?>">
    </td><td>
      <label for="iomn_ev_alle"><?php _e('Alle', 'evento'); ?></label><br>
      <input type="text" name="iomn_ev_alle" class="iomn_ev_alle" value="<?php // echo iomn_get_meta('iomn_pre_alle'); ?>">
    </td>
    <td>
      <label for="iomn_ev_sala"><?php _e('Sala', 'evento'); ?></label><br>
      <input type="text" name="iomn_ev_sala[]" class="iomn_ev_sala" value="<?php // echo iomn_get_meta('iomn_pre_sala'); ?>">
    </td>
    <td>
      <br />
      <button class="iomn_ev_del"><i class="fa fa-minus-circle"></i></button>
    </td>
  </tr>
  <tr>
    <td colspan="4">
    </td>
    <td colspan="2" style="text-align: right">
      <button id="iomn_ev_add">Aggiungi Nuovo <i class="fa fa-plus-circle"></i></button>
    </td>
  </tr>
</table>
<script>
jQuery(function ($) {
  var tpdata = {
    showPeriodLabels: false,
    hours: {
      starts: 7, // First displayed hour
      ends: 20                  // Last displayed hour
    },
    minutes: {
      starts: 0, // First displayed minute
      ends: 45, // Last displayed minute
      interval: 15, // Interval of displayed minutes
      manual: []                // Optional extra entries for minutes
    },
    hourText: 'Ore', // Define the locale text for "Hours"
    minuteText: 'Minuti', // Define the locale text for "Minute"
    amPmText: ['AM', 'PM'], // Define the locale text for periods
  };

  $('#iomn_ev_add').click(function () {
    $('.iomn_ev_row').last('tr').after('<tr class="iomn_ev_row"><td> <label for="iomn_ev_type">Tipo attività</label><br /> <select name="iomn_ev_type[]" id="iomn_ev_type"> <option value="">Selezionare</option> <option value="preop">Preoperatorio</option> <option value="op">Operatorio</option> <option value="na">Generico</option> </select> </td> <td> <label for="iomn_ev_data"><?php _e('Data', 'evento'); ?></label><br> <input type="text" name="iomn_ev_data[]" class="iomn_ev_data"> </td><td> <label for="iomn_ev_dalle"><?php _e('Dalle', 'evento'); ?></label><br> <input type="text" name="iomn_ev_dalle[]" class="iomn_ev_dalle"> </td><td> <label for="iomn_ev_alle"><?php _e('Alle', 'evento'); ?></label><br> <input type="text" name="iomn_ev_alle" class="iomn_ev_alle" > </td> <td> <label for="iomn_ev_sala"><?php _e('Sala', 'evento'); ?></label><br> <input type="text" name="iomn_ev_sala[]" class="iomn_ev_sala"> </td> <td> <br /> <button class="iomn_ev_del"><i class="fa fa-minus-circle"></i></button> </td> </tr> ');
    $('.iomn_ev_del').click(function () {
      if($('.iomn_ev_row').length > 1) {
        $(this).closest('tr').remove();
      }
      return false;
    });
    $('.iomn_ev_dalle').timepicker(tpdata);
    $('.iomn_ev_alle').timepicker(tpdata);
    return false;
  });

  $('.iomn_ev_del').click(function () {
    if($('.iomn_ev_row').length > 1) {
      $(this).closest('tr').remove();
    }
    return false;
  });
  
  $('.iomn_ev_dalle').timepicker(tpdata);
  $('.iomn_ev_alle').timepicker(tpdata);

});
</script>

<p>
  <label for="iomn_dove"><?php _e('Dove', 'evento'); ?></label><br>
  <select name="iomn_dove" id="iomn_dove">
    <option value="" selected>-- Specificare --</option>
    <?php
    // Get all theme taxonomy terms
    $ospedali = get_terms('iomn_strutture', 'hide_empty=0');
    $selezione = wp_get_object_terms($post->ID, 'iomn_strutture');
    foreach ($ospedali as $ospedale) {
      if (!is_wp_error($selezione) && !empty($selezione) && !strcmp($ospedale->slug, $selezione[0]->slug)) {
        printf("<option value=\"%s\" selected>%s</option>\n", $ospedale->slug, $ospedale->name);
      } else {
        printf("<option value=\"%s\">%s</option>\n", $ospedale->slug, $ospedale->name);
      }
    }
    ?>
  </select>
</p>
<table>
  <caption>Posti disponibili</caption>
  <tr>
    <td>
      <label for="iomn_medici"><?php _e('Medici', 'evento'); ?></label><br>
      <input type="text" name="iomn_medici" id="iomn_medici" value="<?php // echo iomn_get_meta('iomn_medici'); ?>">
    </td><td>
      <label for="iomn_tnfp"><?php _e('TNFP', 'evento'); ?></label><br>
      <input type="text" name="iomn_tnfp" id="iomn_tnfp" value="<?php // echo iomn_get_meta('iomn_tnfp'); ?>"></td>
    </tr>
  </table>
  <script>
  /* Italian initialisation for the jQuery UI date picker plugin. */
  /* Written by Antonello Pasella (antonello.pasella@gmail.com). */
  jQuery(function ($) {
    $.datepicker.regional['it'] = {
      closeText: "Chiudi",
      prevText: "&#x3C;Prec",
      nextText: "Succ&#x3E;",
      currentText: "Oggi",
      monthNames: ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
      "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"],
      monthNamesShort: ["Gen", "Feb", "Mar", "Apr", "Mag", "Giu",
      "Lug", "Ago", "Set", "Ott", "Nov", "Dic"],
      dayNames: ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"],
      dayNamesShort: ["Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab"],
      dayNamesMin: ["Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa"],
      weekHeader: "Sm",
      dateFormat: "dd/mm/yy",
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ""};
      $.datepicker.setDefaults($.datepicker.regional['it']);
    });

    // Imposta datepicker e timepicker per i campi relativi
    jQuery(document).ready(function () {
      jQuery("#iomn_op_data").datepicker({minDate: 1, maxDate: "+1Y", dateFormat: "dd/mm/yy", regional: "it",
      onSelect: function () {
        //- get date from another datepicker without language dependencies
        var minDate = jQuery('#iomn_op_data').datepicker('getDate');
        minDate.setDate(minDate.getDate() - 1);
        jQuery("#iomn_pre_data").datepicker("change", {maxDate: minDate});
      }
    });
    jQuery(".iomn_op_dalle").timepicker({
      showPeriodLabels: false,
      hours: {
        starts: 7, // First displayed hour
        ends: 20                  // Last displayed hour
      },
      minutes: {
        starts: 0, // First displayed minute
        ends: 45, // Last displayed minute
        interval: 15, // Interval of displayed minutes
        manual: []                // Optional extra entries for minutes
      },
      hourText: 'Ore', // Define the locale text for "Hours"
      minuteText: 'Minuti', // Define the locale text for "Minute"
      amPmText: ['AM', 'PM'], // Define the locale text for periods

    });
    jQuery(".iomn_op_alle").timepicker({
      showPeriodLabels: false,
      hours: {
        starts: 7, // First displayed hour
        ends: 20                  // Last displayed hour
      },
      minutes: {
        starts: 0, // First displayed minute
        ends: 45, // Last displayed minute
        interval: 15, // Interval of displayed minutes
        manual: []                // Optional extra entries for minutes
      },
      hourText: 'Ore', // Define the locale text for "Hours"
      minuteText: 'Minuti', // Define the locale text for "Minute"
      amPmText: ['AM', 'PM'], // Define the locale text for periods

    });

    jQuery(".iomn_pre_data").datepicker({minDate: 1, maxDate: "+1Y", dateFormat: "dd/mm/yy", regional: "it",
    onSelect: function () {
      //- get date from another datepicker without language dependencies
      var minDate = jQuery('#iomn_pre_data').datepicker('getDate');
      minDate.setDate(minDate.getDate() + 1);
      jQuery("#iomn_op_data").datepicker("change", {minDate: minDate});
    }
  });

  jQuery(".iomn_pre_dalle").timepicker({
    showPeriodLabels: false,
    hours: {
      starts: 7, // First displayed hour
      ends: 20                  // Last displayed hour
    },
    minutes: {
      starts: 0, // First displayed minute
      ends: 45, // Last displayed minute
      interval: 15, // Interval of displayed minutes
      manual: []                // Optional extra entries for minutes
    },
    hourText: 'Ore', // Define the locale text for "Hours"
    minuteText: 'Minuti', // Define the locale text for "Minute"
    amPmText: ['AM', 'PM'], // Define the locale text for periods

  });
  jQuery(".iomn_pre_alle").timepicker({
    showPeriodLabels: false,
    hours: {
      starts: 7, // First displayed hour
      ends: 20                  // Last displayed hour
    },
    minutes: {
      starts: 0, // First displayed minute
      ends: 45, // Last displayed minute
      interval: 15, // Interval of displayed minutes
      manual: []                // Optional extra entries for minutes
    },
    hourText: 'Ore', // Define the locale text for "Hours"
    minuteText: 'Minuti', // Define the locale text for "Minute"
    amPmText: ['AM', 'PM'], // Define the locale text for periods

  });
});
</script>
