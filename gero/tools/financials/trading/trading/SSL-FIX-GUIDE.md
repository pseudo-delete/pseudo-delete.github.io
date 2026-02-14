# SSL Certificate Error - Fix Guide

## The Problem

You're seeing this error:
```
❌ Ticker error: unable to verify the first certificate
❌ Candles error: unable to verify the first certificate
```

This happens when Node.js can't verify OKX's SSL certificate. Common causes:
- Corporate firewall/proxy
- Antivirus software intercepting HTTPS
- Outdated Node.js CA certificates
- Network configuration issues

## ✅ Quick Fix (Recommended)

### Option 1: Use the Updated server.js

I've already updated `server.js` to handle SSL issues. Just restart your server:

1. **Stop the current server** (Ctrl+C)
2. **Restart with:**
   ```bash
   npm start
   ```

The updated server automatically handles SSL certificate issues.

### Option 2: Run with SSL Disabled (If Option 1 doesn't work)

**Windows (PowerShell):**
```powershell
$env:NODE_TLS_REJECT_UNAUTHORIZED="0"; npm start
```

**Windows (Command Prompt):**
```cmd
set NODE_TLS_REJECT_UNAUTHORIZED=0&& npm start
```

**Mac/Linux:**
```bash
NODE_TLS_REJECT_UNAUTHORIZED=0 npm start
```

Or use the npm script:
```bash
# Windows
npm run start:no-ssl

# Mac/Linux  
npm run start:no-ssl-unix
```

## 🔍 Verify It's Working

After restarting, you should see:
```
📊 Fetching ticker for: BTC-USDT
✅ Ticker fetched successfully
📈 Fetching candles for: BTC-USDT
✅ Candles fetched successfully (24 candles)
```

Then refresh your browser (http://localhost:3001) and the dashboard should load with data.

## 🛡️ Is This Safe?

**For local development: YES**
- The server only runs on your computer (localhost)
- You're connecting to OKX's official domain (www.okx.com)
- No third parties involved

**Why it's needed:**
- Some corporate networks use SSL inspection
- Antivirus software intercepts HTTPS connections
- This adds their own certificate, which Node.js doesn't trust by default

## 🔧 Long-Term Solutions

### Solution 1: Update Node.js CA Certificates

```bash
npm install -g node
```

This updates Node.js and its CA certificate bundle.

### Solution 2: Use System CA Certificates

**Mac/Linux:**
```bash
export NODE_EXTRA_CA_CERTS=/etc/ssl/certs/ca-certificates.crt
npm start
```

**Windows:**
Download and install the latest Node.js from https://nodejs.org/

### Solution 3: Corporate Proxy Settings

If you're on a corporate network, ask your IT department for:
- Proxy server address
- CA certificate file

Then run:
```bash
export NODE_EXTRA_CA_CERTS=/path/to/corporate-ca.crt
export HTTP_PROXY=http://proxy.company.com:8080
export HTTPS_PROXY=http://proxy.company.com:8080
npm start
```

## 📋 Troubleshooting Steps

### Step 1: Check Network Connection
```bash
ping www.okx.com
```

Should respond with times (not timeout).

### Step 2: Test HTTPS Direct
Open browser: https://www.okx.com

If this loads fine but Node.js fails, it's a Node.js certificate issue.

### Step 3: Check Antivirus
Temporarily disable antivirus SSL scanning and try again.

Common antivirus with SSL inspection:
- Kaspersky
- Avast
- AVG
- Norton
- McAfee

### Step 4: Check Firewall
Make sure your firewall allows Node.js to make HTTPS connections.

### Step 5: Try Different Network
Connect to a different WiFi or use mobile hotspot to rule out network issues.

## 🔐 Production Deployment

**If deploying to a server:**

1. **Use proper SSL certificates:**
```javascript
const fs = require('fs');
const https = require('https');

const httpsAgent = new https.Agent({
    ca: fs.readFileSync('/path/to/ca-bundle.crt')
});
```

2. **Enable strict SSL:**
```bash
STRICT_SSL=true node server.js
```

3. **Use environment variables:**
Create `.env` file:
```
NODE_ENV=production
STRICT_SSL=true
PORT=3001
```

## ❓ Still Having Issues?

### Error: "ECONNREFUSED"
- Server is not running
- Wrong port number
- Firewall blocking connection

### Error: "ETIMEDOUT"
- Network/internet issue
- OKX servers might be down
- Firewall/proxy blocking

### Error: "CERT_HAS_EXPIRED"
- OKX certificate expired (unlikely)
- Your system clock is wrong
- Check: `date` (Mac/Linux) or `echo %date% %time%` (Windows)

### Error: "ENOTFOUND www.okx.com"
- DNS issue
- Internet connection problem
- Try: `nslookup www.okx.com`

## 💡 Quick Test Commands

Test if OKX is accessible:
```bash
# Test 1: Ping
ping www.okx.com

# Test 2: CURL (if installed)
curl -I https://www.okx.com

# Test 3: Node.js test
node -e "require('https').get('https://www.okx.com', r => console.log('OK', r.statusCode))"
```

## 📞 Getting More Help

If none of these work:

1. **Check your setup:**
   - Node.js version: `node --version` (should be v16+)
   - NPM version: `npm --version`
   - Operating system

2. **Check the full error:**
   - Look at the complete error message in terminal
   - Copy the entire stack trace

3. **Try the alternative server:**
   ```bash
   node server-alternative.js
   ```
   This version has even more detailed error messages.

## ✅ Success Checklist

- [ ] Server starts without errors
- [ ] Browser opens http://localhost:3001
- [ ] Dashboard loads (no blank page)
- [ ] Terminal shows: ✅ Ticker fetched successfully
- [ ] Terminal shows: ✅ Candles fetched successfully
- [ ] Price chart displays on dashboard
- [ ] Current price shows a number (not --)

If all checked ✅, you're good to go! 🚀
