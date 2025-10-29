# Website Stress Test Scripts

This collection provides simple stress testing tools for your BPSMS (Bike Parts Sales Management System) website.

## 📁 Available Scripts

### 1. Python Script (`stress_test.py`) - **Recommended**
- **Features**: Most comprehensive, async/await support, detailed reporting
- **Requirements**: `pip install aiohttp`
- **Best for**: Detailed analysis and professional testing

### 2. Bash Script (`stress_test.sh`)
- **Features**: Simple, no external dependencies (except curl and parallel)
- **Requirements**: `curl`, `parallel`, `bc`
- **Best for**: Quick testing and server environments

### 3. Node.js Script (`stress_test.js`)
- **Features**: JavaScript-based, good for web developers
- **Requirements**: `npm install axios`
- **Best for**: JavaScript developers

## 🚀 Quick Start

### Python Version (Recommended)
```bash
# Install requirements
pip install aiohttp

# Basic test
python stress_test.py http://localhost/bpsms

# Advanced test
python stress_test.py http://localhost/bpsms -c 20 -d 60 -o results.json
```

### Bash Version
```bash
# Make executable
chmod +x stress_test.sh

# Basic test
./stress_test.sh http://localhost/bpsms

# Advanced test
./stress_test.sh http://localhost/bpsms 20 60
```

### Node.js Version
```bash
# Install requirements
npm install axios

# Basic test
node stress_test.js http://localhost/bpsms

# Advanced test
node stress_test.js http://localhost/bpsms 20 60
```

## 📊 What Gets Tested

The scripts test these key pages:
- **Home page** (`/`)
- **Products page** (`/?p=products`)
- **Services page** (`/?p=services`)
- **About page** (`/?p=about`)
- **Cart page** (`/cart.php`)
- **Login page** (`/login.php`)
- **Register page** (`/register.php`)
- **Search functionality** (`/?p=products&search=motorcycle`)

## 📈 Understanding the Results

### Key Metrics
- **Total Requests**: Number of requests made
- **Success Rate**: Percentage of successful responses (200-399 status codes)
- **Response Time**: Average, fastest, and slowest response times
- **Requests/Second**: Throughput measurement
- **Error Analysis**: Breakdown of failed requests

### Performance Benchmarks
- **Good Performance**: 
  - Average response time < 1000ms
  - Success rate > 95%
  - Requests/second > 10
- **Needs Attention**:
  - Average response time > 2000ms
  - Success rate < 90%
  - Requests/second < 5

## 🔧 Command Line Options

### Python Script
```bash
python stress_test.py [URL] [OPTIONS]

Options:
  -c, --concurrent CONCURRENT    Maximum concurrent requests (default: 10)
  -d, --duration DURATION        Test duration in seconds (default: 30)
  -o, --output OUTPUT           Output file for detailed results
  --quiet                       Suppress progress output
```

### Bash Script
```bash
./stress_test.sh [URL] [CONCURRENT] [DURATION]

Parameters:
  URL         Base URL of the website (default: http://localhost/bpsms)
  CONCURRENT  Maximum concurrent requests (default: 5)
  DURATION    Test duration in seconds (default: 30)
```

### Node.js Script
```bash
node stress_test.js [URL] [CONCURRENT] [DURATION]

Parameters:
  URL         Base URL of the website (default: http://localhost/bpsms)
  CONCURRENT  Maximum concurrent requests (default: 10)
  DURATION    Test duration in seconds (default: 30)
```

## 📄 Output Files

The scripts generate several output files:

### Python & Node.js
- `stress_test_results_YYYYMMDD_HHMMSS.json` - Detailed results in JSON format

### Bash
- `stress_test_results_YYYYMMDD_HHMMSS.txt` - Raw request data
- `stress_test_summary_YYYYMMDD_HHMMSS.txt` - Human-readable summary

## 🎯 Example Usage Scenarios

### 1. Quick Performance Check
```bash
# Test for 30 seconds with 5 concurrent requests
python stress_test.py http://localhost/bpsms -c 5 -d 30
```

### 2. Load Testing
```bash
# Heavy load test for 2 minutes with 20 concurrent requests
python stress_test.py http://localhost/bpsms -c 20 -d 120
```

### 3. Production Testing
```bash
# Test production server (be careful!)
python stress_test.py https://yourdomain.com -c 10 -d 60
```

## ⚠️ Important Notes

### Before Running Tests
1. **Get Permission**: Always get permission before testing production servers
2. **Start Small**: Begin with low concurrent requests and short duration
3. **Monitor Resources**: Watch your server's CPU, memory, and database connections
4. **Test Locally First**: Always test on local/development environment first

### Safety Guidelines
- **Don't overload production servers** without permission
- **Start with small numbers** (5-10 concurrent requests)
- **Monitor your server** during testing
- **Stop immediately** if you notice server issues

## 🔍 Troubleshooting

### Common Issues

#### "Connection refused" or "Timeout"
- Check if the website is running
- Verify the URL is correct
- Check firewall settings

#### "High failure rate"
- Server might be overloaded
- Database connection issues
- Check server error logs

#### "Very slow response times"
- Database queries might be slow
- Server resources might be insufficient
- Check for blocking operations

### Getting Help
1. Check server error logs
2. Monitor server resources (CPU, memory, disk)
3. Check database performance
4. Verify network connectivity

## 📊 Sample Output

```
🚀 Starting stress test on http://localhost/bpsms
📊 Testing 8 pages with 10 concurrent requests
⏱️  Duration: 30 seconds
────────────────────────────────────────────────────────────
⏳ Waiting for requests to complete...

============================================================
📊 STRESS TEST REPORT
============================================================
🌐 Target URL: http://localhost/bpsms
⏱️  Test Duration: 30.45 seconds
📈 Total Requests: 240
✅ Successful: 238 (99.2%)
❌ Failed: 2 (0.8%)
🚀 Requests/Second: 7.89

📊 RESPONSE TIME STATISTICS
────────────────────────────────────────────────────────
⚡ Average: 1250.50ms
🏃 Fastest: 450.20ms
🐌 Slowest: 3200.80ms

📄 PAGE-BY-PAGE RESULTS
────────────────────────────────────────────────────────
📄 /
   Requests: 30 | Success: 30 (100.0%) | Avg Time: 1200.50ms
📄 /?p=products
   Requests: 30 | Success: 29 (96.7%) | Avg Time: 1350.20ms
...

💡 PERFORMANCE RECOMMENDATIONS
────────────────────────────────────────────────────────
✅ Performance looks good! Your website is handling the load well.

💾 Detailed results saved to: stress_test_results_20250101_120000.json
```

## 🎉 Conclusion

These stress test scripts will help you:
- **Identify performance bottlenecks** before they become problems
- **Test your website's stability** under load
- **Optimize your server configuration** based on real data
- **Ensure your website can handle** expected traffic

Remember to test responsibly and always get permission before testing production servers!
