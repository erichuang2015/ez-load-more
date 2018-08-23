# Ez Load More 

Easily add an AJAX load more button to a WordPress query. Clicking this button will automatically fetch the next page.

Usage
-----

The Ez Load More plugin allows you to add a load more button to your template. Using AJAX and the default WordPress query paging the next page will be fetched and displayed.

All you have to do is include the following code in your template:

`ez_load_more_button($args);`

If you use [Timber](https://wordpress.org/plugins/timber-library/) (and i hope you do), you can use the following in your Twig template:

`{{ function('ez_load_more_button', args) }}`

Arguments 
-----

name |  required | description
------------ | ------------- | -------------
template | yes | The template for the post teasers, in twig (via Timber) or regular WordPress php templates. 
label | yes | The label used in the load more button
context | yes | Will be used to set nonces for this specific AJAX call
button_class | no | The class set on the load more button
custom_loader | no | If adjusting the color and width is not enough, you can overwrite the default loader by setting this parameter to a div classname. Make sure your html with this div is located in a `ajax-loader.php` template in your active theme). Make sure the div class is hidden by default (`display:none`), it will be displayed by javascript. (Tip: [Pure CSS Loaders](https://loading.io/css/))


Examples
-----

Make sure to include the `ez_load_more_button` function outside of your loop.

### Timber/Twig
```
{% block content %}

  {% if posts %}
    {% for post in posts %}
      {% include 'partials/teaser.twig' %}
    {% endfor %}`
  {% else %}
    <p>{{ t('No posts available') }}.</p>
  {% endif %}

  {% set args = {
    'template': 'partials/teaser',
    'label': 'Load more'
    'context': 'archive'
  } %}
  {{ function('ez_load_more_button', args) }}

{% endblock %}
```

### WordPress PHP templates:
```
if (have_posts()) {
	while (have_posts()) : the_post();
		get_template_part('content', 'teaser');
	endwhile; // end of the loop.
	
	$args = [
		// Required
		'template' => 'teaser',
		'label' => 'Load more',
		'context' => 'archive',
		// Not required
		'button_class' => 'my-custom-button-class',
		'custom_loader' => 'lds-ripple',
	];
	ez_load_more_button($args);
}
```
