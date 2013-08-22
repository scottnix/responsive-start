<?php

//
//  Responsive Start Child Theme Function
//


// creates the doctype section, html5boilerplate.com style with conditional classes
// this is the latest version that is compatible with the HTML5 Plugin for Thematic
function childtheme_create_doctype() {
    $content = "<!doctype html>" . "\n";
    $content .= '<!--[if lt IE 8]> <html class="no-js lt-ie9 lt-ie8" dir="' . get_bloginfo ('text_direction') . '" lang="'. get_bloginfo ('language') . '"> <![endif]-->'. "\n";
    $content .= '<!--[if IE 8]> <html class="no-js lt-ie9" dir="' . get_bloginfo ('text_direction') . '" lang="'. get_bloginfo ('language') . '"> <![endif]-->' . "\n";
    $content .= "<!--[if gt IE 8]><!-->" . "\n";
    $content .= "<html class=\"no-js\"";
    return $content;
}
add_filter('thematic_create_doctype', 'childtheme_create_doctype', 11);

// creates the head, meta charset, and viewport tags
function childtheme_head_profile() {
    $content = "<!--<![endif]-->";
    $content .= "\n" . "<head>" . "\n";
    $content .= "<meta charset=\"utf-8\" />" . "\n";
    $content .= "<meta name=\"viewport\" content=\"width=device-width\" />" . "\n";
    return $content;
}
add_filter('thematic_head_profile', 'childtheme_head_profile', 11);

// remove meta charset tag, now in the above function
function childtheme_create_contenttype() {
    // silence
}
add_filter('thematic_create_contenttype', 'childtheme_create_contenttype', 11);



// remove the index and follow tags from header since it is browser default (SEO related, kinda)
// reference - scottnix.com/polishing-thematics-head/
function childtheme_create_robots($content) {
    global $paged;
    if (thematic_seo()) {
        if((is_home() && ($paged < 2 )) || is_front_page() || is_single() || is_page() || is_attachment())
        {
            $content = "";
        } elseif (is_search()) {
            $content = "\t";
            $content .= "<meta name=\"robots\" content=\"noindex,nofollow\" />";
            $content .= "\n\n";
        } else {
            $content = "\t";
            $content .= "<meta name=\"robots\" content=\"noindex,follow\" />";
            $content .= "\n\n";
        }
    return $content;
    }
}
add_filter('thematic_create_robots', 'childtheme_create_robots');



// clear useless garbage for a polished head
// remove really simple discovery
remove_action('wp_head', 'rsd_link');
// remove windows live writer xml
remove_action('wp_head', 'wlwmanifest_link');
// remove index relational link
remove_action('wp_head', 'index_rel_link');
// remove parent relational link
remove_action('wp_head', 'parent_post_rel_link');
// remove start relational link
remove_action('wp_head', 'start_post_rel_link');
// remove prev/next relational link
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');



// register two additional custom menu slots
function childtheme_register_menus() {
    if ( function_exists( 'register_nav_menu' )) {
        register_nav_menu( 'secondary-menu', 'Secondary Menu' );
        register_nav_menu( 'tertiary-menu', 'Tertiary Menu' );
    }
}
add_action('thematic_child_init', 'childtheme_register_menus');



// remove built in drop down theme javascript
// thematictheme.com/forums/topic/correct-way-to-prevent-loading-thematic-scripts/
function childtheme_remove_superfish(){
    remove_theme_support('thematic_superfish');
 }
add_action('wp_enqueue_scripts','childtheme_remove_superfish', 9);



// script manager template for registering and enqueuing files
function childtheme_script_manager() {
    // wp_register_script template ( $handle, $src, $deps, $ver, $in_footer );
    // registers modernizr script, childtheme path, no dependency, no version, loads in header
    wp_register_script('modernizr-js', get_stylesheet_directory_uri() . '/js/modernizr.js', false, false, false);
    // registers fitvids script, local childtheme path, yes dependency is jquery, no version, loads in footer
    wp_register_script('fitvids-js', get_stylesheet_directory_uri() . '/js/jquery.fitvids.js', array('jquery'), false, true);
    // registers misc custom script, childtheme path, yes dependency is jquery, no version, loads in footer
    wp_register_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), false, true);

    // enqueue the scripts for use in theme
    wp_enqueue_script ('modernizr-js');
    wp_enqueue_script ('fitvids-js');
    wp_enqueue_script ('custom-js');

    if ( is_front_page() ) {
        // example of conditional loading, if you only use a script or style on one page, load it conditionally!
    }
}
add_action('wp_enqueue_scripts', 'childtheme_script_manager');

// deregister styles
function childtheme_deregister_styles() {
    // example of removing contact form 7 styling, so it can be added to the main CSS file
    // wp_deregister_style('contact-form-7');

}
add_action('wp_print_styles', 'childtheme_deregister_styles', 100);

// deregister scripts
function childtheme_deregister_scripts() {
    // example of removing some bs gravatar script jetpack loads
    // wp_deregister_script('devicepx');

    if ( ! is_page('contact') ) {
        // remove contact form7 styles on all pages but contact page
        // wp_dequeue_script('contact-form-7');
     }
}
add_action( 'wp_print_scripts', 'childtheme_deregister_scripts', 100 );



// add favicon to site, add 16x16 or 32x32 "favicon.ico" image to child themes main folder
function childtheme_add_favicon() { ?>
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<?php }
add_action('wp_head', 'childtheme_add_favicon');



// remove user agent sniffing from thematic theme
// this is what applies classes to the browser type and version body classes
function childtheme_show_bc_browser() {
    return FALSE;
}
add_filter('thematic_show_bc_browser', 'childtheme_show_bc_browser');



// kill access and add some new code to be used with the jQuery drop down menu
function childtheme_override_access() { ?>
    <div id="access">
        <div class="menu-button"><span class="menu-title">Navigation</span><div class="button"><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></div></div>
        <div class="access-nav" role="navigation">
           <?php
            if ( ( function_exists("has_nav_menu") ) && ( has_nav_menu( apply_filters('thematic_primary_menu_id', 'primary-menu') ) ) ) {
                echo  wp_nav_menu(thematic_nav_menu_args());
            } else {
                echo  thematic_add_menuclass(wp_page_menu(thematic_page_menu_args()));
            }
            ?>
        </div>
    </div><!-- #access -->
    <?php
}



// completely remove nav above functionality
function childtheme_override_nav_above() {
    // silence
}



// featured image size (on anything with excerpt)
function childtheme_post_thumb_size($size) {
    $size = array(300,300);
    return $size;
}
add_filter('thematic_post_thumb_size', 'childtheme_post_thumb_size');



// super hacky way to remove width and height from images, better for slider... but I don't like this :P
// reference - css-tricks.com/snippets/wordpress/remove-width-and-height-attributes-from-inserted-images/
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}



// remove the damn "Permalink to..." that appears on the titles, it's debatable this is bad for internal linking (SEO).
function childtheme_postheader_posttitle($posttitle ) {
    $posttitle = "\n\n\t\t\t\t\t";
    if (is_single() || is_page()) {
        $posttitle .= '<h1 class="entry-title">' . get_the_title() . "</h1>\n";
    } elseif (is_404()) {
        $posttitle .= '<h1 class="entry-title">' . __('Not Found', 'thematic') . "</h1>\n";
    } else {
        $posttitle .= '<h2 class="entry-title">';
        $posttitle .= sprintf('<a href="%s" title="%s" rel="bookmark">%s</a>',
                                apply_filters('the_permalink', get_permalink()),
                                sprintf( esc_attr__('%s', 'thematic'), the_title_attribute( 'echo=0' ) ),
                                get_the_title());
        $posttitle .= "</h2>\n";
    }
    return $posttitle;
}
add_filter('thematic_postheader_posttitle', 'childtheme_postheader_posttitle');



// add read more link to posts, which is very useful for usability
function childtheme_modify_excerpt($text) {
   return str_replace('[...]', '.... <a href="'.get_permalink().'" class="more-link">Read More &raquo;</a>', $text);
}
add_filter('get_the_excerpt', 'childtheme_modify_excerpt');



// kill the post header information, loading this below in the post footer
function childtheme_override_postheader_postmeta() {
    // silence!
}

// example of changing up the display of the entry-utility for a different look
function childtheme_override_postfooter() {
    $post_type = get_post_type();
    $post_type_obj = get_post_type_object($post_type);
    $tagsection = get_the_tags();

    // Display nothing for "Page" post-type
    if ( $post_type == 'page' ) {
        $postfooter = '';
    // For post-types other than "Pages" press on
    } else {
        $postfooter = '<footer class="entry-utility">';
        $postfooter .= '<ul class="main-utilities">';
        $postfooter .= '<li class="icon-calendar">' . thematic_postmeta_entrydate() . '</li>';
        $postfooter .= '<li class="icon-folder">' . thematic_postfooter_postcategory() . '</li>';
        $postfooter .= '<li class="icon-comment">' . thematic_postfooter_postcomments() . '</li>';
        if ( $tagsection ) {
            $postfooter .= '<li class="icon-tag">' . thematic_postfooter_posttags() . '</li>';
        }
        if ( is_user_logged_in() ) {
                $postfooter .= '<li class="icon-pencil">' . thematic_postfooter_posteditlink() . '</li>';
        }
        $postfooter .= '</ul>';
        $postfooter .= "\n\n\t\t\t\t\t</footer><!-- .entry-utility -->\n";
    }
    // Put it on the screen
    echo apply_filters( 'thematic_postfooter', $postfooter ); // Filter to override default post footer
}

// removes the "Published" from the date in the postfooter, also removes seperator " | "
function childtheme_override_postmeta_entrydate() {
    $entrydate = '<span class="entry-date">';
    $entrydate .= get_the_time(thematic_time_display());
    $entrydate .= '</span>';
    return apply_filters('thematic_postmeta_entrydate', $entrydate);
}

// remove unneeded code from postcategory
function childtheme_override_postfooter_postcategory() {
    $postcategory = "\n\n\t\t\t\t\t\t" . '<span class="cat-links">';
    if (is_single()) {
        $postcategory .= __('Categories ', 'thematic') . get_the_category_list(', ');
        $postcategory .= '</span>';
        $posttags = get_the_tags();
        if ( !$posttags ) {
            $postcategory .= '';
        }
    } elseif ( is_category() && $cats_meow = thematic_cats_meow(', ') ) {
        $postcategory .= __('Also posted in ', 'thematic') . $cats_meow;
        $postcategory .= '</span>' . "\n\n\t\t\t\t\t\t";
    } else {
        $postcategory .= __('Posted in ', 'thematic') . get_the_category_list(', ');
        $postcategory .= '</span>' . "\n\n\t\t\t\t\t\t";
    }
    return apply_filters('thematic_postfooter_postcategory',$postcategory);
}

// remove unneeded code from posttags
function childtheme_override_postfooter_posttags() {
    if ( is_single() && !is_object_in_taxonomy( get_post_type(), 'category' ) ) {
        $tagtext = __('','thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\">$tagtext",', ','</span> ');
    } elseif ( is_single() ) {
        $tagtext = __('','thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\">$tagtext",', ','</span> ');
    } elseif ( is_tag() && $tag_ur_it = thematic_tag_ur_it(', ') ) {
        $posttags = '<span class="tag-links">' . __('Also tagged ', 'thematic') . $tag_ur_it . '</span>' . "\n\n\t\t\t\t\t\t";
    } else {
        $tagtext = __('','thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\">$tagtext",', ','</span>' . "\n\n\t\t\t\t\t\t");
    }
    return apply_filters('thematic_postfooter_posttags',$posttags);
}



// override the nav below, it is now removed on single posts, mainly to better control internal linking (SEO)
function childtheme_override_nav_below() {
    if ( ! is_single() ) { ?>
        <div id="nav-below" class="navigation"> <?php
            if ( function_exists( 'wp_pagenavi' ) ) {
                wp_pagenavi();
             } else { ?>
            <div class="nav-previous"><?php next_posts_link(sprintf('<span class="meta-nav">&laquo;</span> %s', __('Older posts', 'thematic') ) ) ?></div>
            <div class="nav-next"><?php previous_posts_link(sprintf('%s <span class="meta-nav">&raquo;</span>',__( 'Newer posts', 'thematic') ) ) ?></div>
            <?php } ?>
        </div>  <?php
    }
}

// override functionality of Thematic HTML5 Plugin and Thematic
// originally H1 on Thematic HTML5 Plugin, originally H3 on Themaitc, changed this to an H4 for fun (SEO)

// filter the title opening of widget areas
function childtheme_before_widgettitle( $content ) {
    $content = "<h4 class=\"widgettitle\">";
    return $content;
}
add_filter( 'thematic_before_title', 'childtheme_before_widgettitle', 11 );

// filter the title closing of widget area
function childtheme_after_widgettitle( $content ) {
    $content = "</h4>\n";
    return $content;
}
add_filter( 'thematic_after_title', 'childtheme_after_widgettitle', 11 );



// add 4th subsidiary aside widget, currently set up to be a footer widget (#footer-widget) underneath the 3 subs
function childtheme_add_subsidiary($content) {
    $content['Footer Widget Aside'] = array(
        'admin_menu_order' => 550,
        'args' => array (
        'name' => 'Footer Aside',
        'id' => '4th-subsidiary-aside',
        'description' => __('The 4th bottom widget area in the footer.', 'thematic'),
        'before_widget' => thematic_before_widget(),
        'after_widget' => thematic_after_widget(),
        'before_title' => thematic_before_title(),
        'after_title' => thematic_after_title(),
            ),
        'action_hook'   => 'widget_area_subsidiaries',
        'function'      => 'childtheme_4th_subsidiary_aside',
        'priority'      => 90
        );
    return $content;
}
add_filter('thematic_widgetized_areas', 'childtheme_add_subsidiary');

// set structure for the 4th subsidiary aside, footer widget (#footer-widget)
function childtheme_4th_subsidiary_aside() {
    if ( is_active_sidebar('4th-subsidiary-aside') ) {
        echo thematic_before_widget_area('footer-widget');
        dynamic_sidebar('4th-subsidiary-aside');
        echo thematic_after_widget_area('footer-widget');
    }
}



// cuts the default size of the search input field down to cut overlap
// css sizes this fine, but it could be placed in things other than aside, this is back up. ;)
function childtheme_thematic_search_form_length() {
    return "16";
}
add_filter('thematic_search_form_length', 'childtheme_thematic_search_form_length');

// change the default search box text
function childtheme_search_field_value() {
    return "Search";
}
add_filter('search_field_value', 'childtheme_search_field_value');



// just because, wrap the site info in a p tag automatically
function childtheme_override_siteinfo() {
    echo "\t\t<p>" . do_shortcode( thematic_get_theme_opt( 'footer_txt' ) ) . "</p>\n";
}



/*
// load google analytics, optimized version
// http://mathiasbynens.be/notes/async-analytics-snippet
function childtheme_google_analytics(){ ?>
<script>var _gaq=[['_setAccount','UA-xxxxxxx-x'],['_trackPageview']];(function(d){var g=d.createElement('script'),s=d.scripts[0];g.src='//www.google-analytics.com/ga.js';s.parentNode.insertBefore(g,s)}(document))</script>
<?php }
add_action('wp_head', 'childtheme_google_analytics');
*/



/*
// hide dashboard widgets on admin screen
// http://www.deluxeblogtips.com/2011/01/remove-dashboard-widgets-in-wordpress.html
function childtheme_hide_dashboard_widgets() {
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // right now
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // recent comments
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // incoming links
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // plugins
    remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');  // quick press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');  // recent drafts
    remove_meta_box('dashboard_primary', 'dashboard', 'normal');   // wordpress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'normal');   // other wordpress news
}
add_action('admin_init', 'childtheme_hide_dashboard_widgets');
*/



/*
// hide unused widget areas on admin screen
function childtheme_hide_widgetized_areas($content) {
//    unset($content['Primary Aside']);
//    unset($content['Secondary Aside']);
//    unset($content['1st Subsidiary Aside']);
//    unset($content['2nd Subsidiary Aside']);
//    unset($content['3rd Subsidiary Aside']);
    unset($content['Index Top']);
    unset($content['Index Insert']);
    unset($content['Index Bottom']);
    unset($content['Single Top']);
    unset($content['Single Insert']);
    unset($content['Single Bottom']);
    unset($content['Page Top']);
    unset($content['Page Bottom']);
    return $content;
}
add_filter('thematic_widgetized_areas', 'childtheme_hide_widgetized_areas');
*/