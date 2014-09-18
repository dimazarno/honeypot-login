Honeypot Login
==============

Hackers usually trying to gain privileges via a website login page, it need a little trick to handle it, move your login page with directory name that is not common, example: /admin_is_g00d or /n0_4dm1n_h3r3, then create another directory with a common name such as /admin or /login which containing a honeypot login. 

Honeypot login is a PHP page that have ability to record malicious activity on login area, the log contain :  

- Timestamp 
- Username 
- Password 
- IP 
- Hostname 
- City 
- Region 
- Country 
- Location (GPS coordinate) 
- ISP 
- Browser 
- OS 
- Useragent 
- Cookies 
- Session  

Honeypot Login also equipped with:  
- Simple captcha to avoid brute force. 
- Responsive design using the Bootstrap. 
- Integrated with ipinfo.org API, up to 1000 requests per day! if you need beyond that you can contact ipinfo.org for further info. 
- 100% safe, not connected to any database (to avoid another attack)

More Information : [dimazarno.blogspot.com](http://dimazarno.blogspot.com/)