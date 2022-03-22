# Progressbar

## Polylang

If the progressbar is configured to show the number of form submissions and polylang is used, the progressbar will show
the total of all submissions in any language (if the form has any translations).

The offset however, is only taken from the page, where the progressbar is integrated. The offset of the other languages
is not added.

## Custom Count Function

Use the `theme_form-submission-count` filter to modify the count of the progressbar's submission. The filter receives
the total count, that would be displayed (inluding any offset), the form_id and the offset. It must return an integer.

Example to display only the submission count of the last 365 days for the form with the id 1234:

```php
add_filter( 'theme_form-submission-count', function ( int $count, int $form_id, int $offset ) {
	if ( 1234 !== $form_id ) {
		return $count;
	}

	require_once get_parent_theme_file_path( '/lib/form/include/FormModel.php' );

	$form  = new FormModel( $form_id );
	$now   = new \DateTime();
	$count = 0;

	foreach ( $form->get_submissions() as $submission ) {
		$date = date_create_from_format( 'Y-m-d H:i:s', $submission->meta_get_timestamp() );
		$diff = $now->diff( $date );
		if ( $diff->days < 365 ) {
			$count ++;
		}
	}

	return $count + $offset;
}, 10, 3 );
```
