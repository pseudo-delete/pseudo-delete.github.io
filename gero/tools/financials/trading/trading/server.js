// OKX Trading API Backend Server
// Run with: node server.js

const express = require('express');
const cors = require('cors');
const crypto = require('crypto');
const axios = require('axios');
const https = require('https');

const app = express();
const PORT = 3001;

// Create HTTPS agent that accepts self-signed certificates
// This is needed for some corporate/network environments
const httpsAgent = new https.Agent({
    rejectUnauthorized: false // Allow self-signed certificates
});

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static('public'));

// OKX API Configuration
const OKX_REST_API = 'https://www.okx.com';

// Generate OKX API signature
function generateSignature(timestamp, method, requestPath, body, secretKey) {
    const prehash = timestamp + method + requestPath + (body || '');
    return crypto.createHmac('sha256', secretKey).update(prehash).digest('base64');
}

// Proxy endpoint for OKX API requests (authenticated)
app.all('/api/okx/*', async (req, res) => {
    try {
        const apiKey = req.headers['okx-api-key'];
        const secretKey = req.headers['okx-secret-key'];
        const passphrase = req.headers['okx-passphrase'];

        if (!apiKey || !secretKey || !passphrase) {
            console.log('❌ Missing API credentials');
            return res.status(400).json({ 
                code: '50000',
                msg: 'Missing API credentials in headers' 
            });
        }

        // Extract the actual OKX API path
        const okxPath = req.path.replace('/api/okx', '');
        const timestamp = new Date().toISOString();
        const method = req.method;
        const body = Object.keys(req.body).length > 0 ? JSON.stringify(req.body) : '';
        
        // Build query string for GET requests
        const queryString = Object.keys(req.query).length > 0 
            ? '?' + new URLSearchParams(req.query).toString() 
            : '';
        
        const requestPath = okxPath + queryString;
        const signature = generateSignature(timestamp, method, requestPath, body, secretKey);

        console.log(`📡 [${method}] ${requestPath}`);

        // Make request to OKX API
        const response = await axios({
            method: method,
            url: OKX_REST_API + requestPath,
            headers: {
                'OK-ACCESS-KEY': apiKey,
                'OK-ACCESS-SIGN': signature,
                'OK-ACCESS-TIMESTAMP': timestamp,
                'OK-ACCESS-PASSPHRASE': passphrase,
                'Content-Type': 'application/json'
            },
            data: Object.keys(req.body).length > 0 ? req.body : undefined,
            timeout: 15000,
            httpsAgent: httpsAgent,
            validateStatus: function (status) {
                return status < 500; // Resolve only if status < 500
            }
        });

        console.log(`✅ Response code: ${response.data.code || 'N/A'}`);
        res.json(response.data);
    } catch (error) {
        console.error('❌ OKX API Error:', {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status
        });
        
        if (error.response) {
            res.status(error.response.status).json(error.response.data);
        } else if (error.code === 'ECONNABORTED') {
            res.status(408).json({
                code: '50000',
                msg: 'Request timeout'
            });
        } else {
            res.status(500).json({
                code: '50000',
                msg: error.message
            });
        }
    }
});

// Public market data endpoints (no authentication required)
app.get('/api/public/ticker', async (req, res) => {
    try {
        const { instId } = req.query;
        
        if (!instId) {
            return res.status(400).json({
                code: '51000',
                msg: 'instId parameter is required'
            });
        }
        
        console.log(`📊 Fetching ticker for: ${instId}`);
        
        const response = await axios.get(`${OKX_REST_API}/api/v5/market/ticker`, {
            params: { instId },
            headers: {
                'Content-Type': 'application/json',
                'User-Agent': 'Mozilla/5.0'
            },
            timeout: 10000,
            httpsAgent: httpsAgent
        });
        
        console.log(`✅ Ticker fetched successfully`);
        res.json(response.data);
    } catch (error) {
        console.error('❌ Ticker error:', error.response?.data || error.message);
        res.status(error.response?.status || 500).json({ 
            code: '50000',
            msg: error.message,
            details: error.response?.data 
        });
    }
});

app.get('/api/public/candles', async (req, res) => {
    try {
        const { instId, bar, limit } = req.query;
        
        if (!instId) {
            return res.status(400).json({
                code: '51000',
                msg: 'instId parameter is required'
            });
        }
        
        console.log(`📈 Fetching candles for: ${instId} (bar: ${bar || '1H'}, limit: ${limit || '24'})`);
        
        const response = await axios.get(`${OKX_REST_API}/api/v5/market/candles`, {
            params: { 
                instId, 
                bar: bar || '1H', 
                limit: limit || '24'
            },
            headers: {
                'Content-Type': 'application/json',
                'User-Agent': 'Mozilla/5.0'
            },
            timeout: 10000,
            httpsAgent: httpsAgent
        });
        
        console.log(`✅ Candles fetched successfully (${response.data.data?.length || 0} candles)`);
        res.json(response.data);
    } catch (error) {
        console.error('❌ Candles error:', error.response?.data || error.message);
        res.status(error.response?.status || 500).json({ 
            code: '50000',
            msg: error.message,
            details: error.response?.data 
        });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({ 
        status: 'OK', 
        timestamp: new Date().toISOString(),
        message: 'OKX Trading API Server is running'
    });
});

// Catch-all for undefined routes
app.use((req, res) => {
    res.status(404).json({
        code: '404',
        msg: 'Endpoint not found'
    });
});

// Error handler
app.use((err, req, res, next) => {
    console.error('Server error:', err);
    res.status(500).json({
        code: '50000',
        msg: 'Internal server error'
    });
});

app.listen(PORT, () => {
    console.log('═══════════════════════════════════════════════');
    console.log('🚀 OKX Trading API Server');
    console.log('═══════════════════════════════════════════════');
    console.log(`📍 Server: http://localhost:${PORT}`);
    console.log(`📊 Dashboard: http://localhost:${PORT}`);
    console.log(`❤️  Health: http://localhost:${PORT}/health`);
    console.log('═══════════════════════════════════════════════');
    console.log('✨ Server is ready to accept requests');
    console.log('');
});
