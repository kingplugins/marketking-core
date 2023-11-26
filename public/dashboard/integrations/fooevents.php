<?php

class Marketking_Foo{

    // Woo_Vou PDF Vouchers
    function foo_scripts(){
        $fooconfig = new FooEvents_Config();

        global $wp_locale;
        global $woocommerce;
        global $post;

        $woocommerce_currency_symbol = '';
        if ( class_exists( 'WooCommerce' ) ) {
            $woocommerce_currency_symbol = get_woocommerce_currency_symbol();
        }
        $fooevents_obj = array(
            'closeText'       => __( 'Done', 'woocommerce-events' ),
            'currentText'     => __( 'Today', 'woocommerce-events' ),
            'monthNames'      => strip_array_indices( $wp_locale->month ),
            'monthNamesShort' => strip_array_indices( $wp_locale->month_abbrev ),
            'monthStatus'     => __( 'Show a different month', 'woocommerce-events' ),
            'dayNames'        => strip_array_indices( $wp_locale->weekday ),
            'dayNamesShort'   => strip_array_indices( $wp_locale->weekday_abbrev ),
            'dayNamesMin'     => strip_array_indices( $wp_locale->weekday_initial ),
            'dateFormat'      => date_format_php_to_js( get_option( 'date_format' ) ),
            'firstDay'        => get_option( 'start_of_week' ),
            'isRTL'           => $wp_locale->is_rtl(),
            'currencySymbol'  => $woocommerce_currency_symbol,
        );

        $local_reminders_args = array(
            'minutesValue' => __( 'minutes', 'woocommerce-events' ),
            'hoursValue'   => __( 'hours', 'woocommerce-events' ),
            'daysValue'    => __( 'days', 'woocommerce-events' ),
            'weeksValue'   => __( 'weeks', 'woocommerce-events' ),
        );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-tooltip' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'wp-color-picker' );

        wp_enqueue_script( 'woocommerce-events-timepicker-script', $fooconfig->scripts_path . 'jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker' ), $fooconfig->plugin_data['Version'], true );
        wp_enqueue_script( 'woocommerce-events-admin-script', $fooconfig->scripts_path . 'events-admin.js', array( 'jquery', 'jquery-ui-datepicker', 'woocommerce-events-timepicker-script', 'wp-color-picker' ), $fooconfig->plugin_data['Version'], true );

        wp_localize_script( 'woocommerce-events-admin-script', 'FooEventsObj', $fooevents_obj );

        wp_localize_script( 'woocommerce-events-admin-script', 'localRemindersObj', $local_reminders_args );

        if (class_exists('Fooevents_Multiday_Events')){
            $foomultidayconfig = new Fooevents_Multiday_Events_Config();
            wp_enqueue_script( 'events-multi-day-script', $foomultidayconfig->scripts_path . 'events-multi-day-admin.js', array( 'jquery-ui-datepicker', 'wp-color-picker' ), $foomultidayconfig->plugin_data['Version'], true );

            $day_term = '';
            //phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( ! empty( $_GET['post'] ) ) {

                //phpcs:ignore WordPress.Security.NonceVerification.Recommended
                $post_id  = sanitize_text_field( wp_unslash( $_GET['post'] ) );
                $day_term = get_post_meta( $post_id, 'WooCommerceEventsDayOverride', true );

            }

            if ( empty( $day_term ) ) {

                $day_term = get_option( 'WooCommerceEventsDayOverride', true );

            }

            if ( empty( $day_term ) || 1 == $day_term ) {

                $day_term = __( 'Day', 'fooevents-multiday-events' );

            }

                $local_args = array(
                    'closeText'       => __( 'Done', 'woocommerce-events' ),
                    'currentText'     => __( 'Today', 'woocommerce-events' ),
                    'monthNames'      => strip_array_indices( $wp_locale->month ),
                    'monthNamesShort' => strip_array_indices( $wp_locale->month_abbrev ),
                    'monthStatus'     => __( 'Show a different month', 'woocommerce-events' ),
                    'dayNames'        => strip_array_indices( $wp_locale->weekday ),
                    'dayNamesShort'   => strip_array_indices( $wp_locale->weekday_abbrev ),
                    'dayNamesMin'     => strip_array_indices( $wp_locale->weekday_initial ),
                    'dateFormat'      => date_format_php_to_js( get_option( 'date_format' ) ),
                    'firstDay'        => get_option( 'start_of_week' ),
                    'isRTL'           => $wp_locale->is_rtl(),
                    'dayTerm'         => esc_attr( $day_term ),
                    'startTimeTerm'   => __( 'Start time', 'fooevents-multiday-events' ),
                    'endTimeTerm'     => __( 'End time', 'fooevents-multiday-events' ),
                );

                wp_localize_script( 'events-multi-day-script', 'localObjMultiDay', $local_args );
        }
       

        $local_args_print = array(
            'ajaxurl'         => admin_url( 'admin-ajax.php' ),
            'ajaxSaveSuccess' => __( 'Your stationery settings have been saved.', 'woocommerce-events' ),
            'ajaxSaveError'   => __( 'An error occurred while saving your stationery settings.', 'woocommerce-events' ),
        );

        wp_enqueue_script( 'woocommerce-events-printing-admin-script', $fooconfig->scripts_path . 'events-printing-admin.js', array( 'jquery' ), $fooconfig->plugin_data['Version'], true );
        wp_localize_script( 'woocommerce-events-printing-admin-script', 'localObjPrint', $local_args_print );

        add_action('wp_footer', function(){
            global $marketking_foo_already_ran;
            if ($marketking_foo_already_ran !== 'yes'){
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){
                        <?php 
                        $fooconfig = new FooEvents_Config();
                        $bundle_script = file_get_contents($fooconfig->scripts_path . 'events-admin.js');
                        echo $bundle_script; 

                        $bundle_script = file_get_contents($fooconfig->scripts_path . 'events-printing-admin.js');
                        echo $bundle_script; 


                        $foomultidayconfig = new Fooevents_Multiday_Events_Config();
                        $bundle_script = file_get_contents($foomultidayconfig->scripts_path . 'events-multi-day-admin.js');
                        echo $bundle_script; 

                        ?>
                    });
                </script>
                <style>
                    #fooevents_printing_widgets .fooevents_printing_widget {
                        width: 24% !important;
                    }
                </style>
                <?php
                $marketking_foo_already_ran = 'yes';
            }
            
        }, 1000);

        if ( isset( $_GET['page'] ) && 'fooevents-settings' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_media();
        }
        if ( isset( $_GET['page'] ) && 'fooevents-event-report' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_script( 'woocommerce-events-chartist', $fooconfig->scripts_path . 'chartist.min.js', array( 'jquery' ), '0.11.3', true );
            wp_enqueue_script( 'woocommerce-events-chartist-tooltip', $fooconfig->scripts_path . 'chartist-plugin-tooltip.min.js', array( 'jquery', 'woocommerce-events-chartist' ), '0.0.18', true );

            wp_enqueue_script( 'woocommerce-events-report', $fooconfig->scripts_path . 'events-reports.js', array( 'jquery', 'woocommerce-events-chartist' ), $fooconfig->plugin_data['Version'], true );
            wp_localize_script( 'woocommerce-events-report', 'FooEventsReportsObj', $fooevents_obj );
        }

        if ( isset( $_GET['post_type'] ) && 'event_magic_tickets' === $_GET['post_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            $add_ticket_args = array(
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'adminURL'      => get_admin_url(),
                'eventOverview' => __( 'Event Overview', 'woocommerce-events' ),
                'selectEvent'   => __( 'Select an event in the <strong>Event Details</strong> section.', 'woocommerce-events' ),
            );

            wp_enqueue_script( 'woocommerce-events-select', $fooconfig->scripts_path . 'select2.min.js', array( 'jquery' ), '4.0.12', true );
            wp_enqueue_script( 'woocommerce-events-admin-select', $fooconfig->scripts_path . 'event-admin-select.js', array( 'jquery', 'woocommerce-events-select' ), $fooconfig->plugin_data['Version'], true );

            wp_enqueue_script( 'woocommerce-events-admin-add-ticket', $fooconfig->scripts_path . 'events-admin-add-ticket.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-color-picker' ), $fooconfig->plugin_data['Version'], true );
            wp_localize_script( 'woocommerce-events-admin-add-ticket', 'FooEventsBookingsAddTicketObj', $add_ticket_args );
        }

        if ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_script( 'woocommerce-events-admin-edit-ticket', $fooconfig->scripts_path . 'events-admin-edit-ticket.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-color-picker' ), $fooconfig->plugin_data['Version'], true );
            wp_localize_script( 'woocommerce-events-admin-edit-ticket', 'FooEventsBookingsEditTicketObj', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
        }

        wp_enqueue_script( 'woocommerce-events-select', $fooconfig->scripts_path . 'select2.min.js', array( 'jquery' ), '4.0.12', true );
        wp_enqueue_script( 'woocommerce-events-admin-select', $fooconfig->scripts_path . 'event-admin-select.js', array( 'jquery', 'woocommerce-events-select' ), $fooconfig->plugin_data['Version'], true );

        $zoom_args = array(
            'testAccess'                            => __( 'Test Access', 'woocommerce-events' ),
            'testingAccess'                         => __( 'Testing Access...', 'woocommerce-events' ),
            'successFullyConnectedZoomAccount'      => __( 'Successfully connected to your Zoom account', 'woocommerce-events' ),
            'fetchUsers'                            => __( 'Fetch Users', 'woocommerce-events' ),
            'fetchingUsers'                         => __( 'Fetching Users...', 'woocommerce-events' ),
            'userOptionMe'                          => __( 'Show only meetings/webinars for the user that generated the API Key and Secret', 'woocommerce-events' ),
            'userOptionSelect'                      => __( 'Show all meetings/webinars created by the following users:', 'woocommerce-events' ),
            'userLoadTimes'                         => __( 'Please note that meeting/webinar load times will increase as more users are selected.', 'woocommerce-events' ),
            'adminURL'                              => get_admin_url(),
            'pluginsURL'                            => plugins_url(),
            'notSet'                                => __( 'Not set', 'woocommerce-events' ),
            'autoGenerate'                          => __( 'Auto-generate', 'woocommerce-events' ),
            'topic'                                 => __( 'Topic', 'woocommerce-events' ),
            'description'                           => __( 'Description', 'woocommerce-events' ),
            'date'                                  => __( 'Date', 'woocommerce-events' ),
            'startDate'                             => __( 'Start date', 'woocommerce-events' ),
            'endDate'                               => __( 'End date', 'woocommerce-events' ),
            'startTime'                             => __( 'Start time', 'woocommerce-events' ),
            'endTime'                               => __( 'End time', 'woocommerce-events' ),
            'duration'                              => __( 'Duration', 'woocommerce-events' ),
            'recurrence'                            => __( 'Recurrence', 'woocommerce-events' ),
            'upcomingOccurrences'                   => __( 'Upcoming occurrences', 'woocommerce-events' ),
            'occurrences'                           => __( 'Occurrences', 'woocommerce-events' ),
            'noOccurrences'                         => __( 'No upcoming occurrences', 'woocommerce-events' ),
            'unableToFetchMeeting'                  => __( 'Unable to fetch meeting details', 'woocommerce-events' ),
            'unableToFetchWebinar'                  => __( 'Unable to fetch webinar details', 'woocommerce-events' ),
            'registrationRequired'                  => __( 'Note: Automatic attendee registration is required.', 'woocommerce-events' ),
            'registrationRequiredForAllOccurrences' => __( 'Note: Automatic attendee registration is required for all occurrences.', 'woocommerce-events' ),
            'automaticRegistration'                 => __( 'Note: Attendees will be registered automatically.', 'woocommerce-events' ),
            'automaticRegistrationAllOccurrences'   => __( 'Note: Attendees will be registered automatically for all occurrences.', 'woocommerce-events' ),
            'meetingRegistrationCurrentlyEnabled'   => __( 'Automatic attendee registration is currently enabled for this meeting', 'woocommerce-events' ),
            'webinarRegistrationCurrentlyEnabled'   => __( 'Automatic attendee registration is currently enabled for this webinar', 'woocommerce-events' ),
            'meetingRegistrationCurrentlyDisabled'  => __( 'Automatic attendee registration is currently disabled for this meeting', 'woocommerce-events' ),
            'webinarRegistrationCurrentlyDisabled'  => __( 'Automatic attendee registration is currently disabled for this webinar', 'woocommerce-events' ),
            'enableMeetingRegistration'             => __( 'Enable automatic attendee registration for this meeting', 'woocommerce-events' ),
            'enableWebinarRegistration'             => __( 'Enable automatic attendee registration for this webinar', 'woocommerce-events' ),
            'registrationAllOccurrencesEnabled'     => __( 'Automatic attendee registration is currently enabled for all occurrences', 'woocommerce-events' ),
            'registrationAllOccurrencesDisabled'    => __( 'Automatic attendee registration is not currently enabled for all occurrences', 'woocommerce-events' ),
            'enableRegistrationForAllOccurrences'   => __( 'Enable automatic attendee registration for all occurrences', 'woocommerce-events' ),
            'registrations'                         => __( 'Registrations', 'woocommerce-events' ),
            'linkMultiMeetingsWebinars'             => __( 'Link the event to these meetings/webinars:', 'woocommerce-events' ),
            'showDetails'                           => __( 'Show details', 'woocommerce-events' ),
            'hideDetails'                           => __( 'Hide details', 'woocommerce-events' ),
            'notRecurringMeeting'                   => __( 'This is not a recurring meeting', 'woocommerce-events' ),
            'notRecurringWebinar'                   => __( 'This is not a recurring webinar', 'woocommerce-events' ),
            'noFixedTimeMeeting'                    => __( "This meeting's recurrence is currently set to 'No Fixed Time' which does not allow attendees to pre-register in advance. Please change the setting for this meeting to have a fixed recurrence (daily/weekly/monthly) in your Zoom account before proceeding.", 'woocommerce-events' ),
            'noFixedTimeWebinar'                    => __( "This webinar's recurrence is currently set to 'No Fixed Time' which does not allow attendees to pre-register in advance. Please change the setting for this webinar to have a fixed recurrence (daily/weekly/monthly) in your Zoom account before proceeding.", 'woocommerce-events' ),
            'editMeeting'                           => __( 'Edit meeting', 'woocommerce-events' ),
            'editWebinar'                           => __( 'Edit webinar', 'woocommerce-events' ),
            'singleEventType'                       => __( 'Single', 'woocommerce-events' ),
            'sequentialEventType'                   => __( 'Sequential days', 'woocommerce-events' ),
            'selectEventType'                       => __( 'Select days', 'woocommerce-events' ),
            'bookingsEventType'                     => __( 'Bookable', 'woocommerce-events' ),
            'seatingEventType'                      => __( 'Seating', 'woocommerce-events' ),
            'singleEventTypeDescription'            => __( 'Standard one-day events.', 'woocommerce-events' ),
            'sequentialEventTypeDescription'        => __( 'Events that occur over multiple days and repeat for a set number of sequential days.', 'woocommerce-events' ),
            'selectEventTypeDescription'            => __( 'Events that repeat over multiple calendar days.', 'woocommerce-events' ),
            'bookingsEventTypeDescription'          => __( 'Events that require customers to select from available date and time slots (bookings and repeat events).', 'woocommerce-events' ),
            'seatingEventTypeDescription'           => __( 'Events that include the ability for customers to select row and seat numbers from a seating chart.', 'woocommerce-events' ),
            'refreshExampleInfo'                    => __( 'Refresh Example Info', 'woocommerce-events' ),
            'hours'                                 => __( 'hours', 'woocommerce-events' ),
            'hour'                                  => __( 'hour', 'woocommerce-events' ),
            'minutes'                               => __( 'minutes', 'woocommerce-events' ),
            'minute'                                => __( 'minute', 'woocommerce-events' ),
            'daily'                                 => __( 'Daily', 'woocommerce-events' ),
            'dateFormat'                            => date_format_php_to_js( get_option( 'date_format' ) ),
        );

        wp_enqueue_script( 'woocommerce-events-zoom-admin-script', $fooconfig->scripts_path . 'events-zoom-admin.js', array( 'jquery' ), $fooconfig->plugin_data['Version'], true );
        wp_localize_script( 'woocommerce-events-zoom-admin-script', 'zoomObj', $zoom_args );
    }

    function foo_styles(){
        $fooconfig = new FooEvents_Config();
        wp_enqueue_style( 'woocommerce-events-admin-script', $fooconfig->styles_path . 'events-admin.css', array(), $fooconfig->plugin_data['Version'] );
        wp_enqueue_style( 'woocommerce-events-admin-timepicker', $fooconfig->styles_path . 'jquery-ui-timepicker-addon.css', array(), '1.2.1' );

        if ( ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) || ( isset( $_GET['page'] ) && 'fooevents-event-report' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_style( 'woocommerce-events-admin-jquery', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), '1.0.0' );
        }

        wp_enqueue_style( 'wp-color-picker' );

        if ( isset( $_GET['page'] ) && 'fooevents-event-report' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_style( 'woocommerce-events-chartist', $fooconfig->styles_path . 'chartist.min.css', array(), '0.11.3' );
            wp_enqueue_style( 'woocommerce-events-chartist-tooltip', $fooconfig->styles_path . 'chartist-plugin-tooltip.css', array(), '0.0.18' );
        }

        if ( isset( $_GET['post_type'] ) && 'event_magic_tickets' === $_GET['post_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification
            wp_enqueue_style( 'woocommerce-events-select', $fooconfig->styles_path . 'select2.min.css', array(), '4.0.12' );
        }

        wp_enqueue_style( 'woocommerce-events-select', $fooconfig->styles_path . 'select2.min.css', array(), '4.0.12' );

        wp_enqueue_style( 'woocommerce-events-zoom-admin-style', $fooconfig->styles_path . 'events-zoom-admin.css', array(), $fooconfig->plugin_data['Version'] );
    }
}

function strip_array_indices( $array_to_strip ) {
    foreach ( $array_to_strip as $item ) {
        $new_array[] = $item;
    }

    return( $new_array );
}
function date_format_php_to_js( $format ) {

    $return_format = $format;
    switch ( $format ) {
        // Predefined WP date formats.
        case 'D d-m-y':
            $return_format = 'D dd-mm-yy';
            break;

        case 'D d-m-Y':
            $return_format = 'D dd-mm-yy';
            break;

        case 'l d-m-Y':
            $return_format = 'DD dd-mm-yy';
            break;

        case 'jS F Y':
            $return_format = 'd MM yy';
            break;

        case 'F j, Y':
            $return_format = 'MM dd, yy';
            break;

        case 'F j Y':
            $return_format = 'MM dd yy';
            break;

        case 'M. j, Y':
            $return_format = 'M. dd, yy';
            break;

        case 'M. d, Y':
            $return_format = 'M. dd, yy';
            break;

        case 'mm/dd/yyyy':
            $return_format = 'mm/dd/yy';
            break;

        case 'j F Y':
            $return_format = 'd MM yy';
            break;

        case 'Y/m/d':
            $return_format = 'yy/mm/dd';
            break;

        case 'm/d/Y':
            $return_format = 'mm/dd/yy';
            break;

        case 'd/m/Y':
            $return_format = 'dd/mm/yy';
            break;

        case 'Y-m-d':
            $return_format = 'yy-mm-dd';
            break;

        case 'm-d-Y':
            $return_format = 'mm-dd-yy';
            break;

        case 'd-m-Y':
            $return_format = 'dd-mm-yy';
            break;

        case 'j. FY':
            $return_format = 'd. MMyy';
            break;

        case 'j. F Y':
            $return_format = 'd. MM yy';
            break;

        case 'j. F, Y':
            $return_format = 'd. MM, yy';
            break;

        case 'j.m.Y':
            $return_format = 'd.mm.yy';
            break;

        case 'j.n.Y':
            $return_format = 'd.m.yy';
            break;

        case 'j. n. Y':
            $return_format = 'd. m. yy';
            break;

        case 'j.n. Y':
            $return_format = 'd.m. yy';
            break;

        case 'j \d\e F \d\e Y':
            $return_format = "d 'de' MM 'de' yy";
            break;

        case 'D j M Y':
            $return_format = 'D d M yy';
            break;

        case 'D F j':
            $return_format = 'D MM d';
            break;

        case 'l j F Y':
            $return_format = 'DD d MM yy';
            break;

        default:
            $return_format = 'yy-mm-dd';
    }

    return $return_format;

}