<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		// Start the loop.
		while ( have_posts() ) : the_post();

			// Include the single post content template.
			// get_template_part( 'template-parts/content', 'single' );
      $evdata = new Iomn_Eventi_Data($post->ID);

      // $data["pre_data"] = iomn_get_meta('iomn_pre_data');
      // $data["pre_dalle"] = iomn_get_meta('iomn_pre_dalle');
      // $data["pre_alle"] = iomn_get_meta('iomn_pre_alle');
      // $data["pre_sala"] = iomn_get_meta('iomn_pre_sala');
      // $data["op_data"] = iomn_get_meta('iomn_op_data');
      // $data["op_dalle"] = iomn_get_meta('iomn_op_dalle');
      // $data["op_alle"] = iomn_get_meta('iomn_op_alle');
      // $data["op_sala"] = iomn_get_meta('iomn_op_sala');

      // $prenotazione = new IomnPrenotazione($post->ID);
      // $data['tnfptot'] = $prenotazione->disponibili('tnfp');
      // $data['tnfpdispo'] = $prenotazione->disponibili('tnfp') - $prenotazione->iscritti('tnfp');
      // $data['medtot'] = $prenotazione->disponibili('medici');
      // $data['meddispo'] = $prenotazione->disponibili('medici') - $prenotazione->iscritti('medici');

      ?>
      <h1><?php the_title(); ?></h1>
      <div class="iomn-container">
          <div class="iomn-location-detail"><big>
          <?php echo $evdata->get_location(); ?>
          </big></div>
          <?php
          for ($i=0; $i < $evdata->sessions(); $i++) {
            $session = $evdata->get_session($i);
            printf('<div class="iomn-date-detail">');
            printf('    <div><strong><big>%s</big></strong></div>', date('d/m/Y', $session['date']));
            printf('    <div>%s-%s</div>', $session['from'], $session['to']);
            $evtype = "";
            if ($session['type'] == 'op') {
              $evtype = "Operatorio";
            } elseif ($session['type'] == 'preop') {
              $evtype = "Preoperatorio";
            }
            if (!empty($evtype)) {
              printf('    <div class="iomn-op">%s</div>', $evtype);
            }
            if (!empty($session['location'])) {
              printf('    <div>Sala: <em>%s</em></div>', $session['location']);
            }
            printf('</div>');

          }
           ?>
      </div>
      <hr />
      <div>
          <?php the_content(); ?>
      </div>
      <hr />
      <div>
          <h4>Posti disponibili</h4>
          Medici: <?php printf('%d/%d', $evdata->vacancies('medici'), $evdata->seats('medici')); ?>
          -
          TNFP: <?php printf('%d/%d', $evdata->vacancies('tnfp'), $evdata->seats('tnfp')); ?>
					-
					Generici:  <?php printf('%d/%d', $evdata->vacancies('generici'), $evdata->seats('generici')); ?>
					<?php
					$user = wp_get_current_user();
					if ($evdata->vacancies($user->get('specialty')) + $evdata->vacancies('generici') > 0) :
					?>
						<button id="iomn_button_reserve_med" style="float: right;" class="btn btn-success" onclick="{
              jQuery('#modalTitle').html('Prenotazione TNFP');
              jQuery('#ajaxcontacttype').val('tnfp');
              jQuery('#ajaxcontact-form').show();
              jQuery('#ajaxSubmit').show();
              jQuery('#ajaxcontact-response').html('');
              jQuery('#iomnReserveModal').modal();
          	};">Prenota</button>
					<?php endif; ?>
      </div>
      <div id="iomnReserveModal" class="modal fade">
          <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
                      <h4 id="modalTitle" class="modal-title"></h4>
                  </div>
                  <div id="modalBody" class="modal-body">
                    <div id="ajaxcontact-response" style="background-color:#E6E6FA ;color:blue;"></div>
                    <div id="ajaxcontact-form">
                      <form id="iomn-ajax-form" action="" method="post" enctype="multipart/form-data">
                      <input id="ajaxcontacttype" type="hidden" name="ajaxcontacttype" value="">
                      <div id="ajaxcontact-text">
                      <strong>Nome e cognome </strong> <br/>
                      <input type="text" id="ajaxcontactname" name="ajaxcontactname"/><br />
                      <br/>
                      <strong>Email </strong> <br/>
                      <input type="text" id="ajaxcontactemail" name="ajaxcontactemail"/><br />
                      <br/>
                      </div>
                    </form>
                    </div>
                  </div>
                  <div class="modal-footer">
                      <button  style="width: initial; " type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                      <a class="btn btn-primary" role="button" id="ajaxSubmit" onclick="ajaxformsendmail(ajaxcontactname.value,ajaxcontactemail.value,ajaxcontacttype.value);">Prenota</a>
                  </div>
              </div>
          </div>
      </div>
      <br />
      <?php
			if ( is_singular( 'iomn_eventi' ) ) {
				// Previous/next post navigation.
				the_post_navigation( array(
					'next_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Next', 'twentysixteen' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Next post:', 'twentysixteen' ) . '</span> ' .
						'<span class="post-title">%title</span>',
					'prev_text' => '<span class="meta-nav" aria-hidden="true">' . __( 'Previous', 'twentysixteen' ) . '</span> ' .
						'<span class="screen-reader-text">' . __( 'Previous post:', 'twentysixteen' ) . '</span> ' .
						'<span class="post-title">%title</span>',
				) );
			}
			// End of the loop.
		endwhile;
		?>

	</main><!-- .site-main -->

	<?php get_sidebar( 'content-bottom' ); ?>

</div><!-- .content-area -->

<?php get_footer(); ?>
