<?php
/*
Plugin Name: Breadcrumb para Yoast
Plugin URI: http://agmialdea.info/yoast-breadcrumb
Depends: wordpress-seo
Description: Añade un widget o shortcode para implementar el Breadcrumb de "Yoast SEO" donde quieras
Author: Alejandro Gil
Version: 1.1
Author URI: http://agmialdea.info
Text Domain: ybreadcrumb
*/

$plugin_name = plugin_basename(__FILE__);
define( 'YBREADCRUMB_BASENAME', $plugin_name );

add_action( 'admin_init', function ()
{
    if ( !function_exists('yoast_breadcrumb') )
    {
        deactivate_plugins( YBREADCRUMB_BASENAME );
        add_action( 'admin_notices', function ()
        {
            $class = 'notice notice-warning is-dismissible';
            $message = __( 'El plugin <strong>YBreadcrumb</strong> depende de <a href="http://socialgag.es/wp-admin/plugin-install.php?tab=plugin-information&amp;plugin=wordpress-seo&amp;TB_iframe=true&amp;width=600&amp;height=550" class="thickbox open-plugin-details-modal">Yoast SEO</a>, se ha desactivado.', 'ybreadcrumb' );

            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
        } );
}
        

}, 20 );

class YBreadcrumb_Widget extends WP_Widget {

	function __construct()
	{
		$opciones = array(
			'classname'     => 'ybreadcrumb_widget',
			'description'   => 'Widget para colocar el Breadcrumb de "WordPress SEO by Yoast"'
		);
		parent::__construct('ybreadcrumb_widget', 'Breadcrumb Widget', $opciones);
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
			<label for="<?php echo $this->get_field_id('title'); ?>">Título:</label></p>
			<input value="<?php echo $instance['title']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>">
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('tag1'); ?>">Etiqueta HTML para el título del Widget:</label></p>
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
			<label for="<?php echo $this->get_field_id('tag2'); ?>">Etiqueta HTML:</label></p>
            <select class="widefat" id="<?php echo $this->get_field_id('tag2'); ?>" name="<?php echo $this->get_field_name('tag2'); ?>">
                <option value="div" <?php if($instance['tag2'] == 'div') echo "selected"; ?>>DIV</option>
                <option value="p" <?php if($instance['tag2'] == 'p') echo "selected"; ?>>P</option>
            </select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('id'); ?>">ID:</label></p>
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