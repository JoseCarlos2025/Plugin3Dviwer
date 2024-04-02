<?php
/*
  Plugin Name: Sketchfab Viewer
  Description: Plugin para mostrar modelos 3D de Sketchfab.
  Version: 1.0
  Author: Javier y José Carlos
*/

function sketchfab_viewer_shortcode($atts) {
    $options = get_option('sketchfab_viewer_options');
    $width = isset($options['width']) ? $options['width'] : '100%';
    $height = isset($options['height']) ? $options['height'] : '400px';

    $atts = shortcode_atts(array(
        'link' => '',
    ), $atts);

    if (!empty($atts['link'])) {
        $output = '<div id="viewer-content" style="width: ' . esc_attr($width) . '; height: ' . esc_attr($height) . '; position:relative;">';
        $output .= '<iframe style="width: ' . esc_attr($width) . '; height: ' . esc_attr($height) . '" class="viewer-frame" src="" id="api-frame" allow="autoplay; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" ></iframe>';

        if (!empty($options['buttons'])) {
            $output .= '<div class="buttom-container">';
            foreach ($options['buttons'] as $button) {
                $button_label = isset($button['label']) ? $button['label'] : '';
                $button_id = isset($button['id']) ? $button['id'] : '';
                if (!empty($button_label) && !empty($button_id)) {
                    $output .= '<button data-hotspot="' . esc_attr($button_id) . '" class="viewer-button">' . esc_html($button_label) . '</button>';
                }
            }
            $output .= '</div>';
        }

        $output .= '<div id="content-entry"></div>';

        $output .= '</div>';

        $output .= '<script src="https://static.sketchfab.com/api/sketchfab-viewer-1.12.1.js"></script>';
        $output .= '<script src="' . plugins_url( 'SnIViewer.min.js', __FILE__ ) . '"></script>';
        $output .= '<script src="' . plugins_url( 'visor-controller.js', __FILE__ ) . '"></script>';

        return $output;
    } else {
        return '';
    }
}
add_shortcode('sketchfab_viewer', 'sketchfab_viewer_shortcode');

function sketchfab_viewer_styles() {
    $options = get_option('sketchfab_viewer_options');
    $background_color = isset($options['background_color']) ? $options['background_color'] : '#ffffff';
    $text_color = isset($options['text_color']) ? $options['text_color'] : '#000000';
    $hover_color = isset($options['hover_color']) ? $options['hover_color'] : '#131C2B';


    echo '<style>
            #viewer-content {
                display: flex;
            }

            .viewer-frame {
              border: none;
            }

            #content-entry {
                width: 30%;
                margin-left: 20px;
                margin-right: 20px;
            }

            .viewer-button {
                border: none;
                background-color: ' . $background_color . ';
                color: ' . $text_color . ';
                text-align: center; 
                text-decoration: none;
                display: inline-flex;
                align-items: center; 
                justify-content: center; 
                font-size: 16px; 
                margin: 4px 10px; 
                cursor: pointer; 
                border-radius: 4px; 
                padding: 10px 20px; 
            }
              
            .viewer-button:hover {
                background-color: '. $hover_color .'; 
            }

            .buttom-container {
                width: 100%;
                position: absolute;
                bottom:20px;
                display: flex;
                justify-content:center;
                pointer-events: none;
            }

            .buttom-container * {
                pointer-events: auto;
            }
        </style>';
}
add_action('wp_head', 'sketchfab_viewer_styles');

function my_custom_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-script', plugin_dir_url(__FILE__) . 'custom-script.js', array('jquery'), '1.0', true);
    wp_localize_script('custom-script', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'my_custom_scripts');

function cargar_contenido_entrada() {
    if (isset($_POST['entry_title'])) {
        $entry_title = sanitize_text_field($_POST['entry_title']); 

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            's' => $entry_title, 
        );

        $entries = get_posts($args);

        if ($entries) {
            $entry = $entries[0];

            $response = array(
                'titulo' => $entry->post_title,
                'contenido' => apply_filters('the_content', $entry->post_content),
            );

            echo json_encode($response);
        } else {
            echo json_encode(array('error' => 'No se encontró la entrada con el título especificado.'));
        }
    } else {
        echo json_encode(array('error' => 'El título de la entrada no se proporcionó.'));
    }

    die();
}
add_action('wp_ajax_cargar_contenido_entrada', 'cargar_contenido_entrada');
add_action('wp_ajax_nopriv_cargar_contenido_entrada', 'cargar_contenido_entrada');

function sketchfab_viewer_menu() {
    add_options_page('Sketchfab Viewer Configuración', 'Sketchfab Viewer', 'manage_options', 'sketchfab-viewer-config', 'sketchfab_viewer_settings_page');
}
add_action('admin_menu', 'sketchfab_viewer_menu');

function sketchfab_viewer_settings_page() {
    ?>
    <div class="wrap">
        <h1>Sketchfab Viewer Configuración</h1>
        <form method="post" action="options.php">
            <?php settings_fields('sketchfab_viewer_options_group'); ?>
            <?php do_settings_sections('sketchfab-viewer-config'); ?>
            <?php submit_button('Guardar Cambios'); ?>
        </form>
    </div>
    <?php
}

function sketchfab_viewer_admin_init() {
    register_setting('sketchfab_viewer_options_group', 'sketchfab_viewer_options');
    add_settings_section('sketchfab_viewer_main_section', 'Configuración Principal', 'sketchfab_viewer_section_callback', 'sketchfab-viewer-config');
    add_settings_field('sketchfab_viewer_width_field', 'Ancho del modelo 3D', 'sketchfab_viewer_width_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
    add_settings_field('sketchfab_viewer_height_field', 'Alto del modelo 3D', 'sketchfab_viewer_height_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
    add_settings_field('sketchfab_viewer_color_field', 'Color del Botón', 'sketchfab_viewer_color_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
    add_settings_field('sketchfab_viewer_hover_color_field', 'Color al pasar por encima del botón', 'sketchfab_viewer_hover_color_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
    add_settings_field('sketchfab_viewer_text_color_field', 'Color texto del Botón', 'sketchfab_viewer_text_color_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
    add_settings_field('sketchfab_viewer_button_field', 'Botones Personalizados', 'sketchfab_viewer_button_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
}
add_action('admin_init', 'sketchfab_viewer_admin_init');

function sketchfab_viewer_section_callback() {
    echo 'Esta es la sección principal de configuración.';
}

function sketchfab_viewer_width_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $width = isset($options['width']) ? $options['width'] : '100%';
    echo '<input type="text" name="sketchfab_viewer_options[width]" value="' . esc_attr($width) . '" />';
}

function sketchfab_viewer_height_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $height = isset($options['height']) ? $options['height'] : '400px';
    echo '<input type="text" name="sketchfab_viewer_options[height]" value="' . esc_attr($height) . '" />';
}

function sketchfab_viewer_color_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $background_color = isset($options['background_color']) ? $options['background_color'] : '#ffffff';
    echo '<input type="color" name="sketchfab_viewer_options[background_color]" value="' . esc_attr($background_color) . '" />';
}

function sketchfab_viewer_hover_color_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $hover_color = isset($options['hover_color']) ? $options['hover_color'] : '#131C2B';
    echo '<input type="color" name="sketchfab_viewer_options[hover_color]" value="' . esc_attr($hover_color) . '" />';
}

function sketchfab_viewer_text_color_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $text_color = isset($options['text_color']) ? $options['text_color'] : '#000000';
    echo '<input type="color" name="sketchfab_viewer_options[text_color]" value="' . esc_attr($text_color) . '" />';
}

function sketchfab_viewer_button_field_render() {
    $options = get_option('sketchfab_viewer_options');
    $buttons = isset($options['buttons']) ? $options['buttons'] : array();

    ?>
    <div id="button-list">
    <?php

    foreach ($buttons as $key => $button) {
        $button_label = isset($button['label']) ? $button['label'] : '';
        $button_id = isset($button['id']) ? $button['id'] : '';
        ?>
        <div class="sketchfab-button">
            <label for="sketchfab_viewer_button_label_<?php echo $key; ?>">Etiqueta del Botón:</label>
            <input type="text" id="sketchfab_viewer_button_label_<?php echo $key; ?>" name="sketchfab_viewer_options[buttons][<?php echo $key; ?>][label]" value="<?php echo esc_attr($button_label); ?>" /><br />
            <label for="sketchfab_viewer_button_id_<?php echo $key; ?>">Número del Hotspot:</label>
            <input type="text" id="sketchfab_viewer_button_id_<?php echo $key; ?>" name="sketchfab_viewer_options[buttons][<?php echo $key; ?>][id]" value="<?php echo esc_attr($button_id); ?>" />
            <button type="button" class="delete-button">Eliminar</button>
        </div>
        <?php
    }
    ?>
    </div>
    <button type="button" id="add-button">Añadir Botón</button>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-button').addEventListener('click', function() {
                var container = document.getElementById('button-list');
                var counter = container.querySelectorAll('.sketchfab-button').length;

                var newButton = document.createElement('div');
                newButton.classList.add('sketchfab-button');
                newButton.innerHTML = '<label for="sketchfab_viewer_button_label_' + counter + '">Etiqueta del Botón:</label>' +
                                      '<input type="text" id="sketchfab_viewer_button_label_' + counter + '" name="sketchfab_viewer_options[buttons][' + counter + '][label]" value="" /><br />' +
                                      '<label for="sketchfab_viewer_button_id_' + counter + '">Número del Hotspot:</label>' +
                                      '<input type="text" id="sketchfab_viewer_button_id_' + counter + '" name="sketchfab_viewer_options[buttons][' + counter + '][id]" value="" />' +
                                      '<button type="button" class="delete-button">Eliminar</button>';
                container.appendChild(newButton);
            });

            document.addEventListener('click', function(event) {
                if (event.target && event.target.classList.contains('delete-button')) {
                    event.target.parentNode.remove();
                }
            });
        });
    </script>
    <?php
}
?>