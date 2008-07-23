<?
#  Description: Simple class using php mail function to construct and send mime multipart
#                emails (i.e. emails with attachments) and support content-id style
#                embedded images in html messages
#
#  Limitations: Uses the ubiquitously supported 7bit (i.e. no encoding) message encoding where as
#                qouted-printable is recommended for html messages. Does not ensure that message
#                line lengths do not exceed the 998 character limit specified by RFC 2822.
#
#  Usage Example:
#    $mulmail = new multipartmail("krisd@work.net", "destination@anywhere.com", "Some Subject");
#    $cid = $mulmail->addattachment("/var/www/html/img/pic.jpg", "image/jpg");
#    $mulmail->addmessage(
#      "<html>\n" .
#      "  <head>\n" .
#      "  </head>\n" .
#      "  <body>\n" .
#      "  This is text before<img src=\"cid:$cid\"> and after\n" .
#      "  </body>\n" .
#      "</html>\n", "text/html");
#    $mulmail->sendmail();

   class multipartmail{
     var $header;
     var $parts;
     var $message;
     var $subject;
     var $to_address;
     var $boundary;

     function multipartmail($dest, $src, $sub){
         $this->to_address = $dest;
         $this->subject = $sub;
         $this->parts = array("");
         $this->boundary = "------------" . md5(uniqid(time()));
         $this->header = "From: $src\n" .
                         "MIME-Version: 1.0\n" .
                         "Content-Type: multipart/related;\n" .
                         " boundary=\"" . $this->boundary . "\"\n" .
                         "X-Mailer: PHP/" . phpversion();
     }

     function addmessage($msg = "", $ctype = "text/plain"){
         $this->parts[0] = "Content-Type: $ctype; charset=UTF-8\n" .
                           "Content-Transfer-Encoding: 7bit\n" .
                           "\n".
                           chunk_split($msg, 65000, "\n");
     }

     function addattachment($file, $ctype){
         $fname = substr(strrchr($file, "/"), 1);
         $data = file_get_contents($file);
         $i = count($this->parts);
         $content_id = "part$i." . sprintf("%09d", crc32($fname)) . strrchr($this->to_address, "@");
         $this->parts[$i] = "Content-Type: $ctype; name=\"$fname\"\n" .
                           "Content-Transfer-Encoding: base64\n" .
                           "Content-ID: <$content_id>\n" .
                           "Content-Disposition: inline;\n" .
                           " filename=\"$fname\"\n" .
                           "\n" .
                           chunk_split( base64_encode($data), 65000, "\n");
         return $content_id;
     }

     function buildmessage(){
         $this->message = "This is a multipart message in mime format.\n";
         $cnt = count($this->parts);
         for($i=0; $i<$cnt; $i++){
           $this->message .= "--" . $this->boundary . "\n" .
                             $this->parts[$i];
         }
     }

     /* to get the message body as a string */
     function getmessage(){
         $this->buildmessage();
         return $this->message;
     }

     function sendmail(){
         $this->buildmessage();
         mail($this->to_address, $this->subject, $this->message, $this->header);
     }
   }

?>
 
