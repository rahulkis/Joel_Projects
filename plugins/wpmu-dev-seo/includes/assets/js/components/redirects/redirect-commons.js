import ConfigValues from '../../es6/config-values';

export function getDefaultRedirectType() {
	return ConfigValues.get('default_redirect_type', 'autolinks');
}
