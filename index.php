<?php
/**
 * Plugin Name:       Table of Contents
 * Description:       Improve readability and navigation of your websites with the Table of Contents Block.
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Version:           1.0.2
 * Author:            Achal Jain
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ib-block-toc
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */
function ideabox_toc_block_init() {
	register_block_type( __DIR__ );
}
add_action( 'init', 'ideabox_toc_block_init' );

function ideabox_toc_filter_render_block( $block_content, $block ) {
	if ( $block['blockName'] === 'ideabox/toc' ) {
		$attrs = $block['attrs'];
		$content = $block_content;

		$data = array(
			'anchors' => isset( $attrs['anchorsByTags'] ) ? implode( ',', $attrs['anchorsByTags'] ) : 'h2,h3,h4,h5,h6',
			'include' => isset( $attrs['includeContainer'] ) ? $attrs['includeContainer'] : null,
			'exclude' => isset( $attrs['excludeContainer'] ) ? $attrs['excludeContainer'] : null,
			'collapsable' => isset( $attrs['collapsable'] ) && ! $attrs['collapsable'] ? 'false' : 'true',
			'offset' => isset( $attrs['extraOffset'] ) ? $attrs['extraOffset'] : null,
		);

		$attr_string = '';

		foreach ( $data as $key => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}
			$attr_string .= "data-$key='$value' ";
		}

		preg_match( '/ class="wp-block-ideabox-toc.*?/', $content, $matches );

		if ( ! empty( $matches ) ) {
			$content = str_replace( $matches[0], $matches[0] . ' ib-block-toc', $content );

			preg_match( '/ class="wp-block-ideabox-toc.*?"/', $content, $matches );

			$content = str_replace( $matches[0], $matches[0] . ' ' . $attr_string, $content );
		} else {
			$content = '<div class="ib-block-toc"' . $attr_string . '>';
			$content .= $block_content;
			$content .= '</div>';
		}

        return $content;
    }

	return $block_content;
}
add_filter( 'render_block', 'ideabox_toc_filter_render_block', 10, 2 );
