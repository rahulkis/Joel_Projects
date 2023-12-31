<template>
	<div class="sui-box">
		<box-header :title="$i18n.label.account" />
		<div class="sui-box-body">
			<p>{{ $i18n.desc.account }}</p>

			<!-- API status notice -->
			<sui-notice v-if="getApiErrorMessage" type="error">
				<p>{{ sprintf( $i18n.notice.api_error, getApiErrorMessage ) }}</p>
			</sui-notice>

			<!-- Account setup notice -->
			<sui-notice type="info" v-if="showFullNotice">
				<p
					v-html="
						sprintf(
							$i18n.notice.account_setup_both,
							$vars.urls.statistics
						)
					"
				></p>
			</sui-notice>
			<sui-notice type="info" v-else-if="showAccountNotice">
				<p
					v-html="
						sprintf(
							$i18n.notice.account_setup_login,
							$vars.urls.statistics
						)
					"
				></p>
			</sui-notice>
			<sui-notice type="info" v-else-if="showTrackingNotice">
				<p
					v-html="
						sprintf(
							$i18n.notice.account_setup_tracking,
							'&#60;head&#62;',
							$vars.urls.statistics
						)
					"
				></p>
			</sui-notice>

			<div class="sui-box-settings-slim-row">
				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label">
						{{ $i18n.label.analytics_4_profile }}
					</span>
					<span class="sui-description">
						{{ $i18n.desc.analytics_4_profile }}
					</span>
				</div>
				<div class="sui-box-settings-col-2">
					<!-- Profile/View selection -->
					<streams-connected v-if="isConnected" />
					<streams-disconnected v-else />
				</div>
			</div>

			<div class="sui-box-settings-row">
				<div class="sui-box-settings-col-1">
					<span v-if="isNetwork()" class="sui-settings-label">
						{{ $i18n.label.network_measurement_id }}
					</span>
					<span v-else class="sui-settings-label">
						{{ $i18n.label.measurement_id }}
					</span>
					<span class="sui-description">
						{{ $i18n.desc.analytics_4 }}
					</span>
					<span v-if="isNetwork()" class="sui-description">
						{{ $i18n.desc.network_measurement_id }}
					</span>
					<span v-else class="sui-description">
						{{ $i18n.desc.measurement_id }}
					</span>
				</div>
				<div class="sui-box-settings-col-2">
					<!-- Show automatic measurement id -->
					<measurement-automatic v-if="showAutoMeasurement"/>
					<!-- Show manual measurement id -->
					<measurement-manual
						:error="error.measurement"
						@validation="measurementValidation"
						v-else
					/>
				</div>
			</div>

			<div class="sui-box-settings-slim-row">
				<div class="sui-box-settings-col-1">
					<span class="sui-settings-label">
						{{ $i18n.label.analytics_profile }}
					</span>
					<span class="sui-description">
						{{ $i18n.desc.analytics_profile }}
					</span>
				</div>
				<div class="sui-box-settings-col-2">
					<!-- Profile/View selection -->
					<profiles-connected v-if="isConnected" />
					<profiles-disconnected v-else />
				</div>
			</div>

			<div class="sui-box-settings-row">
				<div class="sui-box-settings-col-1">
					<span v-if="isNetwork()" class="sui-settings-label">
						{{ $i18n.label.network_tracking_id }}
					</span>
					<span v-else class="sui-settings-label">
						{{ $i18n.label.tracking_id }}
					</span>
					<span v-if="isNetwork()" class="sui-description">
						{{ $i18n.desc.network_tracking }}
					</span>
					<span v-else class="sui-description">
						{{ $i18n.desc.tracking_statistics }}
					</span>
				</div>
				<div class="sui-box-settings-col-2">
					<!-- Show automatic tracking -->
					<tracking-automatic v-if="showAutoTracking" />
					<!-- Show manual tracking -->
					<tracking-manual
						v-else
						:error="error.tracking"
						@validation="trackingValidation"
					/>
				</div>
			</div>
		</div>
		<box-footer :processing="processing" @submit="formSubmit" />
	</div>
</template>

<script>
import SuiNotice from '@/components/sui/sui-notice'
import TrackingManual from './account/tracking-manual'
import BoxHeader from '@/components/elements/box-header'
import BoxFooter from '@/components/elements/box-footer'
import StreamsConnected from './account/streams-connected'
import { isValidGAID, isValidGA4ID } from '@/helpers/utils'
import TrackingAutomatic from './account/tracking-automatic'
import MeasurementManual from './account/measurement-manual'
import ProfilesConnected from './account/profiles-connected'
import StreamsDisconnected from './account/streams-disconnected'
import MeasurementAutomatic from './account/measurement-automatic'
import ProfilesDisconnected from './account/profiles-disconnected'

export default {
	name: 'Account',

	components: {
		BoxHeader,
		BoxFooter,
		SuiNotice,
		TrackingManual,
		StreamsConnected,
		TrackingAutomatic,
		ProfilesConnected,
		MeasurementManual,
		StreamsDisconnected,
		MeasurementAutomatic,
		ProfilesDisconnected,
	},

	props: {
		processing: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			error: {
				tracking: false,
				measurement: false,
			},
			valid: {
				tracking: true,
				measurement: true,
			},
		}
	},

	computed: {
		/**Eh
		 * Check if Google account is connected.
		 *
		 * @since 3.3.0
		 *
		 * @returns {boolean}
		 */
		isConnected() {
			return this.$store.state.helpers.google.logged_in
		},

		/**
		 * Check if we can show automatic tracking ID.
		 *
		 * @since 3.3.0
		 *
		 * @return {boolean}
		 */
		showAutoTracking() {
			let account = this.getOption('account_id', 'google')
			let autoTrack = this.getOption('auto_track', 'google')
			let autoTrackId = this.getOption('auto_track', 'misc')

			return account && autoTrack && autoTrackId && this.isConnected
		},

		/**
		 * Check if we can show automatic measurement ID.
		 *
		 * @since 3.4.0
		 *
		 * @return {boolean}
		 */
		showAutoMeasurement() {
			let stream = this.getOption('stream', 'google')
			let autoMeasurement = this.getOption('auto_track_ga4', 'google')
			let autoMeasurementId = this.getOption('auto_track_ga4', 'misc')

			return stream && autoMeasurement && autoMeasurementId && this.isConnected
		},

		/**
		 * Check if we can show the connection notice.
		 *
		 * When everything is setup, appreciate the user.
		 *
		 * @since 3.2.7
		 */
		showFullNotice() {
			return this.showAccountNotice && this.showTrackingNotice
		},

		/**
		 * Check if Google account is connected.
		 *
		 * @since 3.2.7
		 */
		showAccountNotice() {
			let account = this.getOption('account_id', 'google', 0)
			let loggedIn = this.$store.state.helpers.google.logged_in

			return loggedIn && '' !== account && 0 != account
		},

		/**
		 * Get API error if any.
		 *
		 * @since 3.4.0
		 */
		getApiErrorMessage() {
			return this.getOption('api_error', 'google', false)
		},

		/**
		 * Check if tracking ID is setup.
		 *
		 * @since 3.2.7
		 */
		showTrackingNotice() {
			let trackId = this.getOption('code', 'tracking', '')
			let measurementId = this.getOption('measurement', 'tracking', '')
			let autoTrack = this.getOption('auto_track', 'google')
			let autoTrackId = this.getOption('auto_track', 'misc', '')

			return (
				('' !== trackId && isValidGAID(trackId)) ||
				('' !== measurementId && isValidGA4ID(measurementId)) ||
				(autoTrack && '' !== autoTrackId && isValidGAID(autoTrackId))
			)
		},
	},

	methods: {
		/**
		 * On tracking code validation process.
		 *
		 * @param {object} data Custom data.
		 *
		 * @since 3.2.4
		 */
		trackingValidation(data) {
			this.valid.tracking = data.valid

			if (data.valid) {
				this.error.tracking = false
			}
		},

		/**
		 * On tracking code validation process.
		 *
		 * @param {object} data Validation data.
		 *
		 * @since 3.3.3
		 */
		measurementValidation(data) {
			this.valid.measurement = data.valid

			if (data.valid) {
				this.error.measurement = false
			}
		},

		/**
		 * Save settings values using API.
		 *
		 * @param {string} tab Current tab.
		 *
		 * @since 3.2.4
		 */
		formSubmit(tab) {
			if (this.valid.tracking && this.valid.measurement) {
				this.error = {
					tracking: false,
					measurement: false,
				}

				// Save settings.
				this.$emit('submit')
			} else {
				this.error.tracking = !this.valid.tracking
				this.error.measurement = !this.valid.measurement
			}
		},
	},
}
</script>
