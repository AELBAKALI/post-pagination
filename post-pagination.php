<?php
/*

Support: abdelfatah.elbakli@gmail.com
Plugin Name: Advanced PAGI NATIONS
Plugin URI: http://wordpress.org/plugins/paginations/
Description: Advanced PAGI NATIONS is a Wordpress post pagination plugin. Adds post pagination to your WordPress website postes, is the best solution that you can have.
Version: 1.0.5 Beta
Author: ABDELFATAH EL BAKALI
Author e-mail: abdelfatah.elbakli@gmail.com
Author URI: https://www.revuewer.com
License: LGPL v2.1
License URI: http://www.gnu.org/licenses/lgpl-2.1.html
*/

function advanced_post_pagination() {
  global $post;
  if( ! is_single() || ! strpos( $post->post_content, '<!--nextpage-->' ) ) {
    return;
  }
  $page_links = wp_link_pages( array(
    'before' => '',
    'after' => '',
    'link_before' => '',
    'link_after' => '',
    'next_or_number' => 'number',
    'separator' => ' ',
    'pagelink' => '%',
    'echo' => 0
  ) );
  if( ! empty( $page_links ) ) {
    $style = get_option( 'advanced_post_pagination_style', 'normal' );
    $position = get_option( 'advanced_post_pagination_position', 'bottom' );
    $options = get_option( 'advanced_post_pagination_options' );
    $top_adsense_code = (isset($options['top_adsense_code'])) ? $options['top_adsense_code'] : '';
    $bottom_adsense_code = (isset($options['bottom_adsense_code'])) ? $options['bottom_adsense_code'] : '';
    if( $position == 'top' ) {
      echo $top_adsense_code;
    }
    echo '<div class="advanced-post-pagination '.$style.'">';
    echo $page_links;
    echo '</div>';
    if( $position == 'bottom' ) {
      echo $bottom_adsense_code;
    }
  }
}
/*function advanced_post_pagination( $content ) {
  global $multipage, $numpages;

  if ( ! $multipage ) {
    return $content;
  }

  $options = get_option( 'advanced_post_pagination_options' );
  $style = (isset($options['style'])) ? $options['style'] : 'normal';

  $output = '';
  $output .= '<div class="advanced-post-pagination '.$style.'">';
  $output .= '<span class="pages">Page '.get_query_var('page').' of '.$numpages.'</span>';
  $prev = get_previous_posts_link('&laquo; Previous');
  $next = get_next_posts_link('Next &raquo;');
  if ( $prev ) {
    $output .= '<span class="prev">'.$prev.'</span>';
  }
  if ( $next ) {
    $output .= '<span class="next">'.$next.'</span>';
  }
  $output .= '</div>';

  $content = str_replace( '<!--advpage-->', $output, $content );

  return $content;
}
add_filter( 'the_content', 'advanced_post_pagination' );
*/

function advanced_post_pagination_css() {
  $css = '
    .modern1 .next,
    .modern1 .prev {
      background-color: #333;
      color: #fff;
      border: none;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
    }

    .modern1 .next:hover,
    .modern1 .prev:hover {
      background-color: #444;
    }

    .modern2 .next,
    .modern2 .prev {
      background-color: #fff;
      color: #333;
      border: 1px solid #333;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
    }

    .modern2 .next:hover,
    .modern2 .prev:hover {
      background-color: #f5f5f5;
    }

    .modern3 .next,
    .modern3 .prev {
      background-color: transparent;
      color: #333;
      border: 2px solid #333;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
    }

    .modern3 .next:hover,
    .modern3 .prev:hover {
      color: #fff;
      background-color: #333;
    }
  ';
  wp_add_inline_style( 'advanced-post-pagination', $css );
}
add_action( 'wp_enqueue_scripts', 'advanced_post_pagination_css' );


add_action( 'init', 'advanced_post_pagination' );

add_action( 'admin_menu', 'advanced_post_pagination_menu' );

function advanced_post_pagination_menu() {
  add_options_page( 'Advanced Post Pagination', 'Advanced Post Pagination', 'manage_options', 'advanced-post-pagination', 'advanced_post_pagination_options' );
}

function advanced_post_pagination_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  echo '<div class="wrap">';
  echo '<h1>Advanced Post Pagination Options</h1>';
  echo '<p>Here you can customize the advanced post pagination settings for your site.</p>';
  echo '<form method="post" action="options.php">';
  settings_fields( 'advanced_post_pagination_options' );
  do_settings_sections( 'advanced-post-pagination' );
  submit_button();
  echo '</form>';
  echo '</div>';
}

add_action( 'admin_init', 'advanced_post_pagination_settings' );

function advanced_post_pagination_settings() {
  register_setting( 'advanced_post_pagination_options', 'advanced_post_pagination_options', 'advanced_post_pagination_validate_options' );
  add_settings_section( 'advanced_post_pagination_section', 'Advanced Post Pagination Settings', 'advanced_post_pagination_section_text', 'advanced-post-pagination' );
  add_settings_field( 'advanced_post_pagination_prev_text', 'Previous Button Text', 'advanced_post_pagination_prev_text', 'advanced-post-pagination', 'advanced_post_pagination_section' );
  add_settings_field( 'advanced_post_pagination_next_text', 'Next Button Text', 'advanced_post_pagination_next_text', 'advanced-post-pagination', 'advanced_post_pagination_section' );
  add_settings_field( 'advanced_post_pagination_style', 'Pagination Style', 'advanced_post_pagination_style', 'advanced-post-pagination', 'advanced_post_pagination_section' );
  add_settings_field( 'advanced_post_pagination_position', 'Pagination Position', 'advanced_post_pagination_position', 'advanced-post-pagination', 'advanced_post_pagination_section' );
  add_settings_field( 'advanced_post_pagination_adsense', 'AdSense Code', 'advanced_post_pagination_adsense', 'advanced-post-pagination', 'advanced_post_pagination_section' );

}

function advanced_post_pagination_section_text() {
  echo '<p>Customize the appearance and behavior of the advanced post pagination on your site:</p>';
}

function advanced_post_pagination_prev_text() {
  $options = get_option( 'advanced_post_pagination_options' );
  $prev_text = (isset($options['prev_text'])) ? $options['prev_text'] : '&laquo; Previous';
  echo '<input id="advanced_post_pagination_prev_text" name="advanced_post_pagination_options[prev_text]" size="40" type="text" value="'.$prev_text.'" />';
}

function advanced_post_pagination_next_text() {
  $options = get_option( 'advanced_post_pagination_options' );
  $next_text = (isset($options['next_text'])) ? $options['next_text'] : 'Next &raquo;';
  echo '<input id="advanced_post_pagination_next_text" name="advanced_post_pagination_options[next_text]" size="40" type="text" value="'.$next_text.'" />';
}

function advanced_post_pagination_style() {
  $options = get_option( 'advanced_post_pagination_options' );
  $style = (isset($options['style'])) ? $options['style'] : 'normal';
  echo '<input type="radio" id="advanced_post_pagination_style_normal" name="advanced_post_pagination_options[style]" value="normal" '.checked( $style, 'normal', false ).' />';
  echo '<label for="advanced_post_pagination_style_normal">Normal</label>';
  echo '<br />';
  echo '<input type="radio" id="advanced_post_pagination_style_compact" name="advanced_post_pagination_options[style]" value="compact" '.checked( $style, 'compact', false ).' />';
  echo '<label for="advanced_post_pagination_style_compact">Compact</label>';
  echo '<br />';
  echo '<input type="radio" id="advanced_post_pagination_style_modern1" name="advanced_post_pagination_options[style]" value="modern1" '.checked( $style, 'modern1', false ).' />';
  echo '<label for="advanced_post_pagination_style_modern1">Modern 1</label>';
  echo '<br />';
  echo '<input type="radio" id="advanced_post_pagination_style_modern2" name="advanced_post_pagination_options[style]" value="modern2" '.checked( $style, 'modern2', false ).' />';
  echo '<label for="advanced_post_pagination_style_modern2">Modern 2</label>';
  echo '<br />';
  echo '<input type="radio" id="advanced_post_pagination_style_modern3" name="advanced_post_pagination_options[style]" value="modern3" '.checked( $style, 'modern3', false ).' />';
  echo '<label for="advanced_post_pagination_style_modern3">Modern 3</label>';
}


function advanced_post_pagination_position() {
  $options = get_option( 'advanced_post_pagination_options' );
  $position = (isset($options['position'])) ? $options['position'] : 'bottom';
  echo '<input type="radio" id="advanced_post_pagination_position_top" name="advanced_post_pagination_options[position]" value="top" '.checked( $position, 'top', false ).' />';
  echo '<label for="advanced_post_pagination_position_top">Top</label>';
  echo '<br />';
  echo '<input type="radio" id="advanced_post_pagination_position_bottom" name="advanced_post_pagination_options[position]" value="bottom" '.checked( $position, 'bottom', false ).' />';
  echo '<label for="advanced_post_pagination_position_bottom">Bottom</label>';
}
function advanced_post_pagination_adsense() {
  $options = get_option( 'advanced_post_pagination_options' );
  $top_adsense_code = (isset($options['top_adsense_code'])) ? $options['top_adsense_code'] : '';
  $bottom_adsense_code = (isset($options['bottom_adsense_code'])) ? $options['bottom_adsense_code'] : '';
  echo '<label for="advanced_post_pagination_top_adsense_code">AdSense Code for Top Next/Prev Buttons:</label>';
  echo '<br />';
  echo '<textarea id="advanced_post_pagination_top_adsense_code" name="advanced_post_pagination_options[top_adsense_code]" rows="5" cols="50">'.$top_adsense_code.'</textarea>';
  echo '<br />';
  echo '<br />';
  echo '<label for="advanced_post_pagination_bottom_adsense_code">AdSense Code for Bottom Next/Prev Buttons:</label>';
  echo '<br />';
  echo '<textarea id="advanced_post_pagination_bottom_adsense_code" name="advanced_post_pagination_options[bottom_adsense_code]" rows="5" cols="50">'.$bottom_adsense_code.'</textarea>';
}
add_filter( 'the_content', 'add_advanced_post_pagination_to_content' );
function add_advanced_post_pagination_to_content( $content ) {
  if( is_single() ) {
    $content .= advanced_post_pagination();
  }
  return $content;
}


function advanced_post_pagination_validate_options( $input ) {
  $valid = array();
  $valid['prev_text'] = sanitize_text_field( $input['prev_text'] );
  $valid['next_text'] = sanitize_text_field( $input['next_text'] );
  $valid['style'] = sanitize_text_field( $input['style'] );
  $valid['position'] = sanitize_text_field( $input['position'] );
  $valid['top_adsense_code'] = sanitize_text_field( $input['top_adsense_code'] );
  $valid['bottom_adsense_code'] = sanitize_text_field( $input['bottom_adsense_code'] );

  return $valid;
}


