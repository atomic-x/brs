	<div id="footer">
		<div class="footer-holder">
			<div class="footer-frame">
				<div class="footer-info">
					<strong class="logo"><a href="/">Business Review Services, INC</a></strong>
					<div class="textbox">
						<address>
						632 Nilles Rd. Fairfield, OH 45014<br />
						Phone: (513) 887-5344 &bull; 1-800-669-3736
						</address>
						<em class="slogan">Supporting Local Businesses... Strengthening Our Community.</em>
					</div>
				</div>
				<div class="block">
					<ul class="nav">
						<li><a href="/aboutus.php">About Us</a></li>
						<li><a href="/publication.php">Publication</a></li>
						<li><a href="/services/">Featured Businesses</a></li>
						<li><a href="/pricing.php">Pricing</a></li>
						<li><a href="/mission.php">Our Mission</a></li>
						<li><a href="/privacy.php">Privacy Policy</a></li>
						<li><a href="/contactus.php">Contact Us</a></li>
						<li><a href="/testimonials.php">Testimonials</a></li>
					</ul>
					<a target="_blank" class="facebook" href="https://www.facebook.com/BusinessReviewServices">
						<span>find us</span><br />
						on facebook
					</a>
					<strong class="copyright">All content Copyright &copy;<?= date("Y"); ?> Business Review Services, Inc.</strong>
				</div>
			</div>
		</div>
	</div>
    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("header").style.opacity = "0.1";
            document.getElementById("main").style.opacity = "0.1";
            document.getElementById("footer").style.opacity = "0.1";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("header").style.opacity = "1";
            document.getElementById("main").style.opacity = "1";
            document.getElementById("footer").style.opacity = "1";
        }
    </script>
</body>
</html>