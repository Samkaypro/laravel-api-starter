# Security Policy

## Supported Versions

Currently, these versions of Laravel API Starter are supported with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.x.x   | :white_check_mark: |

## Reporting a Vulnerability

The Laravel API Starter team takes security issues seriously. We appreciate your efforts to responsibly disclose your findings.

To report a security vulnerability, please follow these steps:

1. **DO NOT** create a public GitHub issue for security vulnerabilities
2. Email your findings to [samkaypro@gmail.com] 
3. Include as much information as possible:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix if available

We will acknowledge receipt of your report within 48 hours and provide an estimated timeframe for a fix.

## Security Best Practices

When deploying this application, please follow these security best practices:

1. **Environment Configuration**:
   - Keep your `.env` file secure and never commit it to version control
   - Use strong, unique keys for `APP_KEY` and other sensitive values
   - Configure proper `APP_ENV` and `APP_DEBUG` settings in production

2. **Database Security**:
   - Use strong, unique passwords for database access
   - Restrict database access to authorized networks only
   - Regularly backup your database

3. **API Authentication**:
   - Use HTTPS for all API endpoints
   - Implement proper token expiration and rotation
   - Follow the principle of least privilege for API tokens

4. **User Authentication**:
   - Enforce strong password policies
   - Implement rate limiting for authentication attempts
   - Consider implementing two-factor authentication for admin accounts

5. **Regular Updates**:
   - Keep Laravel and all dependencies up to date
   - Monitor security advisories for Laravel and related packages
   - Apply security patches promptly

6. **File Uploads**:
   - Validate all file uploads (type, size, content)
   - Store uploaded files outside the web root
   - Scan uploads for malware when appropriate

7. **CORS Configuration**:
   - Configure restrictive CORS policies
   - Only allow necessary origins, methods, and headers

## Security Features

The Laravel API Starter includes several security features:

- Sanctum token-based authentication
- Rate limiting on authentication endpoints
- Form request validation
- CSRF protection
- Role-based access control
- Secure password storage with bcrypt
- Support for password reset and email verification

## Vulnerability Disclosure Policy

- **Disclosure Timeline**: We aim to release patches for reported vulnerabilities within 30 days
- **Acknowledgment**: Contributors who report valid security issues will be acknowledged (with permission)
- **Public Disclosure**: Vulnerabilities will be publicly disclosed after a patch is released 