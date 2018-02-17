<?php include_once("includes/header.php"); 
$msg = '';
if($_POST) {
	if (strlen($_FILES['resume']['name'])) {
		if($_FILES['resume']['error'] > 0){
			$error_array = array(
				0 => "There is no error, the file uploaded with success",
				1 => "The uploaded file exceeds the size limit",
				2 => "The uploaded file exceeds the size limit",
				3 => "The uploaded file was only partially uploaded",
				4 => "No file was uploaded",
				6 => "Missing a temporary folder"
			);
			$msg = "There was an error uploading the your resume.  Error: ".$error_array[$_FILES['resume']['error']];
			
		}
		
		$file_types = array(
			'pdf' => 'application/pdf',
		);
		
		$finfo = new finfo();//_open(FILEINFO_MIME_TYPE);
		
		if (false === $ext = array_search($finfo->file($_FILES['resume']['tmp_name'], FILEINFO_MIME_TYPE), $file_types, true)) {
			//throw new RuntimeException('Invalid file format.');
			$msg = "The file type you attempted to send is not allowed, please make sure you are sending PDF file.";
		} else {		
			$file_path = "resume/";
			$file_txt = $_POST['firstname']."_".$_POST['lastname'];
			$file_name = $file_txt.'_'.sha1(date("Y-m-d H:i:s")).".".$ext;
			$file = $file_path . $file_name;
			if (move_uploaded_file($_FILES['resume']['tmp_name'], $file_path . $file_name)) {					
				// Success
				#################
				$file_size = filesize($file);
				$handle = fopen($file, "r");
				$content = fread($handle, $file_size);
				fclose($handle);
				$content = chunk_split(base64_encode($content));                    
				
				$subject = 'Business Review | Careers | Resume';
				
				// a random hash will be necessary to send mixed content
				$separator = md5(time());
				
				// carriage return type (we use a PHP end of line constant)
				$eol = PHP_EOL;
				
				$message = "Hi Michael<br><br>";
				$message .= "Please check the info below along with the attached pdf file,".$eol."".$eol."<br><br>";
				$message .= "Name: ".$_POST['firstname']." ".$_POST['lastname'].$eol."".$eol."<br>";
				$message .= "Phone: ".$_POST['dayphone'].$eol."".$eol."<br>";
				$message .= "Email: ".$_POST['email'].$eol."".$eol."<br><br>";
				$message .= "<small style='color:#cacaca;'>All content Copyright ©2016 Business Review Services, Inc.".$eol."".$eol."</small>";
				
				// main header (multipart mandatory)
				$headers = "From: career@businessreviewservices.com". $eol;
				$headers .= "Reply-To: career@businessreviewservices.com". $eol;
				//$headers .= "CC: moises.developer@gmail.com". $eol;
				//$headers .= "BCC: rkeast@gmail.com". $eol;
				$headers .= "MIME-Version: 1.0" . $eol;
				$headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
				
				// message
				$body .= "--" . $separator . $eol;
				$body .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $eol;
				$body .= "Content-Transfer-Encoding: 8bit" . $eol;
				$body .= $message . $eol;
				
				// attachment
				$body .= "--" . $separator . $eol;
				$body .= "Content-Type: application/octet-stream; name=\"" . $file_name . "\"" . $eol;
				$body .= "Content-Transfer-Encoding: base64" . $eol;
				$body .= "Content-Disposition: attachment" . $eol;
				$body .= "X-Attachment-Id: ".rand(1000,99999)."\r\n\r\n";
				$body .= $content . $eol;
				$body .= "--" . $separator . "--";
				
				$emailto = 'careers@businessreviewservices.com'; // 'moises.developer@gmail.com';  
				if(mail($emailto, $subject, $body, $headers)) {
					$msg = "Your resume has been sent successfully!";
				} 
				#################
			}else{
				$msg = "Error attaching your resume, please try again later.";
			}
		}
	}
}
?>
	<div id="main">
		<div class="twocolumns">
			<div id="content">
				
				<br/><br/>
				
				<img src="images/careers-banner.png" height="133" width="633" border="0" alt="careers image" />
				
				<h2 class="title">RESUME/APPLICATION SUBMISSION</h2>
				<?php if($msg) {
					echo '<h3 style="padding:10px;background:#c4df9b; color:#333; font-weight:normal">'.$msg.'</h3>';	
				}?>
				<div class="form_wrapper" style="margin-bottom: 0px;">
					<form class="validateForm" action="careers-apply.php" method="post" enctype="multipart/form-data">
						<input type="hidden" name="frmAction" value="true">
						
						<div class="column w50p">
							<div class="m10">
								<label for="firstname">
									<label class="required">*</label> First Name:
									<input type="text" name="firstname" id="firstname" value="<?= $GLOBALS['firstname']; ?>" required="yes" />
								</label>
							</div>
						</div>
						
						<div class="column w50p">
							<div class="m10">
								<label for="lastname">
									<label class="required">*</label> Last Name:
									<input type="text" name="lastname" id="lastname" value="<?= $GLOBALS['lastname']; ?>" required="yes" />
								</label>
							</div>
						</div>
						
						<br class="clear" />
						
						<div class="column w50p">
							<div class="m10">
								<label for="dayphone">
									<label class="required">*</label> Day Phone:
									<input type="text" name="dayphone" id="dayphone" value="<?= $GLOBALS['dayphone']; ?>" required="yes" />
								</label>
							</div>
						</div>
						
						<div class="column w50p">
							<div class="m10">
								<label for="email">
									<label class="required">*</label> Email:
									<input type="text" name="email" id="email" value="<?= $GLOBALS['email']; ?>" required="yes" />
								</label>
							</div>
						</div>
                        
                        <div class="column w100p">
							<div class="m10">
								<label for="email">
									<label class="required">*</label> Upload Resume (.pdf only):
									<input type="file" name="resume" id="resume" value=""  required="yes"/>
								</label>
							</div>
						</div>
						
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

			</div>
			<div id="sidebar">
				<div class="sidebar-holder">
					<div class="sidebar-frame">
						<img src="images/careers-03.jpg" width="220" height="440" border="0" title="Business Review Services Careers" />
					</div>
				</div>
			</div>
		</div>
	</div>
<? include_once("includes/footer.php"); ?>