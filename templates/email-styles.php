<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates/Emails
 * @version 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Load colors
$bg              = iwj_option( 'email_background_color', '#f7f7f7' );
$body            = iwj_option( 'email_body_background_color', '#ffffff' );
$base            = iwj_option( 'email_base_color' , '#96588a');
$base_text       = iwj_light_or_dark( $base, '#202020', '#ffffff' );
$text            = iwj_option( 'email_text_color', '#3c3c3c' );
$body_text       = iwj_option( 'email_body_text_color', '#3c3c3c' );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
?>
#wrapper {
	background-color: <?php echo esc_attr( $bg ); ?>;
	margin: 0;
	padding: 70px 0 70px 0;
	-webkit-text-size-adjust: none !important;
	width: 100%;
}
#template_container {
	width: 600px;
    margin: 0 auto;
    color: <?php echo esc_attr( $text ); ?>;
}

#template_header td{
	background-color: <?php echo esc_attr( $base ); ?>;
	border-radius: 5px 5px 0 0 !important;
	color: <?php echo esc_attr( $base_text ); ?>;
	border-bottom: 0;
	font-weight: bold;
	line-height: 100%;
	vertical-align: middle;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    padding: 25px 45px;
}

#template_header h1,
#template_header h1 a {
	color: <?php echo esc_attr( $base_text ); ?>;
}

#template_footer{
    text-align: center;
    padding-top: 40px;
}

#body_content {
	background-color: <?php echo esc_attr( $body ); ?>;
    color: <?php echo esc_attr( $body_text ); ?>;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 14px;
    line-height: 150%;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    padding: 30px 45px 25px;
    border-radius: 0 0 5px 5px;
}

#body_content table td {
	padding: 48px;
}

#body_content table td td {
	padding: 12px;
}

#body_content table td th {
	padding: 12px;
}

#body_content p {
	margin: 0 0 16px;
}
#body_content a{
    color: <?php echo esc_attr( $base ); ?>;
}

h1 {
	color: <?php echo esc_attr( $base ); ?>;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 30px;
	font-weight: 300;
	line-height: 150%;
	margin: 0;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h2 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 18px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

h3 {
	color: <?php echo esc_attr( $base ); ?>;
	display: block;
	font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
	font-size: 16px;
	font-weight: bold;
	line-height: 130%;
	margin: 16px 0 8px;
	text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
}

a {
	color: <?php echo esc_attr( $base ); ?>;
	font-weight: normal;
	text-decoration: underline;
}

img {
	border: none;
	display: inline;
	font-size: 14px;
	font-weight: bold;
	height: auto;
	line-height: 100%;
	outline: none;
	text-decoration: none;
	text-transform: capitalize;
}
.job-list-email .job-item {
    border-bottom: 1px solid #f6f7f9;
    display: table;
    padding: 40px 0;
    width: 100%;
}
.job-list-email .job-item:last-child {
    border-bottom: none;
}
.jobs-email-title {
    border-bottom: 2px solid #eeeeee;
    font-size: 18px;
    margin: 0 0 40px;
    padding-bottom: 10px;
    color: #333333;
}
.job-list-email .image {
    border-radius: 5px;
    line-height: 1;
    margin: 0 30px 0 0;
    overflow: hidden;
    position: relative;
    text-align: center;
}
.job-list-email .image img {
    max-height: 90px;
}
.job-list-email .info-wrap {
    float: left;
    padding-left: 5px;
    position: relative;
    width: calc(100% - 76px);
}
.job-list-email .info-wrap h3 {
    color: #333333 !important;
}
.job-list-email .job-meta {
    padding: 0;
}
.job-list-email .job-meta li {
    list-style: none;
    margin-bottom: 5px;
}
.job-list-email .job-meta li a {
    text-decoration: none;
}

.order_details{
    list-style: none;
    padding: 0;
    margin: 0;
}
.order_details li{
    padding: 5px 0;
    margin: 0;
}

<?php
echo iwj_option('email_styles');

do_action('iwj_email_style');