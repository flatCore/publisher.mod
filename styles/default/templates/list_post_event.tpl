<div class="row">
	<div class="col-md-4">
		<div class="teaser-image">
			<img src="{post_img_src}" class="img-thumbnail">
		</div>
	</div>
	<div class="col-md-8">
		<div class="event-date">
			<div class="event-date-header">
				<span class="event-start-day">{event_start_day}.</span>
				<span class="event-start-month">{event_start_month_text}</span>
			</div>
			<span class="event-start-year">{event_start_year}</span>
			<div class="event-date-footer">
				<span class="event-end-date">{event_end_day}.{event_end_month}.{event_end_year}</span>
			</div>
		</div>
		<span>{post_releasedate} </span>
		<a class="post-headline-link" href="{post_href}"><h3>{post_title}</h3></a>
		{post_teaser}
	</div>
</div>
<div class="row">
	<div class="col-md-8 offset-md-4">
		<p class="text-right">{post_cats}</p>
		<p><a class="btn btn-primary btn-sm {read_more_class}" href="{post_href}">{read_more_text}</a></p>
	</div>
</div>
<hr>