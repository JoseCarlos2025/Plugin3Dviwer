<?php
/*
  Plugin Name: Sketchfab Viewer
  Description: Plugin para mostrar modelos 3D de Sketchfab.
  Version: 1.0
  Author: Javier
*/

function sketchfab_viewer_shortcode($atts) {
    $options = get_option('sketchfab_viewer_options');
    $width = isset($options['width']) ? $options['width'] : '100%';
    $height = isset($options['height']) ? $options['height'] : '400px';

    $atts = shortcode_atts(array(
        'link' => '',
    ), $atts);

    if (!empty($atts['link'])) {
        $output = '<div style="width: ' . esc_attr($width) . '; height: ' . esc_attr($height) . '; position:relative;">';
        $output .= '<iframe style="width: ' . esc_attr($width) . '; height: ' . esc_attr($height) . '" class="viwer-frame" src="" id="api-frame" allow="autoplay; xr-spatial-tracking" xr-spatial-tracking execution-while-out-of-viewport execution-while-not-rendered web-share allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" ></iframe>';

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

        $output .= '</div>';

        $output .= '<script src="' . plugins_url( 'visor-controller.js', __FILE__ ) . '"></script>';
        $output .= '<script src="' . plugins_url( 'use-example.js', __FILE__ ) . '"></script>';

        return $output;
    } else {
        return '';
    }
}
add_shortcode('sketchfab_viewer', 'sketchfab_viewer_shortcode');

function sketchfab_viewer_styles() {
    $options = get_option('sketchfab_viewer_options');
    $background_color = isset($options['background_color']) ? $options['background_color'] : '#ffffff';

    echo '<style>
            .viwer-frame {
              position: absolute;
              border: none;
            }
            .viewer-button {
                border: none;
                background-color: ' . $background_color . ';
                color: white;
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
                background-color: #131C2B; 
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

    echo '<script type="text/javascript" src="https://static.sketchfab.com/api/sketchfab-viewer-1.12.1.js"></script>
    <script type="text/javascript" src="https://3dviwer.s3.eu-north-1.amazonaws.com/SnIViwer.min.js"></script>';
}
add_action('wp_head', 'sketchfab_viewer_styles');

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
    add_settings_field('sketchfab_viewer_color_field', 'Color de Fondo', 'sketchfab_viewer_color_field_render', 'sketchfab-viewer-config', 'sketchfab_viewer_main_section');
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