<footer class="mastfoot">
	<div class="grid grid--w950">
		<hr>
		<div class="mastfoot__content grid--flex flex--space-between">
			<div class="footer-component">
				<h5>{{ config('app.name') }}</h5>
				<p>&copy; 2017 All Rights Reserved.</p>
			</div>

			<div class="footer-component">
				<ul class="list--inline">
					<li class="list__item"><a href="{{ route('page.support') }}">Support</a></li>
					<li class="list__item"><a href="{{ route('page.contact') }}">Contact</a></li>
				</ul>
			</div>
		</div>		
	</div>	
</footer>