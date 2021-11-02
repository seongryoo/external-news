<?php
class ExternalNews_DisplayNews {
  function render_news_card( $id ) {
    $title = get_the_title( $id );
    $date = get_post_meta( $id, 'ext_news__date', true );
    $link = get_post_meta( $id, 'ext_news__link', true );
    $img = get_post_meta( $id, 'ext_news__img', true );
    $img_alt = get_post_meta( $img, '_wp_attachment_image_alt', true );
    $img_src = wp_get_attachment_image_src( $img, 'full' )[0];
    $news_source = get_post_meta( $id, 'ext_news__source', true );
    $date_obj = date_create( $date );
    $date_label = date_format( $date_obj, 'M j, Y' );

    $card = '';
    $card .= '<div class="card">';
      $card .= '<a href="' . esc_url( $link ) . '">';
        $card .= '<img class="news-image" src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $title ) . '">';
        $card .= '<div class="news-headline">' . $title . '</div>';
      $card .= '</a>';
      $card .= '<div class="publication">';
        $card .= '<p class="news-source">' . esc_html( $news_source ) . '</p>';
        $card .= '<p class="news-date">' . esc_html( $date_label ) . '</p>';
      $card .= '</div>';
    $card .= '</div>';
    return $card;
  }
  function normalize_date( $id ) {
    $date = get_post_meta( $id, 'ext_news__date', true );
    $date_formatted = strtotime( $date );
    return $date_formatted;
  }
  function compare_desc( $id1, $id2 ) {
    $date1 = $this->normalize_date( $id1 );
    $date2 = $this->normalize_date( $id2 );
    return -( $date1 <=> $date2 );
  }
  function compare_asc( $id1, $id2 ) {
    $date1 = $this->normalize_date( $id1 );
    $date2 = $this->normalize_date( $id2 );
    return ( $date1 <=> $date2 );
  }
  function render_block( $attributes ) {
    $num_articles = $attributes[ 'numArticles' ];
    $desc = $attributes[ 'desc' ];
    $markup = '';

    $markup .= '<div class="ext-news">';
      $query = new WP_Query( array(
        'post_type'       => 'ext_news',
        'posts_per_page'  => -1,
      ) );
      $article_ids = array();
      if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
          $query->the_post();
          $id = get_the_ID();
          array_push( $article_ids, $id );
        }
      }
      if ( $desc ) {
        usort( $article_ids, function( $a, $b ) {
          return $this->compare_desc( $a, $b );
        } );
      } else {
        usort( $article_ids, function( $a, $b ) {
          return $this->compare_asc( $a, $b );
        } );
      }
      $count_articles = 0;
      foreach( $article_ids as $id ) {
        $markup .= $this->render_news_card( $id );
        $count_articles++;
        if ( $num_articles != 'ALL' && $count_articles >= intval( $num_articles ) ) {
          break;
        }
      }
    $markup .= '</div>';
    return $markup;
  }

  function __construct() {
    add_action( 'init', function() {
      if ( ! function_exists( 'register_block_type' ) ) {
        return;
      }
      $attrs = array(
        'numArticles' => array(
          'type' => 'string',
          'default' => '3',
        ),
        'desc' => array(
          'type' => 'boolean',
          'default' => true,
        ),
      );
      $register_args = array(
        'attributes' => $attrs,
        'render_callback' => function( $attributes ) {
          return $this->render_block( $attributes );
        },
      );
      register_block_type( 'shryoo/display-news', $register_args );
    } );
  }
}
$displayNews = new ExternalNews_DisplayNews();