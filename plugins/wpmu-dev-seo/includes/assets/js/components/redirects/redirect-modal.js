import React from 'react';
import Modal from '../modal';
import { __, sprintf } from '@wordpress/i18n';
import { createInterpolateElement } from '@wordpress/element';
import fieldWithValidation from '../field-with-validation';
import TextInputField from '../form-fields/text-input-field';
import {
	isNonEmpty,
	isRegexStringValid,
	isRelativeUrlValid,
	isUrlValid,
	isValuePlainText,
	Validator,
} from '../../utils/validators';
import SelectField from '../form-fields/select-field';
import Button from '../button';
import { getDefaultRedirectType } from './redirect-commons';
import AccordionItem from '../accordion-item';
import AccordionItemOpenIndicator from '../accordion-item-open-indicator';
import SideTabsField from '../side-tabs-field';
import Notice from '../notices/notice';

export default class RedirectModal extends React.Component {
	static defaultProps = {
		editMode: false,
		id: '',
		title: '',
		source: '',
		destination: '',
		homeUrl: '',
		type: getDefaultRedirectType(),
		options: [],
		inProgress: false,
		onSave: () => false,
		onClose: () => false,
	};

	constructor(props) {
		super(props);

		const fieldsInitiallyValid = this.props.editMode;

		this.state = {
			title: this.props.title,
			isTitleValid: true,
			source: this.props.source,
			isSourceValid: fieldsInitiallyValid,
			sourceTouched: false,
			destination: this.props.destination,
			isDestinationValid: fieldsInitiallyValid,
			type: this.props.type,
			options: this.props.options,
		};

		this.titleField = fieldWithValidation(TextInputField, [
			isValuePlainText,
		]);
		this.sourceField = fieldWithValidation(TextInputField, [
			isNonEmpty,
			isValuePlainText,
			this.urlValidator(),
			this.sourceContainsHomeUrlValidator(),
		]);
		this.sourceRegexField = fieldWithValidation(TextInputField, [
			isNonEmpty,
			new Validator(
				isRegexStringValid,
				__('This regex is invalid.', 'wds')
			),
		]);
		this.destinationField = fieldWithValidation(TextInputField, [
			isNonEmpty,
			isValuePlainText,
			this.urlValidator(),
		]);
	}

	sourceContainsHomeUrlValidator() {
		return new Validator((url) => {
			const isRelative = isRelativeUrlValid(url);
			const startsWithHome = url.startsWith(this.props.homeUrl);

			return isRelative || startsWithHome;
		}, __('You need to enter a URL belonging to the current site.', 'wds'));
	}

	urlValidator() {
		return new Validator(
			isUrlValid,
			__(
				'You need to use an absolute URL like https://domain.com/new-url or start with a slash /new-url.',
				'wds'
			)
		);
	}

	handleTitleChange(title, isValid) {
		this.setState({
			title,
			isTitleValid: isValid,
		});
	}

	handleSourceChange(source, isValid) {
		this.setState({
			source,
			isSourceValid: isValid,
			sourceTouched: true,
		});
	}

	handleDestinationChange(destination, isValid) {
		this.setState({
			destination,
			isDestinationValid: isValid,
		});
	}

	handleOptionChange(option, value) {
		let options = [...this.state.options];
		if (value) {
			if (!options.includes(option)) {
				options.push(option);
			}
		} else {
			options = options.filter((element) => element !== option);
		}
		this.setState({ options });
	}

	render() {
		const {
			source,
			isSourceValid,
			sourceTouched,
			destination,
			isDestinationValid,
			type,
			title,
			isTitleValid,
			options,
		} = this.state;
		const { id, inProgress, onClose, onSave, homeUrl } = this.props;
		const onSubmit = () =>
			onSave({
				id,
				title: title.trim(),
				source: source.trim(),
				destination: destination.trim(),
				type,
				options,
			});
		const submissionDisabled =
			!isTitleValid ||
			!isSourceValid ||
			!isDestinationValid ||
			inProgress;
		const isRegex = options.includes('regex');
		const TitleField = this.titleField;
		const SourceField = isRegex ? this.sourceRegexField : this.sourceField;
		const DestinationField = this.destinationField;
		const maybeIsRegex = /[\[*^$\\{|]/g.test(source);
		const modalDescription = isRegex
			? // translators: %s: Home url.
			  __(
					'Regex redirects will only match absolute URLs, such as <strong>%s/cats</strong>.',
					'wds'
			  )
			: // translators: %s: Home url.
			  __(
					'Allowed formats include relative URLs like <strong>/cats</strong> or absolute URLs such as <strong>%s/cats</strong>.',
					'wds'
			  );
		const sourcePlaceholder = isRegex
			? sprintf(
					// translators: %s: Home url.
					__('E.g. %s/(.*)-cats', 'wds'),
					this.removeTrailingSlash(homeUrl)
			  )
			: __('E.g. /cats', 'wds');

		return (
			<Modal
				id="wds-add-redirect-form"
				title={__('Add Redirect', 'wds')}
				description={createInterpolateElement(
					sprintf(
						modalDescription,
						this.removeTrailingSlash(homeUrl)
					),
					{
						strong: <strong />,
					}
				)}
				onEnter={onSubmit}
				onClose={onClose}
				disableCloseButton={inProgress}
				enterDisabled={submissionDisabled}
				focusAfterOpen="wds-source-field"
				focusAfterClose="wds-add-redirect-dashed-button"
				dialogClasses={{
					'sui-modal-md': true,
					'sui-modal-sm': false,
				}}
				small={true}
			>
				<SourceField
					id="wds-source-field"
					label={__('Old URL', 'wds')}
					description={
						isRegex
							? __(
									'Enter regex to match absolute URLs.',
									'wds'
							  )
							: ''
					}
					value={source}
					placeholder={sourcePlaceholder}
					onChange={(src, isValid) =>
						this.handleSourceChange(src, isValid)
					}
					disabled={inProgress}
					validateOnInit={sourceTouched || isNonEmpty(source)}
				/>

				{maybeIsRegex && !isRegex && (
					<Notice
						type="info"
						message={createInterpolateElement(
							__(
								'To configure a regex redirect, you must first select <strong>Regex</strong> in the Advanced settings below.',
								'wds'
							),
							{
								strong: <strong />,
							}
						)}
					/>
				)}

				<DestinationField
					label={__('New URL', 'wds')}
					value={destination}
					placeholder={__('E.g. /cats-new', 'wds')}
					onChange={(dest, isValid) =>
						this.handleDestinationChange(dest, isValid)
					}
					disabled={inProgress}
				/>

				<SelectField
					label={__('Redirect Type', 'wds')}
					description={__(
						'This tells search engines whether to keep indexing the old page, or replace it with the new page.',
						'wds'
					)}
					options={{
						302: __('Temporary', 'wds'),
						301: __('Permanent', 'wds'),
					}}
					selectedValue={type}
					onSelect={(tp) => this.setState({ type: tp })}
					disabled={inProgress}
				/>

				<div className="sui-accordion sui-accordion-flushed">
					<AccordionItem
						header={
							<React.Fragment>
								<div className="sui-accordion-item-title sui-accordion-col-10">
									{__('Advanced', 'wds')}
								</div>
								<div className="sui-accordion-col-2">
									<AccordionItemOpenIndicator />
								</div>
							</React.Fragment>
						}
					>
						<TitleField
							id="wds-title-field"
							label={
								<span>
									<span>{__('Label', 'wds')}</span>{' '}
									{__('(Optional)', 'wds')}
								</span>
							}
							description={__(
								'Use labels to differentiate long or similar URLs.',
								'wds'
							)}
							value={title}
							placeholder={__(
								'E.g. Press release',
								'wds'
							)}
							onChange={(tl, isValid) =>
								this.handleTitleChange(tl, isValid)
							}
							disabled={inProgress}
						/>

						<SideTabsField
							label={__('Regular Expression', 'wds')}
							description={createInterpolateElement(
								__(
									'Choose whether the strings entered into the Old URL and New URL fields above should be treated as plain text URLs or regular expressions (Regex). Note that only valid regular expressions are allowed. <a>Learn more</a> about Regex.',
									'wds'
								),
								{
									a: (
										<a
											target="_blank"
											href="https://wpmudev.com/docs/wpmu-dev-plugins/smartcrawl/#about-regex-redirects"
											rel="noreferrer"
										/>
									),
								}
							)}
							tabs={{
								0: __('Plain Text', 'wds'),
								1: __('Regex', 'wds'),
							}}
							value={isRegex ? '1' : '0'}
							onChange={(checked) =>
								this.handleOptionChange(
									'regex',
									checked === '1'
								)
							}
						/>
					</AccordionItem>
				</div>

				<div
					style={{ display: 'flex', justifyContent: 'space-between' }}
				>
					<Button
						text={__('Cancel', 'wds')}
						ghost={true}
						onClick={onClose}
						disabled={inProgress}
					/>

					<Button
						text={__('Apply Redirect', 'wds')}
						color="blue"
						onClick={onSubmit}
						icon="sui-icon-save"
						disabled={submissionDisabled}
						loading={inProgress}
					/>
				</div>
			</Modal>
		);
	}

	removeTrailingSlash(url) {
		return url.endsWith('/') ? url.slice(0, -1) : url;
	}
}
