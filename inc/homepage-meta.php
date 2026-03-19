<?php
/**
 * Homepage meta: tabs wrapper + saving + admin assets.
 * Text domain: logestay
 */
if ( ! defined( 'ABSPATH' ) ) exit;

require_once get_template_directory() . '/inc/homepage-section-hero.php';
require_once get_template_directory() . '/inc/homepage-section-destination.php';
require_once get_template_directory() . '/inc/homepage-section-listing.php';
require_once get_template_directory() . '/inc/homepage-section-faq.php';
require_once get_template_directory() . '/inc/homepage-section-location.php';
require_once get_template_directory() . '/inc/homepage-section-reviews.php';
require_once get_template_directory() . '/inc/homepage-section-supports.php';
require_once get_template_directory() . '/inc/homepage-section-cta.php';


class Logestay_Homepage_Meta {

	const NONCE_ACTION = 'logestay_homepage_meta_save';
	const NONCE_NAME   = 'logestay_homepage_meta_nonce';

	public static function init() {
		add_action( 'add_meta_boxes', [ __CLASS__, 'add_metabox' ] );
		add_action( 'save_post_page', [ __CLASS__, 'save' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_assets' ] );
	}

	public static function is_homepage_context( $post_id ): bool {
		if ( ! $post_id ) return false;

		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id && (int) $post_id === $front_id ) return true;

		$template = (string) get_post_meta( $post_id, '_wp_page_template', true );
		if ( $template === 'homepage.php' ) return true;

		return false;
	}

	public static function add_metabox() {
		add_meta_box(
			'logestay_homepage_meta',
			__( 'Homepage – Content Settings', 'logestay' ),
			[ __CLASS__, 'render' ],
			'page',
			'normal',
			'high'
		);
	}

	public static function admin_assets( $hook ) {
		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) return;

		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;

		// Allow on new page screen; restrict on edit screen.
		if ( $hook === 'post.php' && ! self::is_homepage_context( $post_id ) ) return;

		wp_enqueue_media();
		wp_enqueue_script( 'jquery-ui-sortable' );

		wp_register_script( 'logestay-homepage-meta-admin', '', [ 'jquery', 'jquery-ui-sortable' ], '1.0.0', true );
		wp_enqueue_script( 'logestay-homepage-meta-admin' );

		$js = <<<JS
(function($){
  // Tabs
  $(document).on('click', '.logestay-tabs__nav button', function(e){
    e.preventDefault();
    var \$wrap = $(this).closest('.logestay-tabs');
    var target = $(this).data('target');

    \$wrap.find('.logestay-tabs__nav button').removeClass('is-active');
    $(this).addClass('is-active');

    \$wrap.find('.logestay-tabs__panel').removeClass('is-active');
    \$wrap.find(target).addClass('is-active');
  });

  // Gallery (used by hero section)
  function refreshIds(\$gallery){
    var ids = [];
    \$gallery.find('.logestay-gallery__item').each(function(){
      var id = $(this).data('id');
      if(id) ids.push(id);
    });
    \$gallery.find('input.logestay-gallery__ids').val(ids.join(','));
  }

  $(document).on('click', '.logestay-gallery__add', function(e){
    e.preventDefault();
    var \$gallery = $(this).closest('.logestay-gallery');

    var frame = wp.media({
      title: 'Select hero images',
      button: { text: 'Use images' },
      multiple: true
    });

    frame.on('select', function(){
      var selection = frame.state().get('selection');
      selection.each(function(attachment){
        var a = attachment.toJSON();
        var thumb = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;

        var html = ''
          + '<li class="logestay-gallery__item" data-id="'+ a.id +'">'
          +   '<div class="logestay-gallery__thumb"><img src="'+ thumb +'" alt="" /></div>'
          +   '<div class="logestay-gallery__actions">'
          +     '<button type="button" class="button link-button logestay-gallery__remove">Remove</button>'
          +   '</div>'
          + '</li>';

        \$gallery.find('.logestay-gallery__list').append(html);
      });

      refreshIds(\$gallery);
    });

    frame.open();
  });

  $(document).on('click', '.logestay-gallery__remove', function(e){
    e.preventDefault();
    var \$gallery = $(this).closest('.logestay-gallery');
    $(this).closest('.logestay-gallery__item').remove();
    refreshIds(\$gallery);
  });

  $(document).ready(function(){
    $('.logestay-gallery__list').sortable({
      items: '> .logestay-gallery__item',
      cursor: 'move',
      update: function(){
        var \$gallery = $(this).closest('.logestay-gallery');
        refreshIds(\$gallery);
      }
    });
  });
})(jQuery);
JS;

		wp_add_inline_script( 'logestay-homepage-meta-admin', $js );

		wp_register_style( 'logestay-homepage-meta-admin', false );
		wp_enqueue_style( 'logestay-homepage-meta-admin' );

		$css = <<<CSS
.logestay-tabs{margin-top:10px;}
.logestay-tabs__nav{display:flex;gap:8px;margin:0 0 12px 0;padding:0;flex-wrap:wrap;}
.logestay-tabs__nav button{border:1px solid #ccd0d4;background:#f6f7f7;padding:8px 12px;border-radius:6px;cursor:pointer;}
.logestay-tabs__nav button.is-active{background:#fff;border-color:#2271b1;color:#2271b1;}
.logestay-tabs__panel{display:none;background:#fff;border:1px solid #ccd0d4;border-radius:8px;padding:14px;}
.logestay-tabs__panel.is-active{display:block;}

.logestay-field{margin-bottom:12px;}
.logestay-field label{display:block;font-weight:600;margin-bottom:6px;}
.logestay-field input[type="text"],
.logestay-field input[type="url"],
.logestay-field textarea{width:100%;max-width:900px;}

.logestay-gallery{margin-top:12px;}
.logestay-gallery__list{display:flex;flex-wrap:wrap;gap:12px;margin:12px 0 0 0;padding:0;}
.logestay-gallery__item{list-style:none;width:140px;border:1px solid #ccd0d4;background:#fff;border-radius:10px;padding:10px;}
.logestay-gallery__thumb{width:100%;height:90px;display:flex;align-items:center;justify-content:center;overflow:hidden;border-radius:8px;background:#f6f7f7;}
.logestay-gallery__thumb img{max-width:100%;height:auto;display:block;}
.logestay-gallery__actions{margin-top:8px;text-align:center;}
CSS;

		wp_add_inline_style( 'logestay-homepage-meta-admin', $css );
	}

	public static function render( $post ) {
		$post_id = (int) $post->ID;

		if ( ! self::is_homepage_context( $post_id ) ) {
			echo '<p style="margin:0;">' . esc_html__( 'Tip: Set this page as the Front page (Settings → Reading) or use the "homepage.php" template to enable homepage fields.', 'logestay' ) . '</p>';
			return;
		}

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		$tabs = [
			[
				'id'    => 'logestay-tab-hero',
				'label' => __( 'Hero', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Hero', 'render' ],
			],
			[
				'id'    => 'logestay-tab-destination',
				'label' => __( 'Destination', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Destination', 'render' ],
			],
			[
				'id'    => 'logestay-tab-listing',
				'label' => __( 'Listing', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Listing', 'render' ],
			],
			[
				'id'    => 'logestay-tab-review',
				'label' => __( 'Reviews', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Reviews', 'render' ],
			],
			[
				'id'    => 'logestay-tab-location',
				'label' => __( 'Locations', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Location', 'render' ],
			],
			[
				'id'    => 'logestay-tab-faq',
				'label' => __( 'FAQ', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Faq', 'render' ],
			],
			[
				'id'    => 'logestay-tab-support',
				'label' => __( 'Supports', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Supports', 'render' ],
			],
			[
				'id'    => 'logestay-tab-cta',
				'label' => __( 'CTA', 'logestay' ),
				'cb'    => [ 'Logestay_Homepage_Section_Cta', 'render' ],
			],
		];

		?>
		<div class="logestay-tabs">
			<div class="logestay-tabs__nav">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<button type="button" class="<?php echo $index === 0 ? 'is-active' : ''; ?>" data-target="#<?php echo esc_attr( $tab['id'] ); ?>">
						<?php echo esc_html( $tab['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>

			<?php foreach ( $tabs as $index => $tab ) : ?>
				<div id="<?php echo esc_attr( $tab['id'] ); ?>" class="logestay-tabs__panel <?php echo $index === 0 ? 'is-active' : ''; ?>">
					<?php call_user_func( $tab['cb'], $post_id ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	public static function save( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( wp_is_post_revision( $post_id ) ) return;
		if ( ! current_user_can( 'edit_page', $post_id ) ) return;
		if ( ! self::is_homepage_context( $post_id ) ) return;

		$nonce = isset( $_POST[ self::NONCE_NAME ] ) ? sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) return;

		// Save hero (includes slides now)
		Logestay_Homepage_Section_Hero::save( $post_id, $_POST );
		Logestay_Homepage_Section_Destination::save( $post_id, $_POST );
		Logestay_Homepage_Section_Listing::save( $post_id, $_POST );
		Logestay_Homepage_Section_Reviews::save( $post_id, $_POST );
		Logestay_Homepage_Section_Location::save( $post_id, $_POST );
		Logestay_Homepage_Section_Faq::save( $post_id, $_POST );
		Logestay_Homepage_Section_Supports::save( $post_id, $_POST );
		Logestay_Homepage_Section_Cta::save( $post_id, $_POST );
	}
}

Logestay_Homepage_Meta::init();



/**
 * Hide the content editor ONLY for specific page templates (keeps meta boxes).
 */
add_action('admin_init', function () {

	global $pagenow;

	if ( ! in_array($pagenow, ['post.php', 'post-new.php'], true) ) return;

	$post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
	if (!$post_id && !empty($_POST['post_ID'])) $post_id = absint($_POST['post_ID']);
	if (!$post_id) return;

	// Only for pages
	if (get_post_type($post_id) !== 'page') return;

	$template = get_page_template_slug($post_id);

	$hide_editor_templates = [
		'templates/homepage.php',
	];

	if (in_array($template, $hide_editor_templates, true)) {
		remove_post_type_support('page', 'editor'); // hides "Type / to choose a block"
	}
});