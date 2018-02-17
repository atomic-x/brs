<?php include_once("includes/header.php");
function showformmissing ($string) { ?>
	<p class="subhead">Oops!</p>

	<P CLASS="copy">You left out something important! Please use the back button on your browser to fill in the missing information. Your submission was missing the following information:</P>

	<P CLASS="copy"><?= "$string\n"; ?></P>

	&nbsp;<br/><br/>
<?php } ?>
	<div id="main">
		<div class="twocolumns">
			<div id="content">
				<h2 class="title">
				    Sign Up Now
				</h2>
				<p class="copy">Sign up today to be one of the 17 local businesses in your area.</p>
				<?php
				/***********  HTML to return if form has not been submitted *************/
				function showform () { ?>
					<div class="form_wrapper" style="margin-bottom: 0px;">
						<form class="validateForm" action="signup.php" method="post">
							<input type="hidden" name="frmAction" value="true">
							
							<div class="column w50p">
								<div class="m10">
									<label for="compname">
										<label class="required">*</label> Company Name:
										<input type="text" name="compname" id="compname" value="<?= $GLOBALS['compname']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="address">
										<label class="required">*</label> Address:
										<input type="text" name="address" id="address" value="<?= $GLOBALS['address']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w50p">
								<div class="m10">
									<label for="city">
										<label class="required">*</label> City:
										<input type="text" name="city" id="city" value="<?= $GLOBALS['city']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="state">
										<label class="required">*</label> State:
										<input type="text" name="state" id="state" value="<?= $GLOBALS['state']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
								<div class="column w50p">
								<div class="m10">
									<label for="zip">
										<label class="required">*</label> Zip:
										<input type="text" name="zip" id="zip" value="<?= $GLOBALS['zip']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="phone">
										<label class="required">*</label> Phone:
										<input type="text" name="phone" id="phone" value="<?= $GLOBALS['phone']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
									<div class="column w50p">
								<div class="m10">
									<label for="email">
										<label class="required">*</label> Email:
										<input type="text" name="email" id="email" value="<?= $GLOBALS['email']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="website">
										<label class="required">*</label> Website:
										<input type="text" name="website" id="website" value="<?= $GLOBALS['website']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="briefdescription">
										<label class="required">*</label> Please provide a brief description about your company:
										<textarea name="briefdescription" id="briefdescription" rows=3 cols=40 size="50"><?= $GLOBALS['briefdescription']; ?></textarea>
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="serviceprov">
										<label class="required">*</label> What service or services do you provide?
										<textarea name="serviceprov" id="serviceprov" rows=3 cols=40 size="50"><?= $GLOBALS['serviceprov']; ?></textarea>
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="compdiff">
										<label class="required">*</label> What makes your company different?
										<textarea name="compdiff" id="compdiff" rows=3 cols=40 size="50"><?= $GLOBALS['compdiff']; ?></textarea>
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="whycall">
										<label class="required">*</label> Why should consumers call you over your competition?
										<textarea name="whycall" id="whycall" rows=3 cols=40 size="50"><?= $GLOBALS['whycall']; ?></textarea>
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w50p caligned">
								<div class="m10">
									<label for="submit">
										<input class="submitBtn" type="submit" value="Submit" name="submit" />
									</label>
								</div>
							</div>
							
							<div class="column w50p caligned">
								<div class="m10">
									<label for="reset">
										<input name="reset" type="RESET" value="Reset">
									</label>
								</div>
							</div>
							
							<br class="clear" />
						</form>
					</div>
				<? }
				/***********  MAIN CODE *************/
				
				$MP = "/usr/sbin/sendmail -finfo@businessreviewservices.com";
				
				extract($_POST);
				
				if ($frmAction) {
					$required = array("compname","address","city","state","zip","phone", "email", "website", "briefdescription", "serviceprov", "compdiff", "whycall");
					while (list($key, $val) = each($required)) {
						if (! $GLOBALS[$val]) {
							$missing = $missing . "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$val\n";
						}
					}
	
					if ($missing != '') {
						showformmissing($missing);
						showform();
					} else {
	
						$_POST[compname] = mysql_escape_string($_POST[compname]);
						$_POST[address] = mysql_escape_string($_POST[address]);
						$_POST[city] = mysql_escape_string($_POST[city]);
						$_POST[state] = mysql_escape_string($_POST[state]);
						$_POST[zip] = mysql_escape_string($_POST[zip]);
						$_POST[phone] = mysql_escape_string($_POST[phone]);
						$_POST[email] = mysql_escape_string($_POST[email]);
						$_POST[website] = mysql_escape_string($_POST[website]);
						$_POST[briefdescription] = mysql_escape_string($_POST[briefdescription]);
						$_POST[serviceprov] = mysql_escape_string($_POST[serviceprov]);
						$_POST[briefdescription] = mysql_escape_string($_POST[compdiff]);
						$_POST[serviceprov] = mysql_escape_string($_POST[whycall]);
	
	
						# Common Headers
						$eol="\n";
						$headers ='';
						$headers .= 'From: '.$_POST[compname].' <'.$_POST[email].'>'.$eol;
						$headers .= 'Reply-To: '.$_POST[compname].' <'.$_POST[email].'>'.$eol;
						$headers .= 'Return-Path: '.$_POST[compname].' <'.$_POST[email].'>'.$eol;    // these two to set reply address
						$headers .= 'Subject: Business Review Services Site Contact'.$eol;
						$headers .= "Message-ID: <".time()." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
						$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
						# Boundry for marking the split & Multitype Headers
						$mime_boundary=md5(time());
						$headers .= 'MIME-Version: 1.0'.$eol;
						$headers .= "Content-Type: text/plain; ".$eol;
						$msg = "";
						$msg .= "---------------------------------------------------".$eol;
						$msg .= "Company Name: $compname".$eol;
						$msg .= "Address: $address".$eol;
						$msg .= "City: $city".$eol;
						$msg .= "State: $state".$eol;
						$msg .= "Zip: $zip".$eol;
						$msg .= "Phone: $phone".$eol;
						$msg .= "Email: $email".$eol;
						$msg .= "Website: $website".$eol;
						$msg .= "Brief description of business: $briefdescription".$eol;
						$msg .= "Services provided: $serviceprov".$eol;
						$msg .= "What makes your company different: $compdiff".$eol;
						$msg .= "Why sould consumers call your business: $whycall".$eol;
						$msg .= "".$eol;
						$msg .= "---------------------------------------------------".$eol;
						
						$to = "info@businessreviewservices.com";
						$from = mysql_escape_string ($_POST[email]);
						if (!mail($to,$from,$msg,$headers)){?>
							<p>The message did not send. Please try again, or call us for directly.</p>
						<?}else{?>
							<p class="copy">Thank you for contacting Business Review Services!</p>
							
							<p class="copy">If needed a representative will contact you as soon as possible. Keep in mind that there may be a 24-48 hour response time if you have attempted to contact on weekends or holidays.</p>
							
							<p class="copy">If you would like to speak to us via phone please call (513)887-5344</p>
						<?}?>
					<?}//end else
				}else{
					showform();
				}?>
			</div>
			<div id="sidebar">
				<div class="sidebar-holder">
					<div class="sidebar-frame">
						
					</div>
				</div>
			</div>
		</div>
	</div>
<? include_once("includes/footer.php"); ?>