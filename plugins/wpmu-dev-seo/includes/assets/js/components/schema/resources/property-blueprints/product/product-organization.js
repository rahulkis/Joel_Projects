import uniqueId from 'lodash-es/uniqueId';
import { __ } from '@wordpress/i18n';
import ProductContactPoint from './product-contact-point';
import ProductPostalAddress from './product-postal-address';

const id = uniqueId;
const ProductOrganization = {
	name: {
		id: id(),
		label: __('Name', 'wds'),
		type: 'TextFull',
		source: 'schema_settings',
		value: 'organization_name',
		description: __('The name of the organization.', 'wds'),
	},
	url: {
		id: id(),
		label: __('URL', 'wds'),
		type: 'URL',
		source: 'site_settings',
		value: 'site_url',
		description: __('The URL of the organization.', 'wds'),
	},
	logo: {
		id: id(),
		label: __('Logo', 'wds'),
		type: 'ImageObject',
		source: 'schema_settings',
		value: 'organization_logo',
		description: __('The logo of the organization.', 'wds'),
	},
	address: {
		id: id(),
		label: __('Addresses', 'wds'),
		labelSingle: __('Address', 'wds'),
		optional: true,
		description: __('The addresses of the organization.', 'wds'),
		properties: {
			0: {
				id: id(),
				type: 'PostalAddress',
				properties: ProductPostalAddress,
			},
		},
	},
	contactPoint: {
		id: id(),
		label: __('Contact Points', 'wds'),
		labelSingle: __('Contact Point', 'wds'),
		optional: true,
		description: __(
			'The contact points of the organization.',
			'wds'
		),
		properties: {
			0: {
				id: id(),
				type: 'ContactPoint',
				properties: ProductContactPoint,
			},
		},
	},
};
export default ProductOrganization;
