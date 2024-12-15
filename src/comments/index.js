import domReady from '@wordpress/dom-ready';
import { getBlockType, unregisterBlockType } from '@wordpress/blocks';

domReady( () => {
	const blocks = [
		'core/comment-author-name',
		'core/comment-content',
		'core/comment-date',
		'core/comment-edit-link',
		'core/comment-reply-link',
		'core/comment-template',
		'core/comments',
		'core/comments-pagination',
		'core/comments-pagination-next',
		'core/comments-pagination-numbers',
		'core/comments-pagination-previous',
		'core/comments-title',
		'core/latest-comments',
		'core/post-comments-form',
		'core/post-comments',
	];

	blocks.forEach( ( block ) => {
		if ( undefined !== getBlockType( block ) ) {
			unregisterBlockType( block );
		}
	} );
} );
