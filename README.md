# contactform
A basic PHP-based web contact form that uses JQuery/AJAX for a pretty, seamless experience

# Motivation
I felt like creating a simple, no-frills contact form for my website. I don't have much experience with JQuery or Javascript in general, so I figured this would make decent practice.

# Installation
Just place the files from this repo where you want them, and edit some of the content in send.php (CHANGEME section) to customize (e.g., specify send address and subject). Pretty straightforward.

# Dependencies
-PHP (Duh)  
-PHP mbstring module  
-If using SELinux, ensure sending of mail from httpd is allowed - e.g., setsebool -P httpd_can_sendmail 1  
-Make sure the mail relay specified in your php.ini is configured properly and check that your firewall isn't blocking the necessary port(s)

# Change History
v1.1
  "Prettied up" success/error message for our non-Javascript friends by leveraging GET requests to pass responses back to our form
  
v1.0
  Initial release
