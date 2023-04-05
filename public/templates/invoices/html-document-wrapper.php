<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo esc_html( $document->get_title() ); ?></title>
	<style type="text/css"><?php $document->template_styles(); ?></style>
	<style type="text/css"><?php do_action( 'wpo_wcpdf_custom_styles', $document->get_type(), $document ); ?></style>
</head>
<body class="<?php echo apply_filters( 'wpo_wcpdf_body_class', $document->get_type(), $document ); ?>">
<?php echo $output_body; ?>
</body>
</html>