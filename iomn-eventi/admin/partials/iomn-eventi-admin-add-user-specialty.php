<h3>Informazioni per la gestione delle prenotazioni</h3>
<table class="form-table">
  <tr>
    <th><label for="specialty">Specialità</label></th>
    <td>
      <select name="specialty" id="specialty">
        <option value="na">Specificare</option>
        <?php
        $currentspec = "";
        if (is_object($user)) {
          $currentspec = esc_attr( get_the_author_meta( 'specialty', $user->ID ) );
        }
        ?>
        <option value="medico" <?php echo $currentspec == 'medico' ? "SELECTED" : ""; ?>>Medico</option>
        <option value="tnfp" <?php echo $currentspec == 'tnfp' ? "SELECTED" : ""; ?>>TNFP</option>
      </select>
      <span class="description">Selezionare la specialità.</span>
    </td>
  </tr>
</table>
