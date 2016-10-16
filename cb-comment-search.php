<?php
/**
 * Plugin Name: Comment Search
 * Description: Search a comment and its replies by comment ID
 * Plugin URI: http://medhabi.com
 * Author: Nazmul Ahsan
 * Author URI: http://nazmulashan.me
 * Version: 1.0
 * License: GPL2
 * Text Domain: cb-comment-search
 */

/*

Copyright (C) 2016  Nazmul Ahsan  n.mukto@gmail.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* if accessed directly, exit.
*/
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* Main class for the plugin
* @package @WordPress
* @subpackage CB_Comment_Search
* @author Nazmul Ahsan
*/
if( ! class_exists( 'CB_Comment_Search' ) ) :
class CB_Comment_Search {

	/**
	 * instance of the plugin
	 */
	public static $_instance;

	/**
	 * constructor function
	 * @return void
	 */
	public function __construct() {
		add_shortcode( 'comment_search', array( $this, 'comment_search' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_head', array( $this, 'ajaxurl' ) );
		add_action( 'wp_ajax_generate_comment', array( $this, 'generate_comment' ) );
		add_action( 'wp_ajax_nopriv_generate_comment', array( $this, 'generate_comment' ) );
	}

	/**
	 * html for comment search shortcode
	 * @return $html string
	 */
	public function comment_search() {
		$html = '
		<p>Please, fill in with your comment ID:</p>
		<form class="comment-search-form">
			<input type="text" id="comment-id" required />
			<input type ="submit" value ="Buscar" />
		</form>
		<div id="show_comments"></div>';
		return $html;
	}

	/**
	 * enqueue required js and css files
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'cb-comment-search', plugins_url( '/assets/js/script.min.js', __FILE__ ), array(), '1.0', true );
		wp_enqueue_style( 'cb-comment-search', plugins_url( '/assets/css/style.min.css', __FILE__ ) );
	}

	/**
	 * add ajaxurl variable to wp_head to be used in ajax
	 */
	public function ajaxurl() {
		?>
		<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		</script>
		<?php
	}

	/**
	 * generate comment from given ID
	 */
	public function generate_comment() {
		$comment = get_comment( $_POST['comment_id'] );
		if ( $comment ) { ?>
			<ol class="mdc-children children">
				<?php $this->single_comment( $comment ); ?>
			</ol>
			<?php
		} else {
			_e( '<p>No comments found!</p>' );
		}
		die();
	}

	/**
	 * generate replies to a given post
	 */
	function generate_replies( $comment ) {
		$replies = get_comments( array( 'parent' => $comment->comment_ID, 'status' => 'approve', 'order' => 'ASC' ) );
		if ( $replies ) { ?>
			<ol class="mdc-children children">
				<?php
				foreach ( $replies as $reply ) {
					$this->single_comment( $reply );
				} ?>
			</ol>
			<?php
		}
	}

	/**
	 * template of a single comment
	 * @param $comment the comment
	 */
	public function single_comment( $comment ) {
		?>
		<li id="li-comment-<?php echo $comment->comment_ID; ?>" class="comment depth-<?php echo $comment->comment_ID; ?>">
			<article id="comment-<?php echo $comment->comment_ID; ?>" class="comment">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<div class="show-avatar-left">
							<?php echo get_avatar( $comment, 48 ); ?>
						</div>
						<div class="show-meta-right">
						<span class="says"><a class="url" rel="external nofollow" href="<?php echo $comment->comment_author_url; ?>"> <?php echo $comment->comment_author; ?></a></span><br />								<span class="comment-time"><a href="<?php echo get_comment_link( $comment ); ?>"><?php echo date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $comment->comment_date ) ); ?></a></span>
						</div>
					</div>
				</footer>
				<div class="comment-content">
					<p><?php echo $comment->comment_content; ?></p>
				</div>
				<?php self::generate_replies( $comment ); ?>
			</article>
		</li>
		<?php
	}

	/**
	* Instantiate the plugin
	*/
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}
endif;

CB_Comment_Search::instance();
