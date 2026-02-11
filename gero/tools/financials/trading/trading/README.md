# OKX Trading Dashboard

A professional, full-featured cryptocurrency trading dashboard for OKX exchange with real-time data, charts, and order execution.

## 🚀 Features

- **Real-time Price Charts** - 24-hour candlestick data with interactive Chart.js visualization
- **Account Management** - View balances, positions, and P&L in real-time
- **Order Execution** - Place market and limit orders directly from the dashboard
- **Trading History** - Complete order history with status tracking
- **Multi-Pair Support** - Trade BTC, ETH, SOL, XRP, BNB and more
- **Secure API Integration** - HMAC SHA256 authentication with backend proxy
- **Auto-Refresh** - Data updates every 10 seconds automatically
- **Responsive Design** - Works on desktop, tablet, and mobile devices

## 📋 Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- OKX account with API credentials

## 🔧 Installation

1. **Extract all files to a folder**

2. **Install dependencies:**
```bash
npm install
```

3. **Start the backend server:**
```bash
npm start
```

The server will start on `http://localhost:3001`

4. **Open your browser and navigate to:**
```
http://localhost:3001
```

## 🔑 OKX API Setup

1. Log in to your OKX account
2. Go to **Settings** → **API** → **Create API Key**
3. Set permissions:
   - ✅ Read (required)
   - ✅ Trade (required for placing orders)
   - ❌ Withdraw (NOT recommended for security)
4. Save your:
   - API Key
   - Secret Key
   - Passphrase

⚠️ **Important:** Keep your API credentials secure and never share them!

## 💻 Using the Dashboard

1. **Connect API:**
   - Click the settings icon (⚙️) in the top right
   - Enter your OKX API credentials
   - Click "Connect to OKX"

2. **View Data:**
   - Account balance updates automatically
   - Current prices shown in real-time
   - Interactive price charts for 24-hour data

3. **Place Orders:**
   - Select trading pair (BTC-USDT, ETH-USDT, etc.)
   - Choose order type (Market or Limit)
   - Enter price (for limit orders) and amount
   - Click Buy or Sell

4. **Monitor Trades:**
   - View open positions with unrealized P&L
   - Check order history in the table
   - All data refreshes every 10 seconds

## 🏗️ Project Structure

```
okx-trading-dashboard/
├── server.js           # Backend API proxy server
├── package.json        # Node.js dependencies
├── public/
│   └── index.html     # Frontend dashboard
└── README.md          # This file
```

## 🔒 Security Notes

- API credentials are stored locally in your browser's localStorage
- All API requests go through your own backend server (localhost:3001)
- The backend proxies requests to OKX with proper HMAC authentication
- Never commit your API keys to version control
- Use IP whitelisting on OKX for additional security

## 🛠️ Technical Stack

**Frontend:**
- React 18
- Chart.js for price charts
- Custom CSS with CSS Grid layout
- JetBrains Mono & Syne fonts

**Backend:**
- Express.js
- Axios for HTTP requests
- Node.js Crypto for HMAC signatures
- CORS enabled for local development

**API:**
- OKX REST API v5
- HMAC SHA256 authentication
- Public & private endpoints

## 📊 Supported Trading Pairs

- BTC-USDT
- ETH-USDT
- SOL-USDT
- XRP-USDT
- BNB-USDT

You can easily add more pairs by editing the `<select>` dropdown in `index.html`.

## 🐛 Troubleshooting

**"Connection failed" error:**
- Check that the backend server is running on port 3001
- Verify your API credentials are correct
- Ensure your OKX API key has "Read" and "Trade" permissions

**"CORS error":**
- The backend server should handle CORS automatically
- Make sure you're accessing via `http://localhost:3001` not `file://`

**Chart not displaying:**
- Check browser console for errors
- Ensure Chart.js is loading from CDN
- Try refreshing the page

**Orders not executing:**
- Verify you have sufficient balance
- Check that trading is enabled on your API key
- Ensure the trading pair is correct

## 📝 Development

To run in development mode with auto-restart:

```bash
npm install -g nodemon
npm run dev
```

## ⚠️ Disclaimer

This software is for educational purposes only. Trading cryptocurrencies carries risk. Always:
- Test with small amounts first
- Never trade more than you can afford to lose
- Use proper risk management
- Keep your API keys secure
- Monitor your positions regularly

The developers are not responsible for any financial losses incurred while using this software.

## 📄 License

MIT License - feel free to modify and use as needed.

## 🙏 Credits

- OKX for their comprehensive trading API
- Chart.js for visualization
- React team for the framework
