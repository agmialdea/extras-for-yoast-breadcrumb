<?php
/*
Plugin Name: EXTRAS for Yoast Breadcrumb
Plugin URI: http://agmialdea.info/extras-para-yoast-breadcrumb
Description: Shortcode and widget to include Yoast's breadcrumb function everywhere on your WordPress website.
Author: Alejandro Gil
Author URI: http://agmialdea.info

Version: 1.1
Depends: wordpress-seo

Text Domain: ybreadcrumb
Domain Path: /languages/

License: GPLv3
*/

// Evitar el acceso directo al plugin
if ( !defined( 'ABSPATH' ) ) {
	die( '¡Buen intento! ;)' );
}

// Localización
add_action( 'plugins_loaded', function ()
{
	load_plugin_textdomain( 'ybreadcrumb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
} );

add_action( 'admin_init', function ()
{
    $wpseo_internallinks = get_option( 'wpseo_internallinks' );
    
    if ( !function_exists('yoast_breadcrumb') || $wpseo_internallinks['breadcrumbs-enable'] != 1 )
    {
        deactivate_plugins( plugin_basename(__FILE__) );
        add_action( 'admin_notices', function ()
        {
            $class = 'notice notice-warning is-dismissible';
            $message = sprintf( wp_kses( __( 'El plugin <strong>EXTRAS for Yoast Breadcrumb</strong> se ha desactivado. Su activación depende de que la opción &quot;Migas de pan&quot; del plugin <a href="%1$s" class="%2$s">Yoast SEO</a> esté activa.', 'ybreadcrumb' ), array(  'a' => array( 'href' => array(), 'class' => array() ), 'strong' => array() ) ), esc_url( get_admin_url(null, 'plugin-install.php?tab=plugin-information&amp;plugin=wordpress-seo&amp;TB_iframe=true&amp;width=600&amp;height=550') ), esc_attr( 'thickbox open-plugin-details-modal' ) );

            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
        } );
    }

}, 20 );

class YBreadcrumb_Widget extends WP_Widget {

	function __construct()
	{
		$opciones = array(
			'classname'     => 'ybreadcrumb_widget',
			'description'   => __('Widget para colocar el Breadcrumb de "WordPress SEO by Yoast"', 'ybreadcrumb')
		);
		parent::__construct('ybreadcrumb_widget', __('Breadcrumb Widget', 'ybreadcrumb'), $opciones);
	}

	function widget($args, $instance)
	{
        extract($args); extract($instance);

        echo $before_widget.'<' . $tag1 . ' class="widget-title">'.$title.'</' . $tag1 . '>';
        yoast_breadcrumb('<' . $tag2 . ' id="' . $id . '">','</' . $tag2 . '>');
        echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		return array(
			'title'		=> strip_tags($new_instance['title']),
            'tag1'		=> strip_tags($new_instance['tag1']), 
            'tag2'		=> strip_tags($new_instance['tag2']),
            'id'		=> strip_tags($new_instance['id'])
		);
	}

	function form($instance)
	{
		// Obligamos a $instance a ser un array con todas las opciones disponibles
		$instance = wp_parse_args( (array) $instance, array(
						'title' => '¿D&oacute;nde estas?',
                        'tag1'   => 'h3',
                        'tag2'   => 'div',
                        'id'    => 'breadcrumb'
						
		));
		
		// Filtramos los valores para que se muestren correctamente en los formularios
		$instance['title'] = esc_attr($instance['title']);
        $instance['tag1'] = esc_attr($instance['tag1']);
        $instance['tag2'] = esc_attr($instance['tag2']);
        $instance['id'] = esc_attr($instance['id']);
		
		// Mostramos el formulario
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Título', 'ybreadcrumb'); ?>:</label></p>
			<input value="<?php echo $instance['title']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('tag1'); ?>"><?php _e('Etiqueta HTML para el título del Widget', 'ybreadcrumb'); ?>:</label></p>
            <select class="widefat" id="<?php echo $this->get_field_id('tag1'); ?>" name="<?php echo $this->get_field_name('tag1'); ?>">
                <option value="div" <?php if($instance['tag1'] == 'div') echo "selected"; ?>>DIV</option>
                <option value="p" <?php if($instance['tag1'] == 'p') echo "selected"; ?>>P</option>
                <option value="span" <?php if($instance['tag1'] == 'span') echo "selected"; ?>>SPAN</option> 
                <option value="h2" <?php if($instance['tag1'] == 'h2') echo "selected"; ?>>H2</option>
                <option value="h3" <?php if($instance['tag1'] == 'h3') echo "selected"; ?>>H3</option>
                <option value="h4" <?php if($instance['tag1'] == 'h4') echo "selected"; ?>>H4</option>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('tag2'); ?>"><?php _e('Etiqueta HTML', 'ybreadcrumb'); ?>:</label></p>
            <select class="widefat" id="<?php echo $this->get_field_id('tag2'); ?>" name="<?php echo $this->get_field_name('tag2'); ?>">
                <option value="div" <?php if($instance['tag2'] == 'div') echo "selected"; ?>>DIV</option>
                <option value="p" <?php if($instance['tag2'] == 'p') echo "selected"; ?>>P</option>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('ID', 'ybreadcrumb'); ?>:</label></p>
			<input value="<?php echo $instance['id']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>">
		</p>
		
		<?php
	}

}

add_action('widgets_init', function()
{
		register_widget('YBreadcrumb_Widget');
});


// Shortcode
// [ybreadcrumb tag="div" id="ybreadcrumb"]
add_shortcode( 'ybreadcrumb', function ( $atts )
{
    $att = shortcode_atts( array(
        'tag' => 'div',
        'id' => 'ybreadcrumb'
    ), $atts );

    return yoast_breadcrumb('<' . $att["tag"] . ' id="' . $att["tag"] . '">','</' . $att["tag"] . '>', false);
} );

# EOF #
