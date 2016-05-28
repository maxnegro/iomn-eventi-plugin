<h3>Informazioni per la gestione delle prenotazioni</h3>
<table class="form-table">
  <tr>
    <th><label for="specialty">Specialità</label></th>
    <td>
      <select name="specialty" id="specialty">
        <option value="na">Specificare</option>
        <option value="medico" <?php echo esc_attr( get_the_author_meta( 'specialty', $user->ID ) ) == 'medico' ? "SELECTED" : ""; ?>>Medico</option>
        <option value="tnfp" <?php echo esc_attr( get_the_author_meta( 'specialty', $user->ID ) ) == 'tnfp' ? "SELECTED" : ""; ?>>TNFP</option>
      </select>
      <span class="description">Selezionare la specialità.</span>
    </td>
  </tr>
</table>
