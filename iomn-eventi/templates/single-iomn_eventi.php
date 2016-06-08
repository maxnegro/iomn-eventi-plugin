<?php
/**
* The template for displaying all single posts and attachments
*
* @package WordPress
* @subpackage Twenty_Sixteen
* @since Twenty Sixteen 1.0
*/
function iomn_eventi_single_ajax() {
	wp_enqueue_script('iomn-eventi-ajax-js', plugin_dir_url( __FILE__ ) . '../public/js/ajaxreserveajax.js', array(), NULL, false);
	wp_localize_script( 'iomn-eventi-ajax-js', 'iomn_reserve_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	wp_enqueue_script('iomn-eventi-ajax-cancel-js', plugin_dir_url( __FILE__ ) . '../public/js/ajaxcancelsubscription.js', array(), NULL, false);
	wp_localize_script( 'iomn-eventi-ajax-cancel-js', 'iomn_cancel_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}
add_action('wp_enqueue_scripts', 'iomn_eventi_single_ajax');

$user = wp_get_current_user();

get_header(); ?>
<div id="content" class="site-content"><!-- site main -->
		<?php
		// Start the loop.
		while ( have_posts() ) :
			the_post();

			$evdata = new Iomn_Eventi_Data($post->ID);
		?>
		<header class="content-header">
			<div class="container">
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
		</header>

		<div class="container">
		<div class="iomn-container">
			<ul class="list-group">
			<?php
			$inthepast = false;
			for ($i=0; $i < $evdata->sessions(); $i++) {
				$session = $evdata->get_session($i);
				printf('<li class="list-group-item">');
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
				printf('</li>');

				if (!$inthepast && ($session['date']) < time()) {
					$inthepast = true;
				}
			}
			?>
			</ul>
			<div class="iomn-location-detail">
			Presso: <br />
			<big>
				<?php echo $evdata->get_location(); ?>
			</big>
			<hr />
				Ulteriori informazioni:<br />
				<?php the_content(); ?>
			</div>

		</div>
		<hr />
		<?php if ($inthepast) : ?>
			<div class="alert alert-warning">Questo evento è già trascorso, non è più possibile prenotare.</div>
		<?php elseif ($evdata->reservedby($user->ID)) : ?>
			<div class="alert alert-warning clearfix">Già prenotato a tuo nome.
				<button id="iomn_button_reserve_med" class="btn btn-warning pull-right" onclick="{
					jQuery('#modalTitle').html('Cancella prenotazione');
					jQuery('#ajaxcontact-form').show();
					jQuery('#ajaxSubmit').show();
					jQuery('#ajaxcontact-response').html('');
					jQuery('#iomnCancelModal').modal();
				};">Disdici</button>
			</div>
			<div id="iomnCancelModal" class="modal fade">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span> <span class="sr-only">close</span></button>
							<h4 id="modalTitle" class="modal-title"></h4>
						</div>
						<div id="modalBody" class="modal-body">
							<div id="ajaxcancel-response" style="background-color:#E6E6FA ;color:blue;"></div>
							<div id="ajaxcancel-form">
								<p>Spunta la casella per confermare la tua intenzione di disdire la prenotazione per "<?php the_title(); ?>".</p>
								<p>Per favore lascia anche
								un messaggio dove spieghi qual è stato il problema che ti ha spinto a cambiare idea. Ad operazione
							  completata riceverai un messaggio all'indirizzo <?php echo $user->get('user_email'); ?> per
							  conferma.</p>
								<form id="iomn-ajax-cancel-form" action="" method="post" enctype="multipart/form-data">
									<div id="ajaxcontact-text">

										<input type="checkbox" id="ajaxcancelcheck" name="ajaxcancelcheck"/>&nbsp;<label for="ajaxcancelcheck">Conferma cancellazione della prenotazione.</label><br />
										<label for="ajaxcancelmessage">Note</label>
										<textarea class="form-control" id="ajaxcancelmessage" name="ajaxcancelmessage" rows="5"></textarea>
										<br/>
									</div>
								</form>
							</div>
						</div>
						<div class="modal-footer">
							<button id="ajaxClose" style="width: initial; " type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
							<a class="btn btn-danger" role="button" id="ajaxcancelSubmit" onclick="ajaxcancelreservation(<?php echo $post->ID; ?>,ajaxcancelcheck.checked,ajaxcancelmessage.value);">Disdici</a>
						</div>
					</div>
				</div>
			</div>

		<?php elseif ($evdata->vacancies($user->get('specialty')) + $evdata->vacancies('generici') <= 0) :?>
			<div class="alert alert-danger">Non ci sono posti disponibili per allievi <?php
				$sptext = array(
					'medici' => 'medici',
					'tnfp' => 'TNFP'
				);
				echo $sptext[$user->get('specialty')];
			?>.</div>
		<?php else : ?>
		<div class="alert alert-success clearfix" role="alert">
			<!--
			Medici: <?php printf('%d/%d', $evdata->vacancies('medici'), $evdata->seats('medici')); ?>
			-
			TNFP: <?php printf('%d/%d', $evdata->vacancies('tnfp'), $evdata->seats('tnfp')); ?>
			-
			Generici:  <?php printf('%d/%d', $evdata->vacancies('generici'), $evdata->seats('generici')); ?>
		-->
			<?php
				$sptext = "";
				$spcode = "";
				if ($evdata->vacancies($user->get('specialty')) > 0) {
					switch ($user->get('specialty')) {
						case 'medici':
							$sptext = 'medico';
							$spcode = 'medici';
							break;

						case 'tnfp':
							$sptext = 'TNFP';
							$spcode = 'tnfp';
							break;

						default:
						  // Non dovrebbe mai succedere. Lo metto per consentire debug.
							$sptext = 'Sconosciuto';
							break;
					}
				} else {
					$sptext = 'generico';
					$spcode = 'generici';
				}
				?>
				<button id="iomn_button_reserve_med" class="btn btn-success pull-right" onclick="{
					jQuery('#modalTitle').html('Prenotazione per studente <?php echo $sptext; ?>');
					jQuery('#ajaxcontacttype').val('<?php echo $spcode; ?>');
					jQuery('#ajaxcontact-form').show();
					jQuery('#ajaxSubmit').show();
					jQuery('#ajaxcontact-response').html('');
					jQuery('#iomnReserveModal').modal();
				};">Prenota</button>
				Posti disponibili: <span class="label label-success"><?php echo $evdata->vacancies($user->get('specialty')) + $evdata->vacancies('generici'); ?></span>
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
							<p>Spunta la casella per confermare la tua email, quindi fai clic su "Prenota"
							per confermare la tua partecipazione a <?php the_title(); ?>. Riceverai conferma
							sulla mail indicata e potrai controllare lo stato delle tue prenotazioni nella pagina
							riassuntiva, accessibile dal menu in alto.</p>
							<form id="iomn-ajax-form" action="" method="post" enctype="multipart/form-data">
								<input id="ajaxcontacttype" type="hidden" name="ajaxcontacttype" value="">
								<div id="ajaxcontact-text">
									<strong>Email: <?php echo $user->get('user_email'); ?></strong> <br/>
									<input type="checkbox" id="ajaxcontactemail" name="ajaxcontactemail"/>&nbsp;<label for="ajaxcontactemail">Conferma indirizzo email</label><br />
									<br/>
								</div>
							</form>
						</div>
					</div>
					<div class="modal-footer">
						<button id="ajaxClose" style="width: initial; " type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
						<a class="btn btn-primary" role="button" id="ajaxSubmit" onclick="ajaxformsendmail(<?php echo $post->ID; ?>,ajaxcontactemail.checked,ajaxcontacttype.value);">Prenota</a>
					</div>
				</div>
			</div>
		</div>
		<?php endif; ?>
		<br />
		<?php
		if ( is_singular( 'iomn_eventi' ) ) {
			// Previous/next post navigation.
			the_post_navigation( array(
				'next_text' => '<span class="meta-nav" aria-hidden="true">&nbsp;&raquo;</span> ' .
				'<span class="screen-reader-text">' . __( 'Next post:', 'twentysixteen' ) . '</span> ' .
				'<span class="post-title">%title</span>',
				'prev_text' => '<span class="meta-nav" aria-hidden="true">&nbsp;&laquo;</span> ' .
				'<span class="screen-reader-text">' . __( 'Previous post:', 'twentysixteen' ) . '</span> ' .
				'<span class="post-title">%title</span>',
				) );
			}
			// End of the loop.
		endwhile;
		?>
	</div><!-- .site-main --> -->

</div><!-- .content-area -->

<?php get_footer(); ?>
