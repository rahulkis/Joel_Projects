import React from 'react';
import Dropdown from '../dropdown';
import DropdownButton from '../dropdown-button';
import { __ } from '@wordpress/i18n';
import Checkbox from '../checkbox';
import classnames from 'classnames';

export default class RedirectItem extends React.Component {
	static defaultProps = {
		id: '',
		title: '',
		source: '',
		destination: '',
		type: '',
		options: [],
		selected: false,
		onToggle: () => false,
		onEdit: () => false,
		onDelete: () => false,
	};

	render() {
		const {
			selected,
			title,
			source,
			destination,
			type,
			onToggle,
			onEdit,
			onDelete,
		} = this.props;

		return (
			<div
				className={classnames('wds-redirect-item sui-builder-field', {
					'wds-redirect-has-title': !!title,
				})}
			>
				<div className="wds-redirect-item-checkbox">
					<Checkbox
						checked={selected}
						onChange={(isChecked) => onToggle(isChecked)}
					/>
				</div>

				<div className="wds-redirect-item-source">
					<div className="sui-tooltip" data-tooltip={source}>
						<div className="wds-redirect-item-source-trimmed">
							{source}
						</div>
					</div>
					{title && <small>{title}</small>}
				</div>

				<div className="wds-redirect-item-destination">
					<small>{destination}</small>
				</div>

				<div className="wds-redirect-item-options">
					{type === 301 && (
						<span className="sui-tag sui-tag-sm">
							{__('Permanent', 'wds')}
						</span>
					)}
					{type === 302 && (
						<span className="sui-tag sui-tag-sm">
							{__('Temporary', 'wds')}
						</span>
					)}
					{this.options()}
				</div>

				<div className="wds-redirect-item-dropdown">
					<Dropdown
						buttons={[
							<DropdownButton
								key={0}
								className="wds-edit-redirect-item"
								icon="sui-icon-pencil"
								text={__('Edit', 'wds')}
								onClick={() => onEdit()}
							/>,
							<DropdownButton
								key={1}
								className="wds-remove-redirect-item"
								icon="sui-icon-trash"
								text={__('Remove', 'wds')}
								red={true}
								onClick={() => onDelete()}
							/>,
						]}
					/>
				</div>
			</div>
		);
	}

	options() {
		const labels = {
			regex: __('Regex', 'wds'),
		};

		return this.props.options.map((option) => (
			<span className="sui-tag sui-tag-yellow sui-tag-sm" key={option}>
				{labels[option]}
			</span>
		));
	}
}
