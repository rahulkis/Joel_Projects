import React from 'react';
import { __ } from '@wordpress/i18n';
import Button from '../../../components/button';
import TextInput from '../../../components/input-fields/text-input';
import { createInterpolateElement } from '@wordpress/element';
import GutenbergEditor from '../../../es6/gutenberg-editor';
import ClassicEditor from '../../../es6/classic-editor';
import { uniqueId } from 'lodash-es';
import Notice from '../../../components/notices/notice';
import ConfigValues from '../../../es6/config-values';

export default class FocusKeywords extends React.Component {
	static defaultProps = {
		keywords: [],
		loading: false,
		onUpdateKeywords: () => false,
	};

	constructor(props) {
		super(props);

		// Check if Gutenberg is active.
		if (ConfigValues.get_bool('gutenberg_active', 'metabox')) {
			this.editor = new GutenbergEditor();
		} else {
			this.editor = new ClassicEditor();
		}

		this.state = {
			newKeyword: '',
			loading: this.props.loading,
		};
	}

	handleNewKeyword(value) {
		this.setState({ newKeyword: value.trim() });
	}

	addKeyword() {
		const { keywords, onUpdateKeywords } = this.props;
		const { newKeyword } = this.state;

		if (
			!keywords.find(
				(value) => value.toLowerCase() === newKeyword.toLowerCase()
			)
		) {
			onUpdateKeywords([...keywords, newKeyword]);
		}

		this.setState({ newKeyword: '' });
	}

	removeKeyword(keyword) {
		const { keywords, onUpdateKeywords } = this.props;

		onUpdateKeywords(keywords.filter((kw) => keyword !== kw));
	}

	render() {
		const { keywords, loading } = this.props;
		const { newKeyword } = this.state;

		const focusInputId = uniqueId('wds-focus-input-');

		return (
			<div className="wds-focus-keyword sui-border-frame sui-form-field">
				<label className="sui-label wds-label" htmlFor={focusInputId}>
					{__('Focus keyword', 'wds')}
				</label>
				<p className="sui-description wds-description">
					{createInterpolateElement(
						__(
							'You can analyze the post content for up to 3 focus keywords. The SEO recommendations for each keyword will be displayed in separate tabs below. Enter each keyword you want to analyze and click the <strong>Add Keyword</strong> button, or enter multiple keywords separated by commas and click the <strong>Add Keyword</strong> button only once.',
							'wds'
						),
						{ strong: <strong /> }
					)}
				</p>
				<TextInput
					id={focusInputId}
					name="wds_focus_input"
					placeholder={__(
						'E.g. broken iphone screen',
						'wds'
					)}
					value={newKeyword}
					onChange={(value) => this.handleNewKeyword(value)}
					loading={loading}
					suffix={
						<Button
							id="wds_add_keyword"
							disabled={keywords.length > 2}
							loading={loading}
							text={__('Add Keyword(s)', 'wds')}
							onClick={() => this.addKeyword()}
						></Button>
					}
				></TextInput>
				<TextInput
					type="hidden"
					id="wds_focus"
					name="wds_focus"
					readOnly="readonly"
					value={keywords.join(',')}
				/>

				{keywords.length > 0 && (
					<div
						className="wds-added-keywords-tags"
						id="wds-added-keywords-tags"
					>
						<label className="sui-label">
							{__('Added keywords', 'wds')}
						</label>
						<div className="sui-pagination-active-filters">
							{keywords.map((keyword) => (
								<span
									className="sui-active-filter"
									key={keyword}
								>
									{keyword}
									<span
										className="sui-active-filter-remove wds-remove-keyword"
										onClick={() =>
											this.removeKeyword(keyword)
										}
									></span>
								</span>
							))}
						</div>
					</div>
				)}

				{!keywords.length && (
					<Notice
						type="inactive"
						className="wds-notice"
						icon="sui-icon-info"
						message={__(
							'You need to add focus keywords to see recommendations for this article.',
							'wds'
						)}
					></Notice>
				)}
			</div>
		);
	}
}
