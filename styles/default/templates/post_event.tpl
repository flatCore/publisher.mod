<div class="post-type-{post_type}">
	<h1>{post_title}</h1>
	<p>{post_author} am {post_releasedate}</p>
	<p><img src="{post_img_src}" class="img-fluid"></p>
	<div class="event-date">
		<div class="event-date-header">
			<span class="event-start-day">{event_start_day}</span>
			<span class="event-start-month">{event_start_month_text}</span>
		</div>
		<span class="event-start-year">{event_start_year}</span>
		<div class="event-date-footer">
			<span class="event-end-date">{event_end_day}.{event_end_month}.{event_end_year}</span>
		</div>
	</div>
	{post_teaser}
	
	{post_text}
	
	
	<div class="post-footer">
		<p class="text-right">{post_cats}</p>
		<hr>
		<p><a class="btn btn-link" href="{back_link}">{back_to_overview}</a></p>
	</div>
</div>