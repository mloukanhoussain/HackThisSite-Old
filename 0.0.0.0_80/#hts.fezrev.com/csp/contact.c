// ============================================================================
// This is a Servlet sample for the G-WAN Web Server (http://www.trustleap.com)
// ----------------------------------------------------------------------------
// contact.c: Build dynamic HTML pages to process a 'Contact Form'
//
//            GET and POST forms are processed with the SAME code (the server
//            does the URL/Entity parsing for Request Handlers like contact.c).
//
//            When the form is sent to the server, we use the form fields
//            (and the client IP address) to build an email which is sent
//            to your SMTP server. A feedback page is then sent to clients.
// ============================================================================
// imported functions:
//   get_reply(): get a pointer on the 'reply' dynamic buffer from the server
//  xbuf_reset(): (re)initiatize a dynamic buffer object
// xbuf_frfile(): load a file and append its contents to a specified buffer
//   xbuf_repl(): replace a string by another string in a specified buffer
//    xbuf_cat(): like strcat(), but in the specified dynamic buffer 
//   xbuf_ncat(): like strncat(), but in the specified dynamic buffer 
//   xbuf_xcat(): formatted strcat() (a la printf) in a given dynamic buffer 
//   xbuf_free(): release the memory allocated for a dynamic buffer
//     get_arg(): get the specified form field value
//    sendmail(): send mail for relaying to the 'from' mail address' SMTPserver
//   s_asctime(): like asctime(), but thread-safe
//     get_env(): get connection's 'environment' variables from the server
// ----------------------------------------------------------------------------
#include "gwan.h" // G-WAN exported functions

// Title of our HTML page
static char title []="Contact Form";

// Top of our HTML page
static char top[]="<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">"
       "<html lang=\"en\"><head><title>%s</title><meta http-equiv"
       "=\"Content-Type\" content=\"text/html; charset=utf-8\">"
       "<link href=\"imgs/style.css\" rel=\"stylesheet\" type=\"text/css\">"
       "</head><body><h1>%s</h1>";
// ----------------------------------------------------------------------------
// main()
// ----------------------------------------------------------------------------
int main(int argc, char *argv[])
{
   // get a pointer on the server response buffer
   xbuf_t *reply = get_reply(argv);

   xbuf_t f;       // create a dynamic buffer
   xbuf_reset(&f); // initialize buffer

   int   client_port = get_env(argv, REMOTE_PORT, 0);
   char *client_ip   = get_env(argv, REMOTE_ADDR, 0);

   // -------------------------------------------------------------------------
   // no URL parameters, we have to send the initial "Contact Form"
   // -------------------------------------------------------------------------
   if(argc < 2)
   {
      // a template HTML file, with fields that will be replaced by variables 
      char *file = "contact.html", str[1024], tmp[80];

      // open the template HTML file located under ".../www/contact.html"
      char *wwwpath = get_env(argv, WWW_ROOT, 0);    // get the ".../www/" path
      s_snprintf(str, 1023, "%s/%s", wwwpath, file); // build full file path
      xbuf_frfile(&f, str);                          // load file in buffer
      if(f.len)                                      // load succeeded?
      {
         // build the time and IP address strings
         sprintf(str, "Our current time is: %s", s_asctime(0, tmp));
         sprintf(tmp, "Your IP address is: %s",  client_ip);

         xbuf_repl(&f, "<!--time-->", str);   // replace field1 by variable
         xbuf_repl(&f, "<!--ip-->",   tmp);   // replace field2 by variable
         xbuf_ncat(reply, f.ptr, f.len);     // dump file into HTML page
         xbuf_free(&f);                       // free dynamic buffer

         return 200; // return an HTTP code (200:'OK')
      }
      
      return 404; // return an HTTP code (404:'Not found')
   }

   // -------------------------------------------------------------------------
   // if we have URL parameters, we must process a 'POST' Form
   // -------------------------------------------------------------------------

   // the form field "names" we want to find values for 
   char *url="", *address="", *subject="", *text="";

   // build the top of our HTML page
   xbuf_xcat(reply, top, title, title);

   // get the form field values (note the ending '=' argument delimiter)
   get_arg("url=",     &url,     argc, argv);
   get_arg("address=", &address, argc, argv);
   get_arg("subject=", &subject, argc, argv);
   get_arg("text=",    &text,    argc, argv);
  
   // insert useful information like which language was used (the 'url' arg)
   // and which IP address the client has used to send this email
   xbuf_xcat(&f, "From:%s in '%s'\n---\n%s", client_ip, url, text);

   // send the form data to your mail server (nobody will spam you if
   // this address is known only by this program and your mail server)
   //sendmail("smtp.example.com", address, "from_a2583df4@home.com", 
   //         subject, f.ptr);
   xbuf_free(&f);

   // send feedback to your correspondant and close the HTML page
   xbuf_cat(reply, "<p>Thank you!<br></p></body></html>");

   return 200; // return an HTTP code (200:'OK')
}
// ============================================================================
// End of Source Code
// ============================================================================