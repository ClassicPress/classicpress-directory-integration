The PHP Error Log Viewer plugin for ClassicPress brings your error log straight into your dashboard. Color-coding helps you to quickly scan even the longest of error logs. Or, just filter out the errors you don't want to see. No more wall-of-text error messages – this plugin turns your PHP error log into an incredibly useful display.

![PHP Error Log Viewer plugin for ClassicPress by Code Potent](https://codepotent.com/wp-content/uploads/2021/03/codepotent-php-error-log-viewer-plugin-for-classicpress-image-05.png)

## Fast, Lightweight, and User-Friendly

There are lots of debugging plugins out there – _debugging suites_, really. This plugin isn't intended to become one of them. The PHP Error Log Viewer plugin handles a very specific task and that is to display the PHP error log in a user-friendly manner than can be easily filtered, styled, sorted, preserved, or purged.

## More Debugging, Less Clicking

If you have grown tired of flipping back and forth between screens/browsers/apps/whatever to check and recheck your PHP error log as you're writing code, this will be incredibly handy for you. There's a link to the error log within reach at all times and it doesn't require any special configuration.


### Viewing the Error Log
Click the `PHP Errors` menu item in your admin bar. Hover the menu item momentarily and it will reveal your current PHP version. Alternatively, you can access the error log by navigating to `Dashboard > Tools > PHP Error Log`.

![PHP Error Log Viewer plugin for ClassicPress](https://codepotent.com/wp-content/uploads/2020/02/codepotent-php-error-log-viewer-plugin-for-classicpress-screenshot-02.png)

### Filtering the Error Log
The checkboxes across the top of the display allow you to show and hide each of the various types of errors: `Deprecated`, `Notice`, `Warning`, `Error`, and `Other`. There are also checkboxes to show and hide the time/date, stack traces, and to sort the error log in reverse. Tick your preferred boxes and click the `Apply Filters` button to update the display.

![PHP Error Log Viewer plugin for ClassicPress](https://codepotent.com/wp-content/uploads/2020/02/codepotent-php-error-log-viewer-plugin-for-classicpress-screenshot-03.png)

### Refreshing the Error Log
When viewing the error log, you will find a button to `Refresh Error Log` at the right side of the page. Clicking this button has the same effect as clicking your browser's refresh button. The error log will be re-read and displayed fresh.

![PHP Error Log Viewer plugin for ClassicPress](https://codepotent.com/wp-content/uploads/2019/08/codepotent-php-error-log-viewer-plugin-for-classicpress-image-02.png)

### Purging the Error Log
When viewing the error log, you will find a button to `Purge Error Log` at the right side of the page. Clicking this button will purge all messages from the error log. A confirmation dialog prevents accidental deletion. If your error log is not writable by the PHP process, you will not see this button.

![PHP Error Log Viewer plugin for ClassicPress](https://codepotent.com/wp-content/uploads/2019/08/codepotent-php-error-log-viewer-plugin-for-classicpress-image-02.png)

### Purging the Error Log via AJAX
In the admin bar, you will find a link `PHP Errors` which, when hovered, will expose a link to `Purge Error Log`. Clicking this button will purge all messages from the error log without redirecting you away from the current page. A confirmation dialog prevents accidental deletion. If your error log is not writable by the PHP process, you will not see this link.

![PHP Error Log Viewer plugin for ClassicPress](https://codepotent.com/wp-content/uploads/2021/03/codepotent-php-error-log-viewer-plugin-for-classicpress-image-04.png)

### Manually Triggering Errors
As of version 2.2.0, there is a function that allows you to manually trigger user-level notices, warnings, or errors and have them neatly displayed in the error log. Here is an example of creating your own wrapper function for added convenience.

```
/**
     * Creating your own error logging wrapper function
     *
     * This example shows how you might integrate the logging function into your own
     * utility plugin.
     *
     * @param mixed $data   Pass in a string, integer, array, object, etc.
     * @param str $level    Must be notice, warning, or error.
     * @param int $file     Use __FILE__ constant to include filename.
     * @param bool $line    Use __LINE__ constant to include line number.
     */
    function log_data($data, $level='notice', $file=false, $line=false) {

    	// If error log plugin is active and the needed function exists...
    	if (function_exists('codepotent_php_error_log_viewer_log')) {
    		return codepotent_php_error_log_viewer_log($data, $level, $file, $line);
    	}

    	// Or, if error log plugin is inactive, you can include just the needed function...
    	if (file_exists($file = plugin_dir_path(__DIR__).'codepotent-php-error-log-viewer/includes/functions.php')) {
    		require_once($file);
    		return codepotent_php_error_log_viewer_log($data, $level, $file, $line);
    	}

    	// If the error log plugin just doesn't exist, there's a fallback.
    	trigger_error(print_r($data, true), E_USER_WARNING);

    }

    // Elsewhere, send data to the log like this:
    $data = 'whatever type of data';
    log_data($data, 'notice', __FILE__, __LINE__);
```
---

### Display Options
The checkboxes at the top of the error log display allow you to choose which types of error messages you want to see. Check any of the boxes and click the `Apply Filter` button to update the display.
* **Date/Time**
  Check this box to show dates, times, and other meta data.
* **Notice**
  Check this box to show non-critical PHP notices.
* **Warning**
  Check this box to show non-critical PHP warnings.
* **Error**
  Check this box to show critical PHP errors.
* **Other**
  Check this box to show any other errors that didn't meet the above criteria.
* **Show Stack Traces**
  Check this box to show stack traces for critical errors. Note that not all critical errors will generate a stack trace.
* **Reverse Sort**
  Check this box to display the error log with latest errors at the top.

---

### Primary Alert Bubble This filter allows you to hide or redesign the primary (red) alert bubble in the admin bar. This filter accepts a single argument, the markup of the primary alert bubble.

<pre>function yourprefix_hide_primary_alert($alert) {
    return '';
}
add_filter('codepotent_php_errror_log_viewer_primary_alert', 'yourprefix_hide_primary_alert');
</pre>

--- ### Secondary Alert Bubble This filter will allow you to hide or redesign the secondary (gray) alert bubble in the admin bar. This filter accepts a single argument, the markup of the secondary alert bubble.

<pre>function yourprefix_hide_secondary_alert($alert) {
    return '';
}
add_filter('codepotent_php_errror_log_viewer_primary_alert', 'yourprefix_hide_secondary_alert');
</pre>

--- 

### Add Content Before Legend
In cases where you need to insert some contextual information, either of the following filters can be used to place the content before or after the legend. These filters receive an empty string as an argument.

<pre>function yourprefix_before_error_log_legend($markup) {
    $markup = '<p>This content appears before the legend.</p>';
    return $markup;
}
add_filter('codepotent_php_errror_log_viewer_before_legend', 'yourprefix_before_error_log_legend');
</pre>

--- 
### Add Content After Legend
Identical to the filter above, except this filter places your contextual content below the legend.

<pre>function yourprefix_after_error_log_legend($markup) {
    $markup = '<p>This content appears after the legend.</p>';
    return $markup;
}
add_filter('codepotent_php_errror_log_viewer_after_legend', 'yourprefix_after_error_log_legend');
</pre>

---

### Using Custom Error Colors

To override the color-coding for the error messages and legend, copy the following styles into your theme's <code>style.css</code> file and make your changes there.

<pre>
/* Deprecated code. */
#codepotent-php-error-log-viewer .php-deprecated, 
.codepotent-php-error-log-viewer-legend-box.item-php-deprecated {
	border-left:10px solid #847545;
	}
/* Notices. */
#codepotent-php-error-log-viewer .php-notice,
.codepotent-php-error-log-viewer-legend-box.item-php-notice {
	border-left:10px solid #ccc;
	}
/* Warnings. */
#codepotent-php-error-log-viewer .php-warning,
.codepotent-php-error-log-viewer-legend-box.item-php-warning {
	border-left:10px solid #ffee58;
	}
/* Errors. */
#codepotent-php-error-log-viewer .php-error,
.codepotent-php-error-log-viewer-legend-box.item-php-error {
	border-left:10px solid #e53935;
	}
/* Stack traces. */
#codepotent-php-error-log-viewer .php-stack-trace-title,
#codepotent-php-error-log-viewer .php-stack-trace-step,
#codepotent-php-error-log-viewer .php-stack-trace-origin,
.codepotent-php-error-log-viewer-legend-box.item-php-stack-trace-title {
	border-left:10px solid #ef9a9a;
	}
/* Any other messages. */
#codepotent-php-error-log-viewer .php-other,
.codepotent-php-error-log-viewer-legend-box.item-php-other {
	border-left:10px solid #00bcd4;
	}

</pre>