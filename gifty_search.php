<?php

/**
 * Plugin Name: Giphy Search
 * Plugin Slug: giphy-search
 * Description: Search Giphy API for gifs
 * Version: 1.0.0
 * Author: Alexandro Cueto
 * Author URI: https://iskandercrow.dev/
 */

defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Giphy_Search')) :
  class Giphy_Search
  {
    public function __construct()
    {
      add_action('plugins_loaded', array($this, 'init'));
    }

    public function init()
    {
      add_shortcode('giphy-search', array($this, 'giphy_search_shortcode'));
    }

    public function giphy_search_shortcode()
    {
      $giphy_api_key = "pLURtkhVrUXr3KG25Gy5IvzziV5OrZGa";
      $giphy_search_url = 'https://api.giphy.com/v1/gifs/search?api_key=' . $giphy_api_key . '&q=';

      $giphy_search_query = sanitize_text_field($_POST['giphy_search_query']);

      $giphy_search_url = $giphy_search_url . $giphy_search_query;

      
      $cache_results = get_transient('giphy_search_results_' . $giphy_search_query);

      if ($cache_results === false) {
        $giphy_search_results = wp_remote_get($giphy_search_url);
        
        // cache the results for 5 minutes
        set_transient('giphy_search_results_' . $giphy_search_query, $giphy_search_results, 300);

        $cache_results = $giphy_search_results;
      }

      $giphy_search_results = json_decode($cache_results['body']);

      $giphy_search_results = $giphy_search_results->data;

      $giphy_search_html = '<div class="giphy-search">';
      $giphy_search_html .= '<form action="' . get_permalink() . '" method="POST">';
      $giphy_search_html .= '<input type="text" name="giphy_search_query" placeholder="Search a Gif" value='. $giphy_search_query .'>';
      $giphy_search_html .= '<input type="submit" value="Search">';
      $giphy_search_html .= '</form>';
      $giphy_search_html .= '<h2>Search Results</h2>';

      foreach ($giphy_search_results as $giphy_search_result) {
        $giphy_search_html .= '<img src="' . $giphy_search_result->images->fixed_width->url . '" />';
      }
      $giphy_search_html .= '</div>';

      return $giphy_search_html;
    }
  }

  new Giphy_Search();

endif;
