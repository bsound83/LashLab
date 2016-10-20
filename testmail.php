<?php
    if( $_POST || $_FILES )
    {
            // email fields: to, from, subject, and so on
            // Here 
            $from = "noreply@askewent.com";
            $to = "sraskew@askewent.com";
            
            /* Check all form inputs using check_input function */
            $name = check_input($_POST['inputName'], "Your Name");
            $phone = check_input($_POST['inputPhone'], "Your Phone Number");
            $email = check_input($_POST['inputEmail'], "Your E-mail Address");
            $age = check_input($_POST['inputAge'], "Your Age");
            $church = check_input($_POST['inputChurch'], "Your Church Affiliation");
            $resident = check_input($_POST['inputResident'], "Your Residence");
            $part = check_input($_POST['inputPart'], "Question # 1");
            $testimony = check_input($_POST['inputTestimony'], "Question # 2");
            $gain = check_input($_POST['inputGain'], "Question # 3");
            $personality = check_input($_POST['inputPersonality'], "Question # 4");
            $faith = check_input($_POST['inputFaith'], "Question # 5");

            /* If e-mail is not valid show error message */
            if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
            {
            show_error("Invalid e-mail address");
            }
            /* Let's prepare the message for the e-mail */
            $subject = "Casting Call Request";
            $message = "

            A new casting call has been submitted:

            Name: $name
            Phone: $phone    
            Email: $email
            Age: $age
            Church Affiliation: $church
            Dallas Resident: $resident
            
            Question #1: Why do you want to be part of this reality show?
            $part
            
            Question #2: What is your testimony and do you feel like it will be something that viewers will find relatable?
            $testimony
                
            Question #3: What do you hope to accomplish/gain by being a part of this series?
            $gain
                
            Question #4: Provide 3 words that describe your personality
            $personality
                
            Question #5: Are you hesitant to share your faith journey in this reality series? Why?
            $faith
            

            ";
            $headers = "From: $from";

            // boundary
            $semi_rand = md5(time());
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

            // headers for attachment
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

            // multipart boundary
            $message = "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n"."Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
            fixFilesArray($_FILES['attachment']);
            foreach ($_FILES['attachment'] as $position => $file) 
            {
                    // should output array with indices name, type, tmp_name, error, size
                    $message .= "--{$mime_boundary}\n";
                    $fp     = @fopen($file['tmp_name'],"rb");
                    $data   = @fread($fp,filesize($file['tmp_name']));
                    @fclose($fp);
                $data = chunk_split(base64_encode($data));
                $message .= "Content-Type: application/octet-stream; name=\"".$file['name']."\"\n"."Content-Description: ".$file['name']."\n" ."Content-Disposition: attachment;\n" . " filename=\"".$file['name']."\";size=".$file['size'].";\n"."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
            }
            $message .= "--{$mime_boundary}--";
            //$returnpath = "-f" . $from;
            $ok = @mail($to, $subject, $message, $headers); //, $returnpath);
            
            $successresponse = "<h4 style='width:500px;margin:100px auto;color:#fff;font-family:Georgia,Segoe,Tahoma,Helvetica,Arial,sans-serif;'>Thank you for your submission, selected applicants will be contacted via email or phone.</h4>";
            echo $ok ? $successresponse : "Mail failed";
            if($ok){ return 1; } else { return 0; }
    }
    //This function will correct file array from $_FILES[[file][position]] to $_FILES[[position][file]] .. Very important

    function fixFilesArray(&$files)
    {
            $names = array( 'name' => 1, 'type' => 1, 'tmp_name' => 1, 'error' => 1, 'size' => 1);

            foreach ($files as $key => $part) {
                    // only deal with valid keys and multiple files
                    $key = (string) $key;
                    if (isset($names[$key]) && is_array($part)) {
                            foreach ($part as $position => $value) {
                                    $files[$position][$key] = $value;
                            }
                            // remove old key reference
                            unset($files[$key]);
                    }
            }
    }
    
    /* Functions we used */
    function check_input($data, $problem='')
    {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($problem && strlen($data) == 0)
    {
    show_error($problem);
    }
    return $data;
    }

    function show_error($myError)
    {
    ?>
    <html>
    <body>

    <p>Please correct the following error:</p>
    <strong><?php echo $myError; ?></strong>
    <p>Hit the back button and try again</p>

    </body>
    </html>
    <?php
    exit();
    }
    ?>
