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
				    Contact Us
				</h2>
				<?php
				/***********  HTML to return if form has not been submitted *************/
				function showform () { ?>
					<p><strong>IT ONLY TAKES A FEW MINUTES TO SUBMIT YOUR INFORMATION TO SEE IF YOUR COMPANY MEETS THE CRITERIA TO BE A FEATURED BUSINESS IN YOUR INDUSTRY (The Recognized Businesses are selected based on different criteria for their industry.  Some criteria that apply are BBB rating; customer reviews; continuing education courses, quality of product line or materials, etc.)</strong></p>
					<div class="form_wrapper" style="margin-bottom: 0px;">
						<form class="validateForm" action="contactus.php" method="post">
							<input type="hidden" name="frmAction" value="true">
							
							<div class="column w50p">
								<div class="m10">
									<label for="firstname">
										<label class="required">*</label> Company Name:
										<input type="text" name="firstname" id="firstname" value="<?= $GLOBALS['firstname']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="lastname">
										<label class="required">*</label> Your Name:
										<input type="text" name="lastname" id="lastname" value="<?= $GLOBALS['lastname']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w50p">
								<div class="m10">
									<label for="dayphone">
										<label class="required">*</label> Day Phone:
										<input type="text" name="dayphone" id="dayphone" value="<?= $GLOBALS['dayphone']; ?>" />
									</label>
								</div>
							</div>
							
							<div class="column w50p">
								<div class="m10">
									<label for="email">
										<label class="required">*</label> Email:
										<input type="text" name="email" id="email" value="<?= $GLOBALS['email']; ?>" />
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="howdidyouhear">
										<label class="required">*</label> What services do you offer?
										<textarea name="howdidyouhear" id="howdidyouhear" rows=3 cols=40 size="50"><?= $GLOBALS['howdidyouhear']; ?></textarea>
									</label>
								</div>
							</div>
							
							<br class="clear" />
							
							<div class="column w100p">
								<div class="m10">
									<label for="contactreason">
										<label class="required">*</label> What makes your company unique?
										<textarea name="contactreason" id="contactreason" rows=3 cols=40 size="50"><?= $GLOBALS['contactreason']; ?></textarea>
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
					$required = array("firstname","lastname","dayphone","howdidyouhear","contactreason","email");
					while (list($key, $val) = each($required)) {
						if (! $GLOBALS[$val]) {
							$missing = $missing . "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$val\n";
						}
					}
	
					if ($missing != '') {
						showformmissing($missing);
						showform();
					} else {
	
						$_POST[firstname] = mysql_escape_string($_POST[firstname]);
						$_POST[lastname] = mysql_escape_string($_POST[lastname]);
						$_POST[howdidyouhear] = mysql_escape_string($_POST[howdidyouhear]);
						$_POST[contactreason] = mysql_escape_string($_POST[contactreason]);
						$_POST[email] = mysql_escape_string($_POST[email]);
	
						# Common Headers
						$eol="\n";
						$headers ='';
						$headers .= 'From: '.$_POST[firstname].' '.$_POST[lastname].' <'.$_POST[email].'>'.$eol;
						$headers .= 'Reply-To: '.$_POST[firstname].' '.$_POST[lastname].' <'.$_POST[email].'>'.$eol;
						$headers .= 'Return-Path: '.$_POST[firstname].' '.$_POST[lastname].' <'.$_POST[email].'>'.$eol;    // these two to set reply address
						$headers .= 'Subject: Business Review Services Site Contact'.$eol;
						$headers .= "Message-ID: <".time()." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
						$headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters
						# Boundry for marking the split & Multitype Headers
						$mime_boundary=md5(time());
						$headers .= 'MIME-Version: 1.0'.$eol;
						$headers .= "Content-Type: text/plain; ".$eol;
						$msg = "";
						$msg .= "---------------------------------------------------".$eol;
						$msg .= "Company Name: $firstname".$eol;
						$msg .= "Name: $lastname".$eol;
						$msg .= "Email: $email".$eol;
						$msg .= "Phone: $dayphone".$eol;
						$msg .= "What services do you offer?: $howdidyouhear".$eol;
						$msg .= "What makes your company unique?: $contactreason".$eol;
						$msg .= "".$eol;
						$msg .= "---------------------------------------------------".$eol;
						
						$to = "info@businessreviewservices.com";
						$from = mysql_escape_string ($_POST[email]);
						if (!mail($to,$from,$msg,$headers)){?>
							<p>The message did not send. Please try again, or call us for directly.</p>
						<?}else{?>
							<p class="copy">Your Company has been submitted. Someone will contact you after reviewing your information. Thank You!</p>
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