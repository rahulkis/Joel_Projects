import uniqueId from 'lodash-es/uniqueId';
import { __ } from '@wordpress/i18n';

const id = uniqueId;
const ProductReview = {
	itemReviewed: {
		id: id(),
		label: __('Reviewed Item', 'wds'),
		flatten: true,
		required: true,
		properties: {
			name: {
				id: id(),
				label: __('Reviewed Item', 'wds'),
				type: 'TextFull',
				source: 'post_data',
				value: 'post_title',
				disallowDeletion: true,
				required: true,
				description: __(
					'Name of the item that is being rated. In this case the product.',
					'wds'
				),
			},
		},
	},
	reviewBody: {
		id: id(),
		label: __('Review Body', 'wds'),
		type: 'Text',
		source: 'custom_text',
		value: '',
		disallowDeletion: true,
		description: __('The actual body of the review.', 'wds'),
	},
	datePublished: {
		id: id(),
		label: __('Date Published', 'wds'),
		type: 'DateTime',
		source: 'datetime',
		value: '',
		disallowDeletion: true,
		description: __(
			'The date that the review was published, in ISO 8601 date format.',
			'wds'
		),
	},
	author: {
		id: id(),
		label: __('Author', 'wds'),
		activeVersion: 'Person',
		required: true,
		properties: {
			Person: {
				id: id(),
				label: __('Author', 'wds'),
				disallowDeletion: true,
				disallowAddition: true,
				type: 'Person',
				properties: {
					name: {
						id: id(),
						label: __('Name', 'wds'),
						type: 'TextFull',
						source: 'custom_text',
						value: '',
						description: __(
							'The name of the review author.',
							'wds'
						),
						disallowDeletion: true,
					},
					url: {
						id: id(),
						label: __('URL', 'wds'),
						type: 'URL',
						source: 'custom_text',
						value: '',
						description: __(
							"The URL to the review author's page.",
							'wds'
						),
						disallowDeletion: true,
					},
					description: {
						id: id(),
						label: __('Description', 'wds'),
						type: 'TextFull',
						source: 'custom_text',
						value: '',
						optional: true,
						description: __(
							'Short bio/description of the review author.',
							'wds'
						),
						disallowDeletion: true,
					},
					image: {
						id: id(),
						label: __('Image', 'wds'),
						type: 'ImageObject',
						source: 'image',
						value: '',
						description: __(
							'An image of the review author.',
							'wds'
						),
						disallowDeletion: true,
					},
				},
				required: true,
				description: __(
					"The author of the review. The reviewer's name must be a valid name.",
					'wds'
				),
				isAnAltVersion: true,
			},
			Organization: {
				id: id(),
				label: __('Author Organization', 'wds'),
				disallowDeletion: true,
				disallowAddition: true,
				type: 'Organization',
				properties: {
					logo: {
						id: id(),
						label: __('Logo', 'wds'),
						type: 'ImageObject',
						source: 'image',
						value: '',
						description: __(
							'The logo of the organization.',
							'wds'
						),
						disallowDeletion: true,
					},
					name: {
						id: id(),
						label: __('Name', 'wds'),
						type: 'TextFull',
						source: 'custom_text',
						value: '',
						description: __(
							'The name of the organization.',
							'wds'
						),
						disallowDeletion: true,
					},
					url: {
						id: id(),
						label: __('URL', 'wds'),
						type: 'URL',
						source: 'custom_text',
						value: '',
						description: __(
							'The URL of the organization.',
							'wds'
						),
						disallowDeletion: true,
					},
				},
				required: true,
				description: __(
					"The author of the review. The reviewer's name must be a valid name.",
					'wds'
				),
				isAnAltVersion: true,
			},
		},
	},
	reviewRating: {
		id: id(),
		label: __('Rating', 'wds'),
		type: 'Rating',
		disallowAddition: true,
		disallowDeletion: true,
		properties: {
			ratingValue: {
				id: id(),
				label: __('Rating Value', 'wds'),
				type: 'Text',
				source: 'custom_text',
				value: '',
				disallowDeletion: true,
				description: __(
					'A numerical quality rating for the item, either a number, fraction, or percentage (for example, "4", "60%", or "6 / 10").',
					'wds'
				),
				required: true,
			},
			bestRating: {
				id: id(),
				label: __('Best Rating', 'wds'),
				type: 'Text',
				source: 'custom_text',
				value: '',
				disallowDeletion: true,
				description: __(
					'The highest value allowed in this rating system. If omitted, 5 is assumed.',
					'wds'
				),
			},
			worstRating: {
				id: id(),
				label: __('Worst Rating', 'wds'),
				type: 'Text',
				source: 'custom_text',
				value: '',
				disallowDeletion: true,
				description: __(
					'The lowest value allowed in this rating system. If omitted, 1 is assumed.',
					'wds'
				),
			},
		},
		required: true,
		description: __('The rating given in this review.', 'wds'),
	},
};
export default ProductReview;
