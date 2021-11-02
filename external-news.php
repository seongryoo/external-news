<?php

/**
 * Plugin Name: External News
 */

class ExternalNews {
  private $slugin = 'sh-ex-ne';
  private $editor_scripts = array(
    'external-news',
    'display-news',
  );
  private $styles = array(
    'basic-styling',
  );
  private $editor_styles = array(

  );
  function init__register_ext_news() {
    $labels = array(
      'name'                    => 'External News',
      'singular_name'           => 'Article',
      'menu_name'               => 'External News',
      'add_new'                 => 'Add New',
      'add_new_item'            => 'Add New Article',
      'new_item'                => 'New Article',
      'edit_item'               => 'Edit Article',
      'view_item'               => 'View Article',
      'all_items'               => 'All External News',
      'search_items'            => 'Search External News',
      'not_found'               => 'No articles found.',
      'not_found_in_trash'      => 'No articles found in Trash.',
      'archives'                => 'External News archives',
      'filter_items_list'       => 'Filter external articles list',
      'items_list_navigation'   => 'External articles list navigation',
      'items_list'              => 'External articles list',
    );

    $args = array(
      'labels'                  => $labels,
      'public'                  => true,
      'menu_icon'               => 'dashicons-id-alt',
      'show_in_rest'            => true,
      'publicly_queryable'      => false,
    );
    register_post_type( 'ext_news', $args );
    $supports = array(
      'custom-fields',
    );
    add_post_type_support( 'ext_news', $supports );
  }
  function init__register_ext_news_meta() {
    $args = array(
      'show_in_rest'            => true,
      'single'                  => true,
      'type'                    => 'string',
    );

    $fileArgs = array(
      'show_in_rest'            => true,
      'single'                  => true,
      'type'                    => 'number',
    );
    register_post_meta( 'ext_news', 'ext_news__img', $fileArgs );
    register_post_meta( 'ext_news', 'ext_news__img_url', $args );
    register_post_meta( 'ext_news', 'ext_news__source', $args );
    register_post_meta( 'ext_news', 'ext_news__link', $args );
    register_post_meta( 'ext_news', 'ext_news__date', $args );
  }
  function init__register_block_template() {
    $ext_news_object = get_post_type_object( 'ext_news' );
    $ext_news_object->template = array(
      array( 'shryoo/ext-news-data' ),
    );
    $ext_news_object->template_lock = 'all';
  }
  function __construct() {
    // Load block editor assets
    add_action( 'enqueue_block_editor_assets', function() {
      $wp_deps = array(
        'wp-blocks',
        'wp-editor',
        'wp-edit-post',
      );
      foreach( $this->editor_scripts as $script ) {
        $handle = $this->slugin . '-' . $script;
        $url = plugin_dir_url( __FILE__ ) . 'js/' . $script . '.js';
        wp_enqueue_script( $handle, $url, $wp_deps );
      }
      wp_localize_script( $this->slugin . '-external-news', 'externalNews_scriptData', array(
        'pluginUrl' => plugin_dir_url( __FILE__ ),
      ));
      foreach( $this->editor_styles as $style ) {
        $handle = $this->slugin . '-' . $style;
        $url = plugin_dir_url( __FILE__ ) . 'css/' . $style . '.css';
        wp_enqueue_style( $handle, $url );
      }
    } );
    add_action( 'wp_enqueue_scripts', function() {
      foreach( $this->styles as $style ) {
        $handle = $this->slugin . '-' . $style;
        $url = plugin_dir_url( __FILE__ ) . 'css/' . $style . '.css';
        wp_enqueue_style( $handle, $url );
      }
    } );

    // Make js scripts into modules
    add_filter( 'script_loader_tag', function( $tag, $handle, $src ) {
      foreach( $this->editor_scripts as $script ) {
        $script_handle = $this->slugin . '-' . $script;
        if ($script_handle == $handle) {
          return '<script type="module" src="' . esc_url( $src ) . '"></script>';
        }
      }
      return $tag;
    }, 10, 3 );

    // Plugin behaviors
    add_action( 'init', function() {
      $this->init__register_ext_news();
      $this->init__register_ext_news_meta();
      $this->init__register_block_template();
    } );
    add_filter( 'enter_title_here', function( $input ) {
      if ( 'ext_news' === get_post_type() ) {
        return 'Article title';
      }
      return $input;
    } );
    add_filter( 'manage_ext_news_posts_columns', function( $columns ) {
      $columns[ 'date' ] = 'Post created';
      $columns[ 'article_source' ] = 'News source';
      $columns[ 'article_date' ] = 'Article date';
      return $columns;
    } );
    add_filter( 'manage_ext_news_posts_custom_column', function( $column, $post_id ) {
      switch ( $column ) {
        case 'article_date':
          $date = get_post_meta( $post_id, 'ext_news__date', true );
          $date_obj = date_create( $date );
          $date_label = date_format( $date_obj, 'F jS, Y' );
          $date_day = date_format( $date_obj, 'l' );
          echo $date_label . '<br>' . $date_day;
          break;
        case 'article_source':
          $source = get_post_meta( $post_id, 'ext_news__source', true );
          echo $source;
          break;
      }
    }, 10, 2 );
  }
}


$external_news = new ExternalNews();

include_once( plugin_dir_path( __FILE__ ) . 'block-render/display-news.php');