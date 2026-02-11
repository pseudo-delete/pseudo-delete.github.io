// OKX Trading API Backend Server
// Run with: node server.js

const express = require('express');
const cors = require('cors');
const crypto = require('crypto');
const axios = require('axios');

const app = express();
const PORT = 3001;

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

// Proxy endpoint for OKX API requests
app.all('/api/okx/*', async (req, res) => {
    try {
        const apiKey = req.headers['okx-api-key'];
        const secretKey = req.headers['okx-secret-key'];
        const passphrase = req.headers['okx-passphrase'];

        if (!apiKey || !secretKey || !passphrase) {
            return res.status(400).json({ 
                error: 'Missing API credentials in headers' 
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
            data: Object.keys(req.body).length > 0 ? req.body : undefined
        });

        res.json(response.data);
    } catch (error) {
        console.error('OKX API Error:', error.response?.data || error.message);
        res.status(error.response?.status || 500).json({
            error: error.response?.data || error.message
        });
    }
});

// Public market data endpoints (no authentication required)
app.get('/api/public/ticker', async (req, res) => {
    try {
        const { instId } = req.query;
        const response = await axios.get(`${OKX_REST_API}/api/v5/market/ticker`, {
            params: { instId }
        });
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.get('/api/public/candles', async (req, res) => {
    try {
        const { instId, bar, limit } = req.query;
        const response = await axios.get(`${OKX_REST_API}/api/v5/market/candles`, {
            params: { instId, bar: bar || '1H', limit: limit || 24 }
        });
        res.json(response.data);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

// Health check endpoint
app.get('/health', (req, res) => {
    res.json({ status: 'OK', timestamp: new Date().toISOString() });
});

app.listen(PORT, () => {
    console.log(`🚀 OKX Trading API Server running on http://localhost:${PORT}`);
    console.log(`📊 Dashboard available at http://localhost:${PORT}`);
});
