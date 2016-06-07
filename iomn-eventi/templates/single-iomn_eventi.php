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

}
add_action('wp_enqueue_scripts', 'iomn_eventi_single_ajax');

get_header(); ?>

<div id="content" class="site-content"><!-- site main -->
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
		<header class="content-header">
			<div class="container">
				<h1 class="page-title"><?php the_title(); ?></h1>
			</div>
		</header>

		<div class="container">
		<div class="iomn-container">
			<ul class="list-group">
			<?php
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
		<div>
			<h4>Posti disponibili</h4>
			Medici: <?php printf('%d/%d', $evdata->vacancies('medici'), $evdata->seats('medici')); ?>
			-
			TNFP: <?php printf('%d/%d', $evdata->vacancies('tnfp'), $evdata->seats('tnfp')); ?>
			-
			Generici:  <?php printf('%d/%d', $evdata->vacancies('generici'), $evdata->seats('generici')); ?>
			<span style="padding-left: 4em;"></span>
			<?php
			$user = wp_get_current_user();
			if ($evdata->vacancies($user->get('specialty')) + $evdata->vacancies('generici') > 0) :
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
				<button id="iomn_button_reserve_med" class="btn btn-success" onclick="{
					jQuery('#modalTitle').html('Prenotazione per studente <?php echo $sptext; ?>');
					jQuery('#ajaxcontacttype').val('<?php echo $spcode; ?>');
					jQuery('#ajaxcontact-form').show();
					jQuery('#ajaxSubmit').show();
					jQuery('#ajaxcontact-response').html('');
					jQuery('#iomnReserveModal').modal();
				};">Prenota</button>
			<?php else : ?>
				<button id="iomn_button_reserve_med" class="btn btn-danger" onclick="return false;">Posti esauriti</button>
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
